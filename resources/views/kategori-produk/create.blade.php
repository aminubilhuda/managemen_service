<x-app-layout>
    <x-slot name="title">Tambah Kategori Produk</x-slot>
    <x-slot name="header">Tambah Kategori Produk</x-slot>
    <x-slot name="subtitle">Buat kelompok jenis barang suku cadang, aksesoris, atau perangkat</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('kategori-produk.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="nama_kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_kategori" id="nama_kategori" value="{{ old('nama_kategori') }}" required
                           placeholder="Contoh: Sparepart LCD, Baterai, Aksesoris..."
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('nama_kategori') border-rose-500 @enderror">
                    @error('nama_kategori')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Deskripsi Singkat (Opsional)
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              placeholder="Keterangan kategori..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('kategori-produk.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
