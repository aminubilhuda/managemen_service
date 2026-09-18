<x-app-layout>
    <x-slot name="title">Laporan Laba Rugi</x-slot>
    <x-slot name="header">Laporan Laba Rugi Finansial</x-slot>
    <x-slot name="subtitle">Analisis profitabilitas operasional: Pendapatan, HPP suku cadang, dan beban biaya kas</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('laporan.laba-rugi.export-excel', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Laporan ke Excel
        </a>
    </x-slot>

    <!-- Filter Bulan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('laporan.laba-rugi') }}" class="flex items-center gap-3">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                <span>Pilih Periode Bulan:</span>
                <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" class="rounded-xl border border-slate-200 text-xs py-2 px-3">
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Executive Statement Sheet -->
    <div class="max-w-4xl bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-sm mb-8">
        <div class="text-center pb-6 border-b border-slate-200">
            <h3 class="text-xl font-black text-slate-900">{{ $appPerusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko') }}</h3>
            <p class="text-sm font-bold text-indigo-600 uppercase tracking-wider mt-0.5">Laporan Laba Rugi Operasional</p>
            <p class="text-xs text-slate-400 mt-1">Periode: {{ Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
        </div>

        <div class="py-6 space-y-6 text-sm">
            <!-- 1. PENDAPATAN -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-slate-900 uppercase text-xs tracking-wider">
                    <span>1. Pendapatan Penjualan & Jasa Servis</span>
                    <span>Rp {{ number_format($pendapatan, 0, ',', '.') }}</span>
                </div>
                <div class="pl-4 py-2 space-y-1 text-slate-600 text-xs">
                    <div class="flex items-center justify-between">
                        <span>Realisasi Penjualan & Jasa (Invoice Lunas / Termin)</span>
                        <span>Rp {{ number_format($pendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. HPP -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-slate-900 uppercase text-xs tracking-wider">
                    <span>2. Harga Pokok Penjualan (HPP Suku Cadang)</span>
                    <span class="text-rose-600">( Rp {{ number_format($hpp, 0, ',', '.') }} )</span>
                </div>
                <div class="pl-4 py-2 text-slate-600 text-xs">
                    <div class="flex items-center justify-between">
                        <span>Total Nilai Modal Suku Cadang Terpakai</span>
                        <span>Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- LABA KOTOR SUB -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between font-extrabold text-slate-900">
                <span>LABA KOTOR OPERASIONAL (GROSS PROFIT)</span>
                <span class="text-base text-indigo-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
            </div>

            <!-- 3. BEBAN OPERASIONAL -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-slate-900 uppercase text-xs tracking-wider">
                    <span>3. Beban Kas & Operasional Toko</span>
                    <span class="text-rose-600">( Rp {{ number_format($totalBeban, 0, ',', '.') }} )</span>
                </div>
                <div class="pl-4 py-2 space-y-2 text-xs">
                    @forelse($bebanOperasional as $beban)
                        <div class="flex items-center justify-between text-slate-600">
                            <span>{{ $beban->kategori_pengeluaran }}</span>
                            <span>Rp {{ number_format($beban->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-slate-400 italic">Tidak ada beban operasional tercatat.</div>
                    @endforelse
                </div>
            </div>

            <!-- LABA BERSIH FINAL -->
            <div class="p-5 rounded-2xl {{ $labaBersih >= 0 ? 'bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-200 text-emerald-950' : 'bg-rose-50 border-rose-200 text-rose-950' }} border-2 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider block">LABA BERSIH PERIODE INI (NET PROFIT)</span>
                    <span class="text-xs opacity-75">Laba Kotor dikurangi Total Beban Kas</span>
                </div>
                <span class="text-2xl sm:text-3xl font-black {{ $labaBersih >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</x-app-layout>
