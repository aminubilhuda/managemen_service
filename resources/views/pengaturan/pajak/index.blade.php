<x-app-layout>
    <x-slot name="title">Pengaturan Pajak PPN</x-slot>
    <x-slot name="header">Pengaturan Pajak (PPN)</x-slot>
    <x-slot name="subtitle">Kelola tarif pajak pertambahan nilai yang diterapkan pada kalkulasi faktur invoice</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('pengaturan.pajak.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tarif Pajak
        </a>
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Nama Pajak</th>
                        <th class="px-6 py-3.5 text-center">Persentase</th>
                        <th class="px-6 py-3.5">Kondisi Nilai Transaksi</th>
                        <th class="px-6 py-3.5">Berlaku Mulai</th>
                        <th class="px-6 py-3.5 text-center">Status Aktif</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pajak as $p)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $p->nama_pajak }}
                            </td>
                            <td class="px-6 py-4 text-center font-black text-indigo-600 text-base">
                                {{ $p->persentase }}%
                            </td>
                            <td class="px-6 py-4">
                                @if($p->tipe_aturan === 'diatas_nominal')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <span>Subtotal ≥ Rp {{ number_format($p->nominal_batas, 0, ',', '.') }}</span>
                                    </span>
                                @elseif($p->tipe_aturan === 'dibawah_nominal')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span>Subtotal &lt; Rp {{ number_format($p->nominal_batas, 0, ',', '.') }}</span>
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500 font-medium">Semua Nominal Transaksi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $p->berlaku_mulai ? $p->berlaku_mulai->translatedFormat('d F Y') : 'Sekarang' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($p->aktif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('pengaturan.pajak.edit', $p) }}" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Tarif Pajak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('pengaturan.pajak.destroy', $p) }}" onsubmit="return confirm('Yakin ingin menghapus tarif pajak {{ $p->nama_pajak }} ({{ $p->persentase }}%)?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Tarif Pajak">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada tarif pajak yang dikonfigurasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
