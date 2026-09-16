<x-app-layout>
    <x-slot name="title">Tiket {{ $tiketServis->no_tiket }} — {{ $tiketServis->perangkat }}</x-slot>
    <x-slot name="header">Tiket Servis #{{ $tiketServis->no_tiket }}</x-slot>
    <x-slot name="subtitle">Detail perbaikan unit {{ $tiketServis->perangkat }} — {{ $tiketServis->pelanggan?->nama_pelanggan ?? 'Umum' }}</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('tiket-servis.cetak-tanda-terima', $tiketServis) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition">
            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Tanda Terima
        </a>

        @if($tiketServis->invoice)
            <a href="{{ route('invoice.show', $tiketServis->invoice) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                Lihat Invoice (#{{ $tiketServis->invoice->no_invoice }})
            </a>
        @else
            <a href="{{ route('invoice.create', ['tiket_id' => $tiketServis->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Invoice Kasir
            </a>
        @endif

        <a href="{{ route('tiket-servis.edit', $tiketServis) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold rounded-xl transition">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </x-slot>

    <!-- Top Status Banner -->
    <div class="mb-6 rounded-2xl bg-white border border-slate-200/80 p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Pengerjaan:</span>
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
                            $badgeClass = $statusBadges[$tiketServis->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $badgeClass }}">
                            {{ strtoupper(str_replace('_', ' ', $tiketServis->status)) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Diterima pada {{ $tiketServis->created_at->translatedFormat('l, d F Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- Quick Status Change Form -->
            <form method="POST" action="{{ route('tiket-servis.update-status', $tiketServis) }}" class="flex flex-wrap items-center gap-2">
                @csrf
                @method('PUT')
                <select name="status" class="rounded-xl border border-slate-200 text-xs font-semibold py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800">
                    @foreach($statusList as $key => $label)
                        <option value="{{ $key }}" {{ $tiketServis->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <input type="text" name="catatan" placeholder="Catatan perubahan (opsional)..." class="rounded-xl border border-slate-200 text-xs py-2 px-3 w-52 focus:ring-indigo-500 focus:border-indigo-500">

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">
                    Update Status
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Device & Customer details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-5">
                <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span>Informasi Unit & Keluhan Fisik</span>
                    <span class="font-mono text-xs text-slate-400">ID: {{ $tiketServis->no_tiket }}</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Perangkat / Tipe</span>
                        <p class="font-extrabold text-slate-900 mt-1 text-base">{{ $tiketServis->perangkat }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Kelengkapan Diserahkan</span>
                        <p class="font-semibold text-slate-800 mt-1">{{ $tiketServis->kelengkapan ?: 'Tidak ada' }}</p>
                    </div>
                </div>

                <div>
                    <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Keluhan Pelanggan</span>
                    <div class="mt-1 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-slate-800 font-medium leading-relaxed">
                        {{ $tiketServis->keluhan }}
                    </div>
                </div>

                @if($tiketServis->kondisi_awal)
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Kondisi Fisik Awal</span>
                        <p class="text-slate-700 mt-1 leading-relaxed">{{ $tiketServis->kondisi_awal }}</p>
                    </div>
                @endif

                @if($tiketServis->diagnosa_teknisi)
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Diagnosa & Catatan Teknisi</span>
                        <div class="mt-1 p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-100 text-indigo-950 font-medium leading-relaxed">
                            {{ $tiketServis->diagnosa_teknisi }}
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Estimasi Biaya</span>
                        <p class="font-bold text-slate-800 mt-1">
                            {{ $tiketServis->estimasi_biaya ? 'Rp ' . number_format($tiketServis->estimasi_biaya, 0, ',', '.') : '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Estimasi Selesai</span>
                        <p class="font-bold text-slate-800 mt-1">
                            {{ $tiketServis->estimasi_selesai ? $tiketServis->estimasi_selesai->translatedFormat('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Biaya Final</span>
                        <p class="font-extrabold text-emerald-600 mt-1 text-base">
                            {{ $tiketServis->biaya_final ? 'Rp ' . number_format($tiketServis->biaya_final, 0, ',', '.') : 'Belum Ditutup' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Status Faktur / Kasir</span>
                        @if($tiketServis->invoice)
                            @php
                                $badgeCls = match($tiketServis->invoice->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'partial' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'void' => 'bg-slate-100 text-slate-500 border-slate-200',
                                    default => 'bg-rose-100 text-rose-800 border-rose-200',
                                };
                            @endphp
                            <div class="mt-1">
                                <a href="{{ route('invoice.show', $tiketServis->invoice) }}" class="inline-flex items-center gap-1 font-bold text-xs px-2.5 py-1 rounded-lg border {{ $badgeCls }} hover:opacity-80 transition">
                                    {{ strtoupper($tiketServis->invoice->status) }}
                                </a>
                            </div>
                        @else
                            <p class="font-semibold text-slate-400 mt-1 text-xs">Belum Diterbitkan</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dokumentasi Foto Unit -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h4 class="font-bold text-slate-900 text-base">Dokumentasi Foto Fisik Unit</h4>
                        <p class="text-xs text-slate-400">Bukti kondisi fisik unit saat diterima, saat proses pengerjaan, atau setelah perbaikan</p>
                    </div>
                </div>

                <!-- Upload form -->
                <form method="POST" action="{{ route('tiket-servis.upload-dokumentasi', $tiketServis) }}" enctype="multipart/form-data" class="mb-6 p-4 rounded-xl bg-slate-50/80 border border-slate-200/80">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Tahap Foto</label>
                            <select name="tipe_dokumentasi" class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 font-semibold text-slate-800 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="sebelum">Kondisi Awal (Sebelum)</option>
                                <option value="proses">Proses Pengerjaan</option>
                                <option value="sesudah">Hasil Akhir (Sesudah)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Pilih Foto (Bisa Banyak)</label>
                            <input type="file" name="foto[]" multiple accept="image/*" required class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Keterangan (Opsional)</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Layar retak, bezel lecet..." class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 bg-white focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="sm:col-span-2">
                            <button type="submit" class="w-full px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Upload
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Photo Gallery Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($tiketServis->dokumentasi as $dok)
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-white group relative shadow-sm hover:shadow-md transition">
                            <div class="relative h-44 bg-slate-100 overflow-hidden">
                                <img src="{{ asset('storage/' . $dok->file_path) }}" alt="Dokumentasi Unit" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                @php
                                    $tagColor = match($dok->tipe_dokumentasi) {
                                        'sesudah' => 'bg-emerald-600 text-white',
                                        'proses' => 'bg-amber-500 text-white',
                                        default => 'bg-indigo-600 text-white',
                                    };
                                    $tagLabel = match($dok->tipe_dokumentasi) {
                                        'sesudah' => 'Sesudah',
                                        'proses' => 'Proses',
                                        default => 'Sebelum',
                                    };
                                @endphp
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider shadow-sm {{ $tagColor }}">
                                    {{ $tagLabel }}
                                </span>

                                <a href="{{ asset('storage/' . $dok->file_path) }}" target="_blank" class="absolute top-2 right-2 p-1.5 rounded-lg bg-slate-950/70 hover:bg-slate-950 text-white opacity-0 group-hover:opacity-100 transition shadow-sm" title="Buka Gambar Penuh">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <div class="p-3">
                                <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $dok->keterangan ?: 'Dokumentasi ' . $tagLabel }}">
                                    {{ $dok->keterangan ?: 'Foto kondisi ' . strtolower($tagLabel) }}
                                </p>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 text-[10px] text-slate-400">
                                    <span>{{ $dok->created_at->translatedFormat('d M H:i') }}</span>
                                    <form method="POST" action="{{ route('tiket-servis.hapus-dokumentasi', $dok) }}" onsubmit="return confirm('Yakin ingin menghapus foto ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center text-slate-400 text-xs bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Belum ada foto dokumentasi fisik untuk tiket ini. Silakan unggah foto bukti kondisi unit di atas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Side: Pelanggan & Timeline -->
        <div class="space-y-6">
            <!-- Pelanggan Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider pb-3 border-b border-slate-100 mb-3">
                    Pelanggan & Teknisi
                </h4>
                <div class="space-y-4 text-xs sm:text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-sm border border-indigo-100">
                            {{ strtoupper(substr($tiketServis->pelanggan->nama_pelanggan, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('pelanggan.show', $tiketServis->pelanggan) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                {{ $tiketServis->pelanggan->nama_pelanggan }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $tiketServis->pelanggan->telp ?? 'Tidak ada telp' }}</p>
                        </div>
                    </div>

                    @if($tiketServis->pelanggan->telp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tiketServis->pelanggan->telp) }}?text=Halo%20{{ urlencode($tiketServis->pelanggan->nama_pelanggan) }},%20update%20servis%20{{ urlencode($tiketServis->perangkat) }}%20dengan%20No%20Tiket%20{{ $tiketServis->no_tiket }}%20saat%20ini%20berstatus%20{{ $tiketServis->status_label }}." target="_blank"
                           class="w-full py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center justify-center gap-1.5 transition">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.585 1.771.897 2.802.897 3.182 0 5.768-2.587 5.768-5.766.001-3.187-2.583-5.784-5.774-5.784zm9.969 5.768c0 5.514-4.486 10-10 10-1.854 0-3.593-.509-5.089-1.393l-4.911 1.287 1.309-4.786c-.997-1.554-1.571-3.385-1.571-5.351 0-5.514 4.486-10 10-10s10 4.486 10 10z"/></svg>
                            Hubungi Pelanggan via WhatsApp
                        </a>
                    @endif

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Teknisi Bertugas</span>
                        <p class="font-bold text-slate-800 mt-0.5">
                            {{ $tiketServis->teknisi?->name ?? 'Belum Ditugaskan' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Timeline Status Servis -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h4 class="font-bold text-slate-900 text-sm uppercase tracking-wider pb-3 border-b border-slate-100 mb-4">
                    Riwayat Alur Status
                </h4>

                <div class="space-y-4">
                    @forelse($tiketServis->riwayatStatus as $riwayat)
                        <div class="flex items-start gap-3 relative">
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-1.5 shrink-0 ring-4 ring-indigo-50"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold text-slate-900">
                                        @if($riwayat->status_sebelum)
                                            <span class="text-slate-400 font-normal">{{ \App\Models\TiketServis::STATUSES[$riwayat->status_sebelum] ?? $riwayat->status_sebelum }}</span>
                                            <span class="text-slate-400">&rarr;</span>
                                        @endif
                                        <span class="text-indigo-600">{{ \App\Models\TiketServis::STATUSES[$riwayat->status_sesudah] ?? $riwayat->status_sesudah }}</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                        {{ $riwayat->created_at->translatedFormat('d M H:i') }}
                                    </span>
                                </div>
                                @if($riwayat->catatan)
                                    <p class="text-xs text-slate-600 mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100">{{ $riwayat->catatan }}</p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-1">Oleh: {{ $riwayat->user?->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
