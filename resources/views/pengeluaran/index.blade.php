<x-app-layout>
    <x-slot name="title">Pengeluaran Operasional</x-slot>
    <x-slot name="header">Pengeluaran Kas Operasional</x-slot>
    <x-slot name="subtitle">Pencatatan beban operasional toko, listrik, sewa, gaji, dan belanja perlengkapan</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('pengeluaran.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-rose-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pengeluaran Baru
        </a>
    </x-slot>

    <!-- Expenses Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Kategori Beban</th>
                        <th class="px-6 py-3.5">Deskripsi / Keterangan</th>
                        <th class="px-6 py-3.5">Dicatat Oleh</th>
                        <th class="px-6 py-3.5 text-right">Nominal Pengeluaran</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pengeluaran as $p)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $p->tanggal->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $p->kategori_pengeluaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $p->deskripsi }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $p->user?->name ?? 'Staff' }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-rose-600 text-sm">
                                Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('pengeluaran.edit', $p) }}" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Beban">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('pengeluaran.destroy', $p) }}" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold text-slate-600">Belum ada pengeluaran kas dicatat</p>
                                <p class="text-xs text-slate-400 mt-0.5">Catat biaya operasional toko untuk perhitungan laba rugi yang akurat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengeluaran->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pengeluaran->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
