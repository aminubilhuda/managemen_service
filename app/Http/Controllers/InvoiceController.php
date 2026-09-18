<?php

namespace App\Http\Controllers;

use App\Models\DetailInvoice;
use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\PengaturanPajak;
use App\Models\Perusahaan;
use App\Models\Produk;
use App\Models\TiketServis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tiket.pelanggan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_invoice', 'like', "%{$search}%")
                    ->orWhereHas('tiket.pelanggan', fn ($p) => $p->where('nama_pelanggan', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_invoice', [$request->dari, $request->sampai.' 23:59:59']);
        }

        $invoice = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Tiket selesai yang belum dibuatkan invoice
        $tiketSelesaiBelumInvoice = TiketServis::belumInvoice()
            ->with('pelanggan')
            ->latest()
            ->paginate(10);

        return view('invoice.index', compact('invoice', 'tiketSelesaiBelumInvoice'));
    }

    public function create(Request $request)
    {
        $tiketId = $request->query('tiket_id');
        $tiket = $tiketId ? TiketServis::with('pelanggan')->findOrFail($tiketId) : null;

        // Cek apakah tiket sudah punya invoice
        if ($tiket && $tiket->invoice) {
            return redirect()->route('invoice.show', $tiket->invoice)->with('error', 'Tiket ini sudah memiliki invoice.');
        }

        $tiketList = TiketServis::whereDoesntHave('invoice')
            ->whereNotIn('status', ['batal'])
            ->with('pelanggan')
            ->get();

        $produkList = Produk::orderBy('nama_produk')->get();
        $pajakList = PengaturanPajak::where('aktif', true)->get();
        $pajakAktif = PengaturanPajak::tentukanPajakOtomatis(0);

        return view('invoice.create', compact('tiket', 'tiketList', 'produkList', 'pajakAktif', 'pajakList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tiket_id' => 'nullable|exists:tiket_servis,id',
            'diskon' => 'nullable|numeric|min:0',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'pajak_id' => 'nullable|exists:pengaturan_pajak,id',
            'pajak_persen' => 'nullable|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'nullable',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.harga_modal_satuan' => 'nullable|numeric|min:0',
        ]);

        // Cek jika tiket sudah punya invoice aktif
        if (! empty($validated['tiket_id'])) {
            $existing = Invoice::where('tiket_id', $validated['tiket_id'])->where('status', '!=', 'void')->first();
            if ($existing) {
                return back()->withInput()->with('error', 'Tiket ini sudah memiliki invoice aktif ('.$existing->no_invoice.').');
            }
        }

        $perusahaan = Perusahaan::first();
        if (! $perusahaan) {
            $perusahaan = Perusahaan::create([
                'nama_perusahaan' => config('app.name', 'Nama Toko'),
                'alamat' => 'Alamat belum diatur',
                'telp' => '-',
            ]);
        }

        // Hitung estimasi subtotal setelah diskon untuk resolusi pajak otomatis
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ((int) $item['qty']) * ((float) $item['harga_satuan']);
        }
        $diskonNominal = (float) ($validated['diskon'] ?? 0);
        $setelahDiskon = max(0, $subtotal - $diskonNominal);

        // Ambil pajak (manual atau otomatis berdasarkan threshold)
        $pajak = ! empty($validated['pajak_id'])
            ? PengaturanPajak::find($validated['pajak_id'])
            : PengaturanPajak::tentukanPajakOtomatis($setelahDiskon);

        $pajakPersen = isset($validated['pajak_persen'])
            ? (float) $validated['pajak_persen']
            : ($pajak?->persentase ?? 0);

        DB::transaction(function () use ($validated, $perusahaan, $pajak, $pajakPersen, &$invoice) {
            $diskonNominal = (float) ($validated['diskon'] ?? 0);
            $diskonPersen = (float) ($validated['diskon_persen'] ?? 0);

            $invoice = Invoice::create([
                'no_invoice' => Invoice::generateNoInvoice(),
                'tiket_id' => ! empty($validated['tiket_id']) ? $validated['tiket_id'] : null,
                'perusahaan_id' => $perusahaan->id,
                'tanggal_invoice' => now(),
                'diskon_persen' => $diskonPersen,
                'diskon_nominal' => $diskonNominal,
                'pajak_id' => $pajak?->id,
                'pajak_persen' => $pajakPersen,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Buat detail items & potong stok jika sparepart
            foreach ($validated['items'] as $item) {
                $produk = ! empty($item['produk_id']) ? Produk::find($item['produk_id']) : null;
                $qty = (int) $item['qty'];
                $hargaSatuan = (float) $item['harga_satuan'];
                $hargaModal = $produk ? (float) $produk->harga_modal : (float) ($item['harga_modal_satuan'] ?? 0);

                DetailInvoice::create([
                    'invoice_id' => $invoice->id,
                    'produk_id' => $produk?->id,
                    'deskripsi' => $item['deskripsi'],
                    'qty' => $qty,
                    'harga_satuan' => $hargaSatuan,
                    'harga_modal_satuan' => $hargaModal,
                    'jumlah' => $qty * $hargaSatuan,
                ]);

                // Kurangi stok jika produk terdaftar dan bertipe sparepart
                if ($produk && $produk->tipe === 'sparepart') {
                    $produk->decrement('stok', $qty);
                }
            }

            // Hitung total invoice
            $invoice->hitungTotal();
        });

        return redirect()->route('invoice.show', $invoice)->with('success', 'Invoice berhasil diterbitkan: '.$invoice->no_invoice);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'tiket.pelanggan',
            'perusahaan',
            'detail.produk',
            'pembayaran.user',
        ]);

        return view('invoice.show', compact('invoice'));
    }

    public function bayar(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'jumlah_dibayar' => 'required|numeric|min:1',
            'metode_bayar' => 'required|in:tunai,transfer,qris,kartu',
            'bukti_bayar' => 'nullable|file|max:5120',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('bukti-bayar/'.$invoice->id, 'public');
        }

        Pembayaran::create([
            'invoice_id' => $invoice->id,
            'jumlah_dibayar' => $validated['jumlah_dibayar'],
            'metode_bayar' => $validated['metode_bayar'],
            'tanggal_bayar' => now(),
            'dicatat_oleh' => Auth::id(),
            'bukti_bayar' => $buktiPath,
        ]);

        // Update status invoice
        $invoice->refresh();
        $invoice->updateStatusPembayaran();

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:unpaid,partial,paid,void',
            'keterangan' => 'nullable|string|max:500',
            'metode_bayar' => 'nullable|in:tunai,transfer,qris,kartu',
        ]);

        $statusLama = $invoice->status;
        $statusBaru = $validated['status'];

        DB::transaction(function () use ($invoice, $statusLama, $statusBaru, $validated) {
            // Jika status baru adalah void dan sebelumnya belum void, kembalikan stok sparepart
            if ($statusBaru === 'void' && $statusLama !== 'void') {
                foreach ($invoice->detail as $item) {
                    $produk = $item->produk;
                    if ($produk && $produk->tipe === 'sparepart' && $produk->stok !== null) {
                        $produk->increment('stok', $item->qty);
                    }
                }
            }

            // Jika status sebelumnya void dan diubah menjadi aktif kembali, potong stok kembali
            if ($statusLama === 'void' && $statusBaru !== 'void') {
                foreach ($invoice->detail as $item) {
                    $produk = $item->produk;
                    if ($produk && $produk->tipe === 'sparepart' && $produk->stok !== null) {
                        $produk->decrement('stok', $item->qty);
                    }
                }
            }

            // Jika status diubah menjadi paid (lunas), periksa apakah ada sisa tagihan yang perlu dicatat pembayarannya
            if ($statusBaru === 'paid') {
                $totalDibayar = (float) $invoice->pembayaran()->sum('jumlah_dibayar');
                $sisa = max(0, (float) $invoice->total_tagihan - $totalDibayar);

                if ($sisa > 0) {
                    Pembayaran::create([
                        'invoice_id' => $invoice->id,
                        'jumlah_dibayar' => $sisa,
                        'metode_bayar' => $validated['metode_bayar'] ?? 'tunai',
                        'tanggal_bayar' => now(),
                        'dicatat_oleh' => Auth::id(),
                        'bukti_bayar' => null,
                    ]);
                }

                if ($invoice->tiket_id && $invoice->tiket) {
                    $invoice->tiket->update(['biaya_final' => $invoice->total_tagihan]);
                }
            }

            $catatan = $validated['keterangan'] ?? null;
            $keteranganAkhir = $invoice->keterangan;
            if ($catatan) {
                $keteranganAkhir = ($keteranganAkhir ? $keteranganAkhir."\n" : '').'[Status: '.strtoupper($statusBaru).'] '.$catatan;
            }

            $invoice->update([
                'status' => $statusBaru,
                'keterangan' => $keteranganAkhir,
            ]);
        });

        return back()->with('success', 'Status invoice #'.$invoice->no_invoice.' berhasil diperbarui menjadi: '.strtoupper($statusBaru));
    }

    public function void(Request $request, Invoice $invoice)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        $invoice->update([
            'status' => 'void',
            'keterangan' => ($invoice->keterangan ? $invoice->keterangan."\n" : '').'[VOID] '.$request->alasan,
        ]);

        // Kembalikan stok
        foreach ($invoice->detail as $item) {
            $produk = $item->produk;
            if ($produk && $produk->tipe === 'sparepart' && $produk->stok !== null) {
                $produk->increment('stok', $item->qty);
            }
        }

        return back()->with('success', 'Invoice berhasil di-void.');
    }

    public function cetakPdf(Invoice $invoice)
    {
        $invoice->load(['tiket.pelanggan', 'perusahaan', 'detail.produk', 'pembayaran']);

        $pdf = Pdf::loadView('invoice.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("invoice-{$invoice->no_invoice}.pdf");
    }
}
