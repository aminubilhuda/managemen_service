<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TiketServisResource;
use App\Http\Traits\ApiResponse;
use App\Models\Invoice;
use App\Models\Pembayaran;
use App\Models\TiketServis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Get mobile dashboard summary metrics.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Status count
        $statusCounts = TiketServis::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $activeStatuses = ['diterima', 'dicek', 'menunggu_sparepart', 'dikerjakan'];
        $totalAktif = 0;
        foreach ($activeStatuses as $st) {
            $totalAktif += ($statusCounts[$st] ?? 0);
        }

        // 2. My tasks count (for technicians / users)
        $myTasksCount = TiketServis::where('teknisi_id', $user->id)
            ->whereIn('status', $activeStatuses)
            ->count();

        // 3. Financial snapshot (if user has permission or role)
        $isOwnerOrAdmin = $user->hasAnyRole(['super-admin', 'admin', 'owner']);
        $pendapatanHariIni = 0;
        $pendapatanBulanIni = 0;

        if ($isOwnerOrAdmin) {
            $pendapatanHariIni = (float) Pembayaran::whereDate('tanggal_bayar', today())->sum('jumlah_dibayar');
            $pendapatanBulanIni = (float) Invoice::whereIn('status', ['paid', 'partial'])
                ->whereBetween('tanggal_invoice', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_tagihan');
        }

        // 4. Latest active tickets
        $latestTickets = TiketServis::with(['pelanggan', 'teknisi'])
            ->whereIn('status', $activeStatuses)
            ->latest()
            ->take(5)
            ->get();

        return $this->successResponse([
            'status_counts' => [
                'total_aktif' => $totalAktif,
                'diterima' => $statusCounts['diterima'] ?? 0,
                'dicek' => $statusCounts['dicek'] ?? 0,
                'menunggu_sparepart' => $statusCounts['menunggu_sparepart'] ?? 0,
                'dikerjakan' => $statusCounts['dikerjakan'] ?? 0,
                'selesai' => $statusCounts['selesai'] ?? 0,
                'diambil' => $statusCounts['diambil'] ?? 0,
                'batal' => $statusCounts['batal'] ?? 0,
            ],
            'my_tasks_count' => $myTasksCount,
            'pendapatan' => [
                'hari_ini' => $pendapatanHariIni,
                'bulan_ini' => $pendapatanBulanIni,
            ],
            'tiket_terbaru' => TiketServisResource::collection($latestTickets),
        ], 'Dashboard ringkasan mobile berhasil dimuat.');
    }
}
