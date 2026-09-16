<x-app-layout>
    <x-slot name="title">Tambah Suku Cadang / Produk</x-slot>
    <x-slot name="header">Tambah Suku Cadang / Produk</x-slot>
    <x-slot name="subtitle">Pencatatan suku cadang servis atau aksesoris baru ke dalam sistem ERP</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('produk.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="kode_produk" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Kode / SKU Produk <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode_produk" id="kode_produk" value="{{ old('kode_produk') }}" required
                               placeholder="Contoh: LCD-IP13-ORI"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('kode_produk') border-rose-500 @enderror">
                        @error('kode_produk')
                            <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tipe" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Tipe Produk <span class="text-rose-500">*</span>
                        </label>
                        <select name="tipe" id="tipe" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="sparepart" {{ old('tipe') == 'sparepart' ? 'selected' : '' }}>Suku Cadang (Sparepart Fisik)</option>
                            <option value="jasa" {{ old('tipe') == 'jasa' ? 'selected' : '' }}>Layanan Jasa Servis</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nama_produk" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nama Produk / Suku Cadang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk') }}" required
                               placeholder="Contoh: LCD iPhone 13 Pro Original OLED"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('nama_produk') border-rose-500 @enderror">
                        @error('nama_produk')
                            <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kategori_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Kategori Produk <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_id" id="kategori_id" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="harga_modal" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Harga Modal / Beli (HPP) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="harga_modal" id="harga_modal" value="{{ old('harga_modal', 0) }}" required min="0" step="1000"
                                   class="w-full pl-10 rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        </div>
                    </div>

                    <div>
                        <label for="harga_jual" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Harga Jual ke Konsumen <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual', 0) }}" required min="0" step="1000"
                                   class="w-full pl-10 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="stok" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Jumlah Stok Fisik (Kosongkan jika Jasa)
                        </label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', 0) }}" min="0"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <div>
                        <label for="garansi_hari" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Garansi Toko (Hari)
                        </label>
                        <input type="number" name="garansi_hari" id="garansi_hari" value="{{ old('garansi_hari', 30) }}" min="0"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('produk.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
