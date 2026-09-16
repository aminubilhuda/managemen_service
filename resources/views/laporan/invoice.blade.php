<x-app-layout>
    <x-slot name="title">Laporan Invoice & Pendapatan</x-slot>
    <x-slot name="header">Laporan Transaksi Invoice</x-slot>
    <x-slot name="subtitle">Rekapitulasi penjualan, faktur tagihan servis, dan realisasi penerimaan kas</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('laporan.invoice.export-excel', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export ke Excel (.xlsx)
        </a>
    </x-slot>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('laporan.invoice') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                <span>Dari:</span>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3">
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                <span>Sampai:</span>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3">
            </div>
            <div>
                <select name="status" class="rounded-xl border border-slate-200 text-xs py-2 px-3">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial (Sebagian)</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid (Belum Bayar)</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold uppercase text-slate-400">Total Nilai Tagihan Terbit</p>
            <p class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <p class="text-xs font-bold uppercase text-slate-400">Total Uang Kas Diterima (Realisasi)</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">No. Invoice</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pelanggan</th>
                        <th class="px-6 py-3.5 text-right">Total Tagihan</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($data as $inv)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 font-bold font-mono text-indigo-600">
                                <a href="{{ route('invoice.show', $inv) }}" class="hover:underline">{{ $inv->no_invoice }}</a>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $inv->tanggal_invoice->translatedFormat('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $inv->tiket?->pelanggan?->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-6 py-3.5 text-right font-black text-slate-900">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data invoice pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
