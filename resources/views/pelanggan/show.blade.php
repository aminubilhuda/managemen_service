<x-app-layout>
    <x-slot name="title">Detail Pelanggan — {{ $pelanggan->nama_pelanggan }}</x-slot>
    <x-slot name="header">{{ $pelanggan->nama_pelanggan }}</x-slot>
    <x-slot name="subtitle">Informasi profil pelanggan dan riwayat transaksi servis</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('tiket-servis.create', ['pelanggan_id' => $pelanggan->id]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Tiket Servis
        </a>
        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold rounded-xl transition">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-blue-600 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        {{ strtoupper(substr($pelanggan->nama_pelanggan, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $pelanggan->nama_pelanggan }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Terdaftar: {{ $pelanggan->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                <div class="space-y-3.5 pt-2 border-t border-slate-100 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Nomor Kontak</span>
                        @if($pelanggan->telp)
                            <div class="flex items-center justify-between mt-1">
                                <span class="font-semibold text-slate-800">{{ $pelanggan->telp }}</span>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pelanggan->telp) }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 text-xs font-bold">
                                    WhatsApp →
                                </a>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Tidak tersedia</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Email</span>
                        <span class="font-semibold text-slate-800">{{ $pelanggan->email ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px] font-bold uppercase tracking-wider">Alamat</span>
                        <p class="text-slate-700 mt-1 leading-relaxed">{{ $pelanggan->alamat ?? 'Belum ada catatan alamat.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Mini Card -->
            <div class="bg-gradient-to-br from-indigo-900 to-slate-950 rounded-2xl p-5 text-white border border-indigo-900/50 shadow-sm">
                <p class="text-xs font-semibold text-indigo-300">Total Tiket Servis</p>
                <p class="text-3xl font-black text-white mt-1">{{ $pelanggan->tiketServis->count() }}</p>
                <p class="text-xs text-slate-400 mt-1">Perangkat yang pernah diservis di Cekat Cell</p>
            </div>
        </div>

        <!-- Right Side: Riwayat Tiket Servis -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">Riwayat Servis Perangkat</h4>
                    <span class="text-xs text-slate-400">{{ $pelanggan->tiketServis->count() }} Transaksi</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($pelanggan->tiketServis as $t)
                        <div class="p-6 hover:bg-slate-50/70 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tiket-servis.show', $t) }}" class="font-extrabold text-sm text-indigo-600 hover:text-indigo-800">
                                            {{ $t->no_tiket }}
                                        </a>
                                        <span class="text-xs text-slate-400">•</span>
                                        <span class="text-sm font-bold text-slate-800">{{ $t->perangkat }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">Keluhan: {{ $t->keluhan }}</p>
                                </div>
                                <div class="shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                        @if($t->status === 'selesai' || $t->status === 'diambil') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($t->status === 'batal') bg-rose-50 text-rose-700 border border-rose-200
                                        @else bg-blue-50 text-blue-700 border border-blue-200 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $t->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-400">
                                <span>Tanggal Masuk: {{ $t->created_at->translatedFormat('d M Y H:i') }}</span>
                                <a href="{{ route('tiket-servis.show', $t) }}" class="font-bold text-slate-600 hover:text-indigo-600 transition">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            Pelanggan ini belum memiliki riwayat servis perangkat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
