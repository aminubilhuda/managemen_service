<x-app-layout>
    <x-slot name="title">Laporan Tiket Servis</x-slot>
    <x-slot name="header">Laporan Operasional Tiket Servis</x-slot>
    <x-slot name="subtitle">Statistik dan rincian servis perangkat masuk, teknisi pengerjaan, dan penyelesaian</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('laporan.tiket.export-excel', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export ke Excel (.xlsx)
        </a>
    </x-slot>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('laporan.tiket') }}" class="flex flex-wrap items-center gap-3">
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
                    <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="dicek" {{ request('status') == 'dicek' ? 'selected' : '' }}>Dicek</option>
                    <option value="menunggu_sparepart" {{ request('status') == 'menunggu_sparepart' ? 'selected' : '' }}>Menunggu Sparepart</option>
                    <option value="dikerjakan" {{ request('status') == 'dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="diambil" {{ request('status') == 'diambil' ? 'selected' : '' }}>Diambil</option>
                    <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">No. Tiket</th>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pelanggan</th>
                        <th class="px-6 py-3.5">Perangkat</th>
                        <th class="px-6 py-3.5">Teknisi</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($data as $t)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-3.5 font-bold font-mono text-indigo-600">
                                <a href="{{ route('tiket-servis.show', $t) }}" class="hover:underline">{{ $t->no_tiket }}</a>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $t->created_at->translatedFormat('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $t->pelanggan->nama_pelanggan }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $t->perangkat }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $t->teknisi?->name ?? 'Belum Ditugaskan' }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', $t->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data tiket servis pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
