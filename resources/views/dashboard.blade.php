<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">Ringkasan Operasional Servis</x-slot>
    <x-slot name="subtitle">Pantau performa layanan servis, status unit, invoice, dan keuangan secara real-time</x-slot>

    <!-- Welcome & Quick Action Banner -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl shadow-slate-950/10 border border-slate-800/80 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-2 border border-indigo-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    {{ $appPerusahaan?->nama_perusahaan ?? 'Cekat Cell' }} • Sistem Aktif
                </div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                    Halo, {{ Auth::user()->name }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                    Kelola antrian servis hari ini, update status pengerjaan teknisi, dan pantau arus kas masuk secara akurat.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                @can('tiket.create')
                <a href="{{ route('tiket-servis.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tiket Servis Baru
                </a>
                @endcan
                @can('invoice.create')
                <a href="{{ route('invoice.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 text-slate-100 text-xs sm:text-sm font-semibold transition-all">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Buat Invoice
                </a>
                @endcan
                @can('pengeluaran.create')
                <a href="{{ route('pengeluaran.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 text-slate-100 text-xs sm:text-sm font-semibold transition-all">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Catat Beban Kas
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
        <!-- Tiket Aktif -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tiket Aktif</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $tiketAktif }}</h3>
                    <p class="text-xs text-blue-600 font-semibold mt-1 flex items-center gap-1">
                        <span>Sedang dalam antrian / pengerjaan</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
            </div>
        </div>

        <!-- Tiket Selesai Belum Dibuat Invoice -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Belum Dibuat Invoice</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $tiketSelesaiBelumInvoice->count() }}</h3>
                    <p class="text-xs text-rose-600 font-semibold mt-1 flex items-center gap-1">
                        <span>Tiket selesai, menunggu kasir</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 group-hover:bg-rose-600 text-rose-600 group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
            </div>
        </div>

        <!-- Invoice Belum Lunas -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Invoice Belum Lunas</p>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $invoiceBelumLunas }}</h3>
                    <p class="text-xs text-amber-600 font-semibold mt-1 flex items-center gap-1">
                        <span>Menunggu pelunasan kasir</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-500 text-amber-600 group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pendapatan Bulan Ini</p>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1 truncate">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 font-semibold mt-1">
                        Periode {{ now()->translatedFormat('F Y') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm shrink-0 ml-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Laba Bersih Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $labaBersih >= 0 ? 'from-cyan-500 to-blue-600' : 'from-rose-500 to-red-600' }}"></div>
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Laba Bersih Bulan Ini</p>
                    <h3 class="text-xl sm:text-2xl font-black {{ $labaBersih >= 0 ? 'text-slate-900' : 'text-rose-600' }} mt-1 truncate">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs {{ $labaBersih >= 0 ? 'text-cyan-700' : 'text-rose-600' }} font-semibold mt-1">
                        Pendapatan - HPP - Beban Kas
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $labaBersih >= 0 ? 'bg-cyan-50 group-hover:bg-cyan-600 text-cyan-600' : 'bg-rose-50 group-hover:bg-rose-600 text-rose-600' }} group-hover:text-white flex items-center justify-center transition-all duration-200 shadow-sm shrink-0 ml-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Summary Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 mb-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Grafik Performa Keuangan (6 Bulan)</h3>
                <p class="text-xs text-slate-500">Perbandingan total omset penjualan & servis dengan biaya operasional bulanan</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-600">Pendapatan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="text-slate-600">Pengeluaran</span>
                </div>
            </div>
        </div>
        <div class="h-72 w-full">
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>

    <!-- Antrian Invoice: Tiket Selesai Belum Dibuatkan Invoice -->
    @if($tiketSelesaiBelumInvoice->isNotEmpty())
    <div class="bg-white rounded-2xl border border-rose-200/80 overflow-hidden shadow-sm mb-8">
        <div class="px-6 py-4.5 border-b border-rose-100 flex items-center justify-between bg-rose-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Antrian Invoice</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">{{ $tiketSelesaiBelumInvoice->count() }} tiket</span>
            </div>
            <a href="{{ route('invoice.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Buat Invoice Baru
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="divide-y divide-rose-100/60">
            @foreach($tiketSelesaiBelumInvoice as $t)
                <div class="px-6 py-3.5 hover:bg-rose-50/30 transition-colors flex items-center justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('tiket-servis.show', $t) }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition truncate">
                                {{ $t->no_tiket }}
                            </a>
                            <span class="text-xs text-slate-400">&bull;</span>
                            <span class="text-xs font-semibold text-slate-600 truncate">{{ $t->perangkat }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                            <span class="text-slate-700 font-medium">{{ $t->pelanggan->nama_pelanggan }}</span>
                            <span>&bull;</span>
                            <span class="text-slate-400">{{ $t->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('invoice.create', ['tiket_id' => $t->id]) }}" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Invoice
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Bottom Lists: Tiket Aktif & Invoice -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tiket Aktif Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Antrian Servis Aktif</h3>
                </div>
                <a href="{{ route('tiket-servis.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    Semua Tiket
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-100 flex-1">
                @forelse($tiketTerbaru as $t)
                    <div class="px-6 py-3.5 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('tiket-servis.show', $t) }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition truncate">
                                    {{ $t->no_tiket }}
                                </a>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs font-semibold text-slate-600 truncate">{{ $t->perangkat }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                <span class="text-slate-700 font-medium">{{ $t->pelanggan->nama_pelanggan }}</span>
                                <span>•</span>
                                <span>Teknisi: {{ $t->teknisi?->name ?? 'Belum Ditugaskan' }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @php
                                $statusBadges = [
                                    'diterima' => 'bg-slate-100 text-slate-700 border-slate-300',
                                    'dicek' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'menunggu_sparepart' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'dikerjakan' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'diambil' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                    'batal' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                                $badgeClass = $statusBadges[$t->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">Tidak ada tiket servis aktif saat ini</p>
                        <p class="text-xs text-slate-400 mt-0.5">Semua servis telah selesai diambil oleh pelanggan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Invoice Belum Lunas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm flex flex-col">
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Tagihan Belum Lunas</h3>
                </div>
                <a href="{{ route('invoice.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    Semua Invoice
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-slate-100 flex-1">
                @forelse($invoiceTerbaru as $inv)
                    <div class="px-6 py-3.5 hover:bg-slate-50/80 transition-colors flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('invoice.show', $inv) }}" class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition truncate">
                                    {{ $inv->no_invoice }}
                                </a>
                                @if($inv->tiket)
                                    <span class="text-xs text-slate-400">• {{ $inv->tiket->no_tiket }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1 truncate">
                                {{ $inv->tiket?->pelanggan?->nama_pelanggan ?? 'Umum / Non-Tiket' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-slate-900">Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mt-0.5 {{ $inv->status === 'unpaid' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $inv->status === 'unpaid' ? 'BELUM BAYAR' : 'SEBAGIAN' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Semua invoice berstatus lunas.</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada tagihan tertunda yang perlu ditagihkan ke pelanggan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('chartPendapatan');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: @json($chartData['pendapatan']),
                            backgroundColor: '#10b981',
                            hoverBackgroundColor: '#059669',
                            borderRadius: 8,
                            barThickness: 24,
                        },
                        {
                            label: 'Pengeluaran',
                            data: @json($chartData['pengeluaran']),
                            backgroundColor: '#f43f5e',
                            hoverBackgroundColor: '#e11d48',
                            borderRadius: 8,
                            barThickness: 24,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                callback: value => 'Rp ' + (value >= 1000000 ? (value / 1000000) + ' Jt' : new Intl.NumberFormat('id-ID').format(value))
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
