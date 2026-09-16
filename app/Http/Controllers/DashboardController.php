<?php

namespace App\Http\Controllers;

use App\Models\DetailInvoice;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use App\Models\TiketServis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $bulanIni = Carbon::now()->startOfMonth();
        $bulanIniEnd = Carbon::now()->endOfMonth();

        // Tiket aktif (belum diambil/batal)
        $tiketAktif = TiketServis::whereNotIn('status', ['diambil', 'batal'])->count();

        // Invoice belum lunas
        $invoiceBelumLunas = Invoice::whereIn('status', ['unpaid', 'partial'])->count();

        // Pendapatan bulan ini (dari invoice paid/partial)
        $pendapatanBulanIni = DetailInvoice::whereHas('invoice', function ($q) use ($bulanIni, $bulanIniEnd) {
            $q->whereIn('status', ['paid', 'partial'])
                ->whereBetween('tanggal_invoice', [$bulanIni, $bulanIniEnd]);
        })->sum('jumlah');

        // HPP bulan ini
        $hppBulanIni = DetailInvoice::whereHas('invoice', function ($q) use ($bulanIni, $bulanIniEnd) {
            $q->whereIn('status', ['paid', 'partial'])
                ->whereBetween('tanggal_invoice', [$bulanIni, $bulanIniEnd]);
        })->sum(DB::raw('qty * harga_modal_satuan'));

        // Pengeluaran bulan ini
        $pengeluaranBulanIni = Pengeluaran::whereBetween('tanggal', [$bulanIni, $bulanIniEnd])->sum('jumlah');

        // Laba bersih bulan ini
        $labaBersih = $pendapatanBulanIni - $hppBulanIni - $pengeluaranBulanIni;

        // Grafik pendapatan vs pengeluaran (6 bulan terakhir)
        $chartData = $this->getChartData();

        // Tiket terbaru
        $tiketTerbaru = TiketServis::with(['pelanggan', 'teknisi'])
            ->whereNotIn('status', ['diambil', 'batal'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Invoice terbaru belum lunas
        $invoiceTerbaru = Invoice::with(['tiket.pelanggan'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Tiket selesai yang belum dibuatkan invoice
        $tiketSelesaiBelumInvoice = TiketServis::belumInvoice()
            ->with('pelanggan')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'tiketAktif',
            'invoiceBelumLunas',
            'pendapatanBulanIni',
            'pengeluaranBulanIni',
            'labaBersih',
            'chartData',
            'tiketTerbaru',
            'invoiceTerbaru',
            'tiketSelesaiBelumInvoice'
        ));
    }

    private function getChartData(): array
    {
        $labels = [];
        $pendapatan = [];
        $pengeluaranData = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $labels[] = $bulan->translatedFormat('M Y');
            $start = $bulan->copy()->startOfMonth();
            $end = $bulan->copy()->endOfMonth();

            $pendapatan[] = (float) DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
                $q->whereIn('status', ['paid', 'partial'])
                    ->whereBetween('tanggal_invoice', [$start, $end]);
            })->sum('jumlah');

            $pengeluaranData[] = (float) Pengeluaran::whereBetween('tanggal', [$start, $end])->sum('jumlah');
        }

        return [
            'labels' => $labels,
            'pendapatan' => $pendapatan,
            'pengeluaran' => $pengeluaranData,
        ];
    }
}
