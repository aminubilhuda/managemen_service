<x-app-layout>
    <x-slot name="title">Invoice {{ $invoice->no_invoice }}</x-slot>
    <x-slot name="header">Faktur Invoice #{{ $invoice->no_invoice }}</x-slot>
    <x-slot name="subtitle">Rincian tagihan transaksi servis & penjualan suku cadang</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('invoice.cetak-pdf', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Faktur PDF
        </a>

        @if($invoice->status !== 'void' && $invoice->status !== 'paid')
            @can('pembayaran.create')
                <button type="button" onclick="document.getElementById('modalBayar').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Terima Pembayaran Kasir
                </button>
            @endcan
        @endif

        @if($invoice->status !== 'void')
            @can('invoice.void')
                <form method="POST" action="{{ route('invoice.void', $invoice) }}" onsubmit="return confirm('PERINGATAN: Membatalkan invoice ini akan mengembalikan stok produk yang telah terpotong. Lanjutkan pembatalan (Void)?')" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="alasan" value="Pembatalan invoice oleh kasir/admin">
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs sm:text-sm font-bold rounded-xl border border-rose-200 transition">
                        Void Invoice
                    </button>
                </form>
            @endcan
        @endif
    </x-slot>

    @php
        $totalDibayar = $invoice->pembayaran->sum('jumlah_dibayar');
        $sisaTagihan = max(0, $invoice->total_tagihan - $totalDibayar);
    @endphp

    <!-- Invoice Header Status Banner -->
    <div class="mb-6 rounded-2xl bg-white border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center border border-indigo-100 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Faktur:</span>
                        @php
                            $statusBadges = [
                                'unpaid' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'void' => 'bg-slate-100 text-slate-500 border-slate-200 line-through',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $statusBadges[$invoice->status] ?? 'bg-slate-100' }}">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Diterbitkan pada {{ $invoice->tanggal_invoice->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>

            <!-- Quick Status Change Form -->
            <form method="POST" action="{{ route('invoice.update-status', $invoice) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                @method('PUT')
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-slate-500 hidden sm:inline">Ubah:</span>
                    <select name="status" class="rounded-xl border border-slate-200 text-xs font-bold py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-slate-50">
                        <option value="unpaid" {{ $invoice->status === 'unpaid' ? 'selected' : '' }}>UNPAID (Belum Lunas)</option>
                        <option value="partial" {{ $invoice->status === 'partial' ? 'selected' : '' }}>PARTIAL (Dibayar Sebagian)</option>
                        <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>PAID (Lunas Penuh)</option>
                        <option value="void" {{ $invoice->status === 'void' ? 'selected' : '' }}>VOID (Dibatalkan)</option>
                    </select>
                </div>

                <input type="text" name="keterangan" placeholder="Catatan/Alasan (opsional)..." class="rounded-xl border border-slate-200 text-xs py-2 px-3 w-44 sm:w-52 focus:ring-indigo-500 focus:border-indigo-500">

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition whitespace-nowrap">
                    Update Status
                </button>
            </form>

            <div class="flex items-center gap-6 text-right shrink-0">
                <div>
                    <span class="text-xs text-slate-400 block font-bold uppercase">Total Tagihan</span>
                    <span class="text-xl sm:text-2xl font-black text-slate-900">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
                </div>
                <div class="border-l border-slate-200 pl-6">
                    <span class="text-xs text-slate-400 block font-bold uppercase">Sisa Tagihan</span>
                    <span class="text-xl sm:text-2xl font-black {{ $sisaTagihan > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Invoice Paper (Matching User's Reference Layout) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-sm max-w-3xl mx-auto text-slate-800 font-sans">
                <!-- Top Header: Brand Left & Invoice Meta Right -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                    <!-- Left: Logo & Company -->
                    @php
                        $perusahaan = $invoice->perusahaan ?? \App\Models\Perusahaan::first();
                    @endphp
                    <div class="flex items-start gap-3.5">
                        <div class="shrink-0 pt-0.5">
                            @if(!empty($perusahaan?->logo))
                                <img src="{{ asset('storage/' . $perusahaan->logo) }}" 
                                     alt="{{ $perusahaan->nama_perusahaan }}" 
                                     class="h-12 sm:h-14 w-auto max-w-[140px] sm:max-w-[180px] object-contain rounded-lg">
                            @else
                                <svg class="w-12 h-12" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <polygon points="50,5 90,26 90,74 50,95 10,74 10,26" fill="#1e4e8c" />
                                    <polygon points="50,14 82,31 82,69 50,86 18,69 18,31" fill="#2563eb" />
                                    <circle cx="50" cy="50" r="22" fill="#ffffff" />
                                    <circle cx="50" cy="50" r="13" fill="#1e3a8a" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">
                                {{ $perusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko') }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed">
                                {{ $perusahaan?->alamat ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-600 font-semibold mt-0.5">
                                {{ $perusahaan?->telp ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: Invoice Title & Key Meta -->
                    <div class="text-left sm:text-right">
                        <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3" style="color: #24529a;">
                            Invoice
                        </h1>
                        <div class="text-xs space-y-1 inline-block text-left">
                            <div class="grid grid-cols-[85px_1fr] sm:grid-cols-[95px_1fr] gap-x-2">
                                <span class="text-slate-500">Referensi</span>
                                <span class="font-bold text-slate-900 text-right">{{ $invoice->no_invoice }}</span>
                            </div>
                            <div class="grid grid-cols-[85px_1fr] sm:grid-cols-[95px_1fr] gap-x-2">
                                <span class="text-slate-500">Tanggal</span>
                                <span class="font-semibold text-slate-800 text-right">{{ $invoice->tanggal_invoice->format('d/m/Y, H.i.s') }}</span>
                            </div>
                            @if($invoice->tiket)
                                <div class="grid grid-cols-[85px_1fr] sm:grid-cols-[95px_1fr] gap-x-2">
                                    <span class="text-slate-500">No. Tiket</span>
                                    <span class="font-bold text-slate-900 text-right">{{ $invoice->tiket->no_tiket }}</span>
                                </div>
                            @endif
                            <div class="grid grid-cols-[85px_1fr] sm:grid-cols-[95px_1fr] gap-x-2">
                                <span class="text-slate-500">Status</span>
                                <span class="font-bold text-right uppercase {{ $invoice->status === 'paid' ? 'text-emerald-700' : 'text-slate-900' }}">{{ strtoupper($invoice->status) }}</span>
                            </div>
                            <div class="grid grid-cols-[85px_1fr] sm:grid-cols-[95px_1fr] gap-x-2">
                                <span class="text-slate-500">Garansi</span>
                                <span class="font-semibold text-slate-800 text-right">
                                    {{ $invoice->tiket?->garansi_sampai ? 's/d ' . $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : ($invoice->tiket ? 's/d 30 hari ke depan' : '-') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2-Column Info: Info Perusahaan & Tagihan Untuk -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 my-6">
                    <!-- Left: Info Perusahaan -->
                    <div>
                        <div class="border-b-2 border-slate-700 pb-1 mb-2.5">
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Info Perusahaan</h4>
                        </div>
                        <div class="text-xs text-slate-600 space-y-0.5 leading-relaxed">
                            <p class="font-bold text-slate-900 text-sm">{{ $invoice->perusahaan?->nama_perusahaan ?? ($perusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko')) }}</p>
                            <p>{{ $invoice->perusahaan?->alamat ?? ($perusahaan?->alamat ?? '-') }}</p>
                            <p class="text-slate-700">Telp: {{ $invoice->perusahaan?->telp ?? ($perusahaan?->telp ?? '-') }}</p>
                        </div>
                    </div>

                    <!-- Right: Tagihan Untuk -->
                    <div>
                        <div class="border-b-2 border-slate-700 pb-1 mb-2.5">
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Tagihan Untuk</h4>
                        </div>
                        <div class="text-xs text-slate-600 space-y-0.5 leading-relaxed">
                            <p class="font-bold text-slate-900 text-sm">{{ $invoice->tiket?->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum' }}</p>
                            <p>{{ $invoice->tiket?->pelanggan?->alamat ?? 'Alamat Pelanggan' }}</p>
                            <p class="text-slate-700">Telp: {{ $invoice->tiket?->pelanggan?->telp ?? '-' }}</p>
                            @if($invoice->tiket)
                                <p class="font-semibold text-slate-900">Perangkat: {{ $invoice->tiket->perangkat }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table with Royal Blue Header -->
                <div class="overflow-x-auto my-6">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr style="background-color: #24529a; color: #ffffff;">
                                <th class="py-2.5 px-3.5 font-bold" style="background-color: #24529a; color: #ffffff;">Produk</th>
                                <th class="py-2.5 px-3.5 font-bold" style="background-color: #24529a; color: #ffffff;">Deskripsi</th>
                                <th class="py-2.5 px-3.5 font-bold text-center" style="background-color: #24529a; color: #ffffff;">Qty</th>
                                <th class="py-2.5 px-3.5 font-bold text-right" style="background-color: #24529a; color: #ffffff;">Harga</th>
                                <th class="py-2.5 px-3.5 font-bold text-right" style="background-color: #24529a; color: #ffffff;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->detail as $detail)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-3.5 align-top">
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $detail->produk?->nama_produk ?? $detail->deskripsi }}
                                        </div>
                                        @if($detail->produk && $detail->produk->tipe === 'sparepart')
                                            <div class="text-[11px] text-slate-400 italic">
                                                (Garansi Part s/d {{ $invoice->tiket?->garansi_sampai ? $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : now()->addDays(30)->translatedFormat('d F Y') }})
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3.5 align-top text-slate-600">
                                        {{ $detail->deskripsi }}
                                    </td>
                                    <td class="py-3 px-3.5 align-top text-center font-bold text-slate-800">
                                        {{ $detail->qty }}
                                    </td>
                                    <td class="py-3 px-3.5 align-top text-right text-slate-700 whitespace-nowrap">
                                        Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-3.5 align-top text-right font-bold text-slate-900 whitespace-nowrap">
                                        Rp {{ number_format($detail->jumlah, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary (Keterangan Left & Calculation Right) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 pb-8 border-t border-slate-100 text-xs">
                    <!-- Left: Keterangan -->
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-2">Keterangan</h4>
                        <div class="text-slate-600 text-xs leading-relaxed space-y-1">
                            <p>{{ $invoice->keterangan ?: 'Terima kasih telah mempercayakan service kepada kami.' }}</p>
                            <p>- Syarat Garansi: Tidak boleh merusak segel</p>
                            <p>Garansi service berlaku sampai {{ $invoice->tiket?->garansi_sampai ? $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : now()->addDays(30)->translatedFormat('d F Y') }}.</p>
                        </div>
                    </div>

                    <!-- Right: Calculation Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-slate-700">
                            <span class="font-medium">Total Biaya</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->diskon_nominal > 0)
                            <div class="flex items-center justify-between text-emerald-600">
                                <span>Diskon</span>
                                <span class="font-bold">- Rp {{ number_format($invoice->diskon_nominal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($invoice->pajak_nominal > 0)
                            <div class="flex items-center justify-between text-slate-600">
                                <span>{{ $invoice->pajak?->nama_pajak ?? 'Pajak' }} ({{ $invoice->pajak_persen }}%)</span>
                                <span class="font-bold">+ Rp {{ number_format($invoice->pajak_nominal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-slate-700">
                            <span class="font-medium">Telah Dibayar</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-700">
                            <span class="font-medium">Status</span>
                            <span class="font-bold uppercase {{ $invoice->status === 'paid' ? 'text-emerald-700' : 'text-slate-900' }}">{{ strtoupper($invoice->status) }}</span>
                        </div>
                        <div class="pt-2 border-t-2 border-slate-900 flex items-center justify-between text-sm sm:text-base font-black text-slate-900">
                            <span>Sisa Tagihan</span>
                            <span>Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Date Stamp -->
                <div class="text-center pt-6 border-t border-slate-100">
                    <span class="font-bold text-slate-900 text-xs sm:text-sm">
                        {{ $invoice->tanggal_invoice->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Side: Riwayat Pembayaran -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider">
                        Riwayat Pembayaran
                    </h4>
                    <span class="text-xs font-bold text-emerald-600">Total: Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($invoice->pembayaran as $pemb)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-slate-900 text-sm">
                                    Rp {{ number_format($pemb->jumlah_dibayar, 0, ',', '.') }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                    {{ $pemb->metode_bayar }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-slate-400 text-[11px] mt-2">
                                <span>{{ $pemb->tanggal_bayar->translatedFormat('d M Y H:i') }}</span>
                                <span>Kasir: {{ $pemb->user?->name ?? 'Kasir' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">
                            Belum ada riwayat pembayaran yang dicatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Input Pembayaran Kasir -->
    <div id="modalBayar" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative">
            <button type="button" onclick="document.getElementById('modalBayar').classList.add('hidden')" class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Catat Pembayaran Kasir</h3>
            <p class="text-xs text-slate-500 mb-5">Sisa tagihan yang harus dilunasi: <strong class="text-rose-600">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong></p>

            <form method="POST" action="{{ route('invoice.bayar', $invoice) }}" class="space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="jumlah_dibayar" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" onclick="document.getElementById('jumlah_dibayar').value = '{{ $sisaTagihan }}'" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold">
                            Bayar Penuh (Rp {{ number_format($sisaTagihan, 0, ',', '.') }})
                        </button>
                    </div>
                    <input type="number" name="jumlah_dibayar" id="jumlah_dibayar" value="{{ $sisaTagihan }}" max="{{ $sisaTagihan }}" min="1" step="any" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div>
                    <label for="metode_bayar" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select name="metode_bayar" id="metode_bayar" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="tunai">Tunai / Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="kartu">Kartu Debit / Kredit</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalBayar').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
