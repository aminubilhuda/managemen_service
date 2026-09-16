<x-app-layout>
    <x-slot name="title">Daftar Invoice</x-slot>
    <x-slot name="header">Invoice & Kasir</x-slot>
    <x-slot name="subtitle">Pencatatan faktur penagihan servis, penjualan suku cadang, dan status pembayaran kasir</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('invoice.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Invoice Baru
        </a>
    </x-slot>

    <!-- Antrian Invoice: Tiket Selesai Belum Dibuatkan Invoice -->
    @if($tiketSelesaiBelumInvoice->isNotEmpty())
    <div class="bg-white rounded-2xl border border-rose-200/80 overflow-hidden shadow-sm mb-6">
        <div class="px-6 py-4.5 border-b border-rose-100 flex items-center justify-between bg-rose-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></div>
                <h3 class="font-bold text-slate-800 text-sm sm:text-base">Antrian Invoice</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">{{ $tiketSelesaiBelumInvoice->total() }} tiket menunggu</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-rose-50/30 border-b border-rose-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3">No. Tiket</th>
                        <th class="px-6 py-3">Perangkat</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100/60">
                    @foreach($tiketSelesaiBelumInvoice as $t)
                        <tr class="hover:bg-rose-50/30 transition-colors">
                            <td class="px-6 py-3.5">
                                <a href="{{ route('tiket-servis.show', $t) }}" class="font-extrabold text-indigo-600 hover:text-indigo-800 font-mono text-sm">
                                    {{ $t->no_tiket }}
                                </a>
                            </td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $t->perangkat }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $t->pelanggan->nama_pelanggan }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $t->updated_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('invoice.create', ['tiket_id' => $t->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Buat Invoice
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tiketSelesaiBelumInvoice->hasPages())
            <div class="px-6 py-3 border-t border-rose-100">
                {{ $tiketSelesaiBelumInvoice->links() }}
            </div>
        @endif
    </div>
    @endif

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">No. Invoice / Tanggal</th>
                        <th class="px-6 py-3.5">Pelanggan / Tiket</th>
                        <th class="px-6 py-3.5 text-right">Total Tagihan</th>
                        <th class="px-6 py-3.5 text-right">Sudah Dibayar</th>
                        <th class="px-6 py-3.5 text-right">Sisa Tagihan</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoice as $inv)
                        @php
                            $totalDibayar = $inv->pembayaran->sum('jumlah_dibayar');
                            $sisaTagihan = max(0, $inv->total_tagihan - $totalDibayar);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('invoice.show', $inv) }}" class="font-extrabold text-indigo-600 hover:text-indigo-800 font-mono text-sm block">
                                    {{ $inv->no_invoice }}
                                </a>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">
                                    {{ $inv->tanggal_invoice->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">
                                    {{ $inv->tiket?->pelanggan?->nama_pelanggan ?? 'Penjualan Langsung' }}
                                </div>
                                @if($inv->tiket)
                                    <div class="text-[11px] text-slate-500">
                                        Tiket: {{ $inv->tiket->no_tiket }} ({{ $inv->tiket->perangkat }})
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($inv->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-emerald-600">
                                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $sisaTagihan > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'unpaid' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'void' => 'bg-slate-100 text-slate-500 border-slate-200 line-through',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusClasses[$inv->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ strtoupper($inv->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('invoice.show', $inv) }}" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Lihat Invoice & Bayar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('invoice.cetak-pdf', $inv) }}" target="_blank" class="p-2 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Cetak Nota Invoice PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold text-slate-600">Belum ada invoice yang diterbitkan</p>
                                <p class="text-xs text-slate-400 mt-0.5">Buat invoice langsung atau dari tiket servis yang telah selesai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoice->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $invoice->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
