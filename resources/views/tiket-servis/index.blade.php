<x-app-layout>
    <x-slot name="title">Tiket Servis</x-slot>
    <x-slot name="header">Daftar Tiket Servis</x-slot>
    <x-slot name="subtitle">Pencatatan dan pemantauan alur servis handphone/laptop dari penerimaan hingga selesai</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('tiket-servis.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Tiket Baru
        </a>
    </x-slot>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('tiket-servis.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, nama perangkat, nama pelanggan..."
                       class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="status" class="rounded-xl border border-slate-200 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-700">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="teknisi_id" class="rounded-xl border border-slate-200 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-700">
                    <option value="">Semua Teknisi</option>
                    @foreach($teknisiList as $tek)
                        <option value="{{ $tek->id }}" {{ request('teknisi_id') == $tek->id ? 'selected' : '' }}>{{ $tek->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold rounded-xl transition">
                    Filter
                </button>

                @if(request()->anyFilled(['search', 'status', 'teknisi_id']))
                    <a href="{{ route('tiket-servis.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs sm:text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tiket Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">No. Tiket / Tanggal</th>
                        <th class="px-6 py-3.5">Pelanggan</th>
                        <th class="px-6 py-3.5">Perangkat & Keluhan</th>
                        <th class="px-6 py-3.5">Teknisi</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-right">Biaya Servis</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tiket as $t)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('tiket-servis.show', $t) }}" class="font-extrabold text-indigo-600 hover:text-indigo-800 font-mono text-sm block">
                                    {{ $t->no_tiket }}
                                </a>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">
                                    {{ $t->created_at->translatedFormat('d M Y H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $t->pelanggan->nama_pelanggan }}</div>
                                <div class="text-[11px] text-slate-500">{{ $t->pelanggan->telp ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $t->perangkat }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs" title="{{ $t->keluhan }}">
                                    {{ Str::limit($t->keluhan, 45) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($t->teknisi)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                                            {{ substr($t->teknisi->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $t->teknisi->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-xs">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'diterima' => 'bg-slate-100 text-slate-800 border-slate-300 hover:bg-slate-200',
                                        'dicek' => 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100',
                                        'menunggu_sparepart' => 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100',
                                        'dikerjakan' => 'bg-purple-50 text-purple-800 border-purple-200 hover:bg-purple-100',
                                        'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100',
                                        'diambil' => 'bg-cyan-50 text-cyan-800 border-cyan-200 hover:bg-cyan-100',
                                        'batal' => 'bg-rose-50 text-rose-800 border-rose-300 hover:bg-rose-100',
                                    ];
                                @endphp
                                @can('tiket.update_status')
                                    <div x-data="tiketStatusBadge('{{ $t->status }}', '{{ route('tiket-servis.update-status', $t) }}')" class="relative inline-flex items-center">
                                        <select @change="update($event.target.value, $event)" :disabled="loading"
                                            class="appearance-none pr-7 pl-3 py-1 rounded-full border text-xs font-bold cursor-pointer transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 disabled:opacity-50"
                                            :class="colorClass"
                                            title="Klik untuk mengubah status langsung"
                                        >
                                            @foreach($statusList as $key => $label)
                                                <option value="{{ $key }}" {{ $t->status === $key ? 'selected' : '' }} class="bg-white text-slate-800 font-semibold py-1">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute right-2 flex items-center text-current opacity-70">
                                            <template x-if="!loading">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </template>
                                            <template x-if="loading">
                                                <svg class="animate-spin w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$t->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusList[$t->status] ?? $t->status }}
                                    </span>
                                @endcan
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                @if($t->biaya_final)
                                    Rp {{ number_format($t->biaya_final, 0, ',', '.') }}
                                @elseif($t->estimasi_biaya)
                                    <span class="text-xs text-slate-500 font-normal">Est:</span> Rp {{ number_format($t->estimasi_biaya, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 text-xs font-normal">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('tiket-servis.show', $t) }}" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Lihat Detail & Timeline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('tiket-servis.cetak-tanda-terima', $t) }}" target="_blank" class="p-2 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Cetak Tanda Terima PDF">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('tiket-servis.edit', $t) }}" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Tiket">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($t->status === 'selesai' && !$t->invoice)
                                        <a href="{{ route('invoice.create', ['tiket_id' => $t->id]) }}" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Buat Invoice untuk Tiket Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold text-slate-600">Tidak ada tiket servis yang sesuai filter</p>
                                <p class="text-xs text-slate-400 mt-0.5">Tekan tombol "Buat Tiket Baru" untuk mendaftarkan unit servis masuk.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tiket->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $tiket->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function tiketStatusBadge(initialStatus, updateUrl) {
                const colorMap = {
                    'diterima': 'bg-slate-100 text-slate-800 border-slate-300 hover:bg-slate-200',
                    'dicek': 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100',
                    'menunggu_sparepart': 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100',
                    'dikerjakan': 'bg-purple-50 text-purple-800 border-purple-200 hover:bg-purple-100',
                    'selesai': 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100',
                    'diambil': 'bg-cyan-50 text-cyan-800 border-cyan-200 hover:bg-cyan-100',
                    'batal': 'bg-rose-50 text-rose-800 border-rose-300 hover:bg-rose-100',
                };

                return {
                    status: initialStatus,
                    loading: false,
                    get colorClass() {
                        return colorMap[this.status] || 'bg-slate-100 text-slate-800 border-slate-300';
                    },
                    async update(newStatus, event) {
                        if (newStatus === this.status) return;
                        const oldStatus = this.status;
                        this.loading = true;
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            const response = await fetch(updateUrl, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ status: newStatus })
                            });

                            const data = await response.json();
                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Gagal mengubah status.');
                            }

                            this.status = newStatus;
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { message: data.message, type: 'success' }
                            }));

                            if (newStatus === 'selesai') {
                                setTimeout(() => window.location.reload(), 1000);
                            }
                        } catch (err) {
                            if (event && event.target) {
                                event.target.value = oldStatus;
                            }
                            window.dispatchEvent(new CustomEvent('show-toast', {
                                detail: { message: err.message || 'Terjadi kesalahan saat mengubah status.', type: 'error' }
                            }));
                        } finally {
                            this.loading = false;
                        }
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
