<x-app-layout>
    <x-slot name="title">Suku Cadang & Produk</x-slot>
    <x-slot name="header">Suku Cadang & Produk</x-slot>
    <x-slot name="subtitle">Inventori suku cadang handphone/laptop, harga modal, harga jual, dan kontrol stok</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('produk.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Produk / Suku Cadang
        </a>
    </x-slot>

    <!-- Filters & Search -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('produk.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama produk, SKU / kode produk..."
                       class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select name="kategori_id" class="rounded-xl border border-slate-200 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-700">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>

                <select name="tipe" class="rounded-xl border border-slate-200 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-700">
                    <option value="">Semua Tipe</option>
                    <option value="sparepart" {{ request('tipe') == 'sparepart' ? 'selected' : '' }}>Suku Cadang (Sparepart)</option>
                    <option value="jasa" {{ request('tipe') == 'jasa' ? 'selected' : '' }}>Jasa Servis</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold rounded-xl transition">
                    Filter
                </button>

                @if(request()->anyFilled(['search', 'kategori_id', 'tipe']))
                    <a href="{{ route('produk.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs sm:text-sm font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Kode & Nama Produk</th>
                        <th class="px-6 py-3.5">Kategori / Tipe</th>
                        <th class="px-6 py-3.5 text-right">Harga Modal</th>
                        <th class="px-6 py-3.5 text-right">Harga Jual</th>
                        <th class="px-6 py-3.5 text-center">Stok Fisik</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produk as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $item->nama_produk }}</div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $item->kode_produk }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $item->kategori?->nama_kategori ?? 'Tanpa Kategori' }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $item->tipe === 'sparepart' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-cyan-50 text-cyan-700 border border-cyan-100' }}">
                                    {{ $item->tipe }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-slate-500">
                                Rp {{ number_format($item->harga_modal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->tipe === 'jasa')
                                    <span class="text-xs text-slate-400 font-medium">Layanan Jasa</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $item->stok > 2 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                        {{ $item->stok ?? 0 }} Unit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('produk.edit', $item) }}" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('produk.destroy', $item) }}" onsubmit="return confirm('Yakin ingin menghapus item ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold text-slate-600">Tidak ada produk atau suku cadang ditemukan</p>
                                <p class="text-xs text-slate-400 mt-0.5">Tambahkan data produk baru untuk mulai mengelola stok dan penjualan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produk->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $produk->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
