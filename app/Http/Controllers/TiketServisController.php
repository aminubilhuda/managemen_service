<?php

namespace App\Http\Controllers;

use App\Models\DokumentasiUnit;
use App\Models\Pelanggan;
use App\Models\Perusahaan;
use App\Models\RiwayatStatusTiket;
use App\Models\TiketServis;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TiketServisController extends Controller
{
    public function index(Request $request)
    {
        $query = TiketServis::with(['pelanggan', 'teknisi', 'invoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                    ->orWhere('perangkat', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', fn ($p) => $p->where('nama_pelanggan', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teknisi_id')) {
            $query->where('teknisi_id', $request->teknisi_id);
        }

        $tiket = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $teknisiList = User::role('teknisi')->get();
        $statusList = TiketServis::STATUSES;

        return view('tiket-servis.index', compact('tiket', 'teknisiList', 'statusList'));
    }

    public function create()
    {
        $pelangganList = Pelanggan::orderBy('nama_pelanggan')->get();
        $teknisiList = User::role('teknisi')->get();

        return view('tiket-servis.create', compact('pelangganList', 'teknisiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'teknisi_id' => 'nullable|exists:users,id',
            'perangkat' => 'required|string|max:255',
            'imei_sn' => 'nullable|string|max:255',
            'kelengkapan' => 'nullable|string|max:255',
            'keluhan' => 'required|string',
            'kondisi_awal' => 'nullable|string',
            'estimasi_biaya' => 'nullable|numeric|min:0',
            'estimasi_selesai' => 'nullable|date',
            'foto_sebelum.*' => 'nullable|image|max:5120',
        ]);

        $validated['no_tiket'] = TiketServis::generateNoTiket();
        $validated['status'] = 'diterima';

        $tiket = TiketServis::create($validated);

        // Catat riwayat status awal
        RiwayatStatusTiket::create([
            'tiket_id' => $tiket->id,
            'status_sebelum' => null,
            'status_sesudah' => 'diterima',
            'catatan' => 'Unit diterima',
            'diubah_oleh' => Auth::id(),
        ]);

        // Upload foto dokumentasi
        if ($request->hasFile('foto_sebelum')) {
            foreach ($request->file('foto_sebelum') as $foto) {
                $path = $foto->store('dokumentasi/'.$tiket->id, 'public');
                DokumentasiUnit::create([
                    'tiket_id' => $tiket->id,
                    'tipe_dokumentasi' => 'sebelum',
                    'file_path' => $path,
                    'diupload_oleh' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('tiket-servis.show', $tiket)->with('success', 'Tiket servis berhasil dibuat: '.$tiket->no_tiket);
    }

    public function show(TiketServis $tiketServis)
    {
        $tiketServis->load([
            'pelanggan',
            'teknisi',
            'riwayatStatus.user',
            'dokumentasi.user',
            'invoice.pembayaran',
        ]);

        $teknisiList = User::role('teknisi')->get();
        $statusList = TiketServis::STATUSES;

        return view('tiket-servis.show', compact('tiketServis', 'teknisiList', 'statusList'));
    }

    public function edit(TiketServis $tiketServis)
    {
        $pelangganList = Pelanggan::orderBy('nama_pelanggan')->get();
        $teknisiList = User::role('teknisi')->get();

        return view('tiket-servis.edit', compact('tiketServis', 'pelangganList', 'teknisiList'));
    }

    public function update(Request $request, TiketServis $tiketServis)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'teknisi_id' => 'nullable|exists:users,id',
            'perangkat' => 'required|string|max:255',
            'imei_sn' => 'nullable|string|max:255',
            'kelengkapan' => 'nullable|string|max:255',
            'keluhan' => 'required|string',
            'kondisi_awal' => 'nullable|string',
            'estimasi_biaya' => 'nullable|numeric|min:0',
            'estimasi_selesai' => 'nullable|date',
        ]);

        $tiketServis->update($validated);

        return redirect()->route('tiket-servis.show', $tiketServis)->with('success', 'Tiket servis berhasil diperbarui.');
    }

    public function updateStatus(Request $request, TiketServis $tiketServis)
    {
        $validated = $request->validate([
            'status' => 'required|in:diterima,dicek,menunggu_sparepart,dikerjakan,selesai,diambil,batal',
            'catatan' => 'nullable|string',
        ]);

        $statusLama = $tiketServis->status;

        $tiketServis->update(['status' => $validated['status']]);

        // Catat riwayat
        RiwayatStatusTiket::create([
            'tiket_id' => $tiketServis->id,
            'status_sebelum' => $statusLama,
            'status_sesudah' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'diubah_oleh' => Auth::id() ?? 1,
        ]);

        // Set garansi jika selesai
        if ($validated['status'] === 'selesai' && ! $tiketServis->garansi_sampai) {
            $tiketServis->update(['garansi_sampai' => now()->addDays(30)]);
        }

        $label = TiketServis::STATUSES[$validated['status']] ?? $validated['status'];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $validated['status'],
                'label' => $label,
                'message' => 'Status tiket berhasil diubah menjadi: '.$label,
            ]);
        }

        return back()->with('success', 'Status tiket berhasil diubah menjadi: '.$label);
    }

    public function uploadDokumentasi(Request $request, TiketServis $tiketServis)
    {
        $request->validate([
            'tipe_dokumentasi' => 'nullable|in:sebelum,sesudah,proses',
            'foto' => 'required',
            'foto.*' => 'image|max:10240',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'foto.required' => 'Pilih setidaknya satu file foto untuk diupload.',
            'foto.*.image' => 'File harus berupa gambar.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 10MB.',
        ]);

        $tipe = $request->input('tipe_dokumentasi', 'sebelum');
        $files = $request->file('foto');
        if (! is_array($files)) {
            $files = [$files];
        }

        $count = 0;
        foreach ($files as $foto) {
            if ($foto && $foto->isValid()) {
                $path = $foto->store('dokumentasi/'.$tiketServis->id, 'public');
                DokumentasiUnit::create([
                    'tiket_id' => $tiketServis->id,
                    'tipe_dokumentasi' => $tipe,
                    'file_path' => $path,
                    'keterangan' => $request->keterangan,
                    'diupload_oleh' => Auth::id() ?? 1,
                ]);
                $count++;
            }
        }

        return back()->with('success', "Berhasil mengunggah {$count} foto dokumentasi.");
    }

    public function hapusDokumentasi(DokumentasiUnit $dokumentasi)
    {
        if ($dokumentasi->file_path && Storage::disk('public')->exists($dokumentasi->file_path)) {
            Storage::disk('public')->delete($dokumentasi->file_path);
        }

        $dokumentasi->delete();

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }

    public function destroy(TiketServis $tiketServis)
    {
        // Hapus file dokumentasi
        foreach ($tiketServis->dokumentasi as $dok) {
            Storage::disk('public')->delete($dok->file_path);
        }

        $tiketServis->delete();

        return redirect()->route('tiket-servis.index')->with('success', 'Tiket servis berhasil dihapus.');
    }

    public function cetakTandaTerima(TiketServis $tiketServis)
    {
        $tiketServis->load(['pelanggan']);
        $perusahaan = Perusahaan::first();

        $pdf = Pdf::loadView('tiket-servis.tanda-terima-pdf', compact('tiketServis', 'perusahaan'));
        $pdf->setPaper('A5', 'portrait');

        return $pdf->download("tanda-terima-{$tiketServis->no_tiket}.pdf");
    }
}
