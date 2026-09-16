<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Exports\LabaRugiExport;
use App\Exports\TiketExport;
use App\Models\DetailInvoice;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use App\Models\TiketServis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function invoice(Request $request)
    {
        $query = Invoice::with(['tiket.pelanggan']);

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_invoice', [$request->dari, $request->sampai.' 23:59:59']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->paginate(20)->withQueryString();

        $totalTagihan = $query->sum('total_tagihan');
        $totalDibayar = DB::table('pembayaran')
            ->whereIn('invoice_id', $query->clone()->pluck('invoice.id'))
            ->sum('jumlah_dibayar');

        return view('laporan.invoice', compact('data', 'totalTagihan', 'totalDibayar'));
    }

    public function exportInvoiceExcel(Request $request)
    {
        $query = Invoice::with(['tiket.pelanggan']);

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_invoice', [$request->dari, $request->sampai.' 23:59:59']);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('tanggal_invoice', 'desc')->get();

        return Excel::download(new InvoiceExport($data), 'laporan-invoice-'.date('Y-m-d').'.xlsx');
    }

    public function tiket(Request $request)
    {
        $query = TiketServis::with(['pelanggan', 'teknisi']);

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('created_at', [$request->dari, $request->sampai.' 23:59:59']);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('teknisi_id')) {
            $query->where('teknisi_id', $request->teknisi_id);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('laporan.tiket', compact('data'));
    }

    public function exportTiketExcel(Request $request)
    {
        $query = TiketServis::with(['pelanggan', 'teknisi']);

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('created_at', [$request->dari, $request->sampai.' 23:59:59']);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return Excel::download(new TiketExport($data), 'laporan-tiket-'.date('Y-m-d').'.xlsx');
    }

    public function labaRugi(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        $start = Carbon::parse($bulan)->startOfMonth();
        $end = Carbon::parse($bulan)->endOfMonth();

        // Pendapatan
        $pendapatan = DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
            $q->whereIn('status', ['paid', 'partial'])
                ->whereBetween('tanggal_invoice', [$start, $end]);
        })->sum('jumlah');

        // HPP
        $hpp = DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
            $q->whereIn('status', ['paid', 'partial'])
                ->whereBetween('tanggal_invoice', [$start, $end]);
        })->sum(DB::raw('qty * harga_modal_satuan'));

        $labaKotor = $pendapatan - $hpp;

        // PPN
        $ppn = Invoice::whereIn('status', ['paid', 'partial'])
            ->whereBetween('tanggal_invoice', [$start, $end])
            ->sum('pajak_nominal');

        // Beban Operasional per kategori
        $bebanOperasional = Pengeluaran::whereBetween('tanggal', [$start, $end])
            ->select('kategori_pengeluaran', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori_pengeluaran')
            ->get();

        $totalBeban = $bebanOperasional->sum('total');
        $labaBersih = $labaKotor - $totalBeban;

        // Chart data 12 bulan
        $chartData = $this->getLabaRugiChart($start);

        return view('laporan.laba-rugi', compact(
            'bulan', 'pendapatan', 'hpp', 'labaKotor', 'ppn',
            'bebanOperasional', 'totalBeban', 'labaBersih', 'chartData'
        ));
    }

    private function getLabaRugiChart(Carbon $current): array
    {
        $labels = [];
        $labaData = [];

        for ($i = 11; $i >= 0; $i--) {
            $bulan = $current->copy()->subMonths($i);
            $labels[] = $bulan->translatedFormat('M Y');
            $start = $bulan->copy()->startOfMonth();
            $end = $bulan->copy()->endOfMonth();

            $pendapatan = (float) DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
                $q->whereIn('status', ['paid', 'partial'])
                    ->whereBetween('tanggal_invoice', [$start, $end]);
            })->sum('jumlah');

            $hpp = (float) DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
                $q->whereIn('status', ['paid', 'partial'])
                    ->whereBetween('tanggal_invoice', [$start, $end]);
            })->sum(DB::raw('qty * harga_modal_satuan'));

            $beban = (float) Pengeluaran::whereBetween('tanggal', [$start, $end])->sum('jumlah');

            $labaData[] = $pendapatan - $hpp - $beban;
        }

        return ['labels' => $labels, 'laba' => $labaData];
    }

    public function exportLabaRugiExcel(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));

        return Excel::download(new LabaRugiExport($bulan), 'laporan-laba-rugi-'.$bulan.'.xlsx');
    }
}
