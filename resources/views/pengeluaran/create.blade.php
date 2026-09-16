<x-app-layout>
    <x-slot name="title">Catat Pengeluaran Baru</x-slot>
    <x-slot name="header">Catat Pengeluaran Operasional</x-slot>
    <x-slot name="subtitle">Pencatatan beban kas toko untuk penghitungan laba bersih operasional</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('pengeluaran.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tanggal" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Tanggal Pengeluaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <div>
                        <label for="kategori_pengeluaran" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Kategori Beban <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_pengeluaran" id="kategori_pengeluaran" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="Listrik & Air" {{ old('kategori_pengeluaran') == 'Listrik & Air' ? 'selected' : '' }}>Listrik & Air</option>
                            <option value="Sewa Tempat" {{ old('kategori_pengeluaran') == 'Sewa Tempat' ? 'selected' : '' }}>Sewa Tempat</option>
                            <option value="Gaji Karyawan" {{ old('kategori_pengeluaran') == 'Gaji Karyawan' ? 'selected' : '' }}>Gaji Karyawan</option>
                            <option value="Perlengkapan Servis" {{ old('kategori_pengeluaran') == 'Perlengkapan Servis' ? 'selected' : '' }}>Perlengkapan / Alat Servis</option>
                            <option value="Internet & Pulsa" {{ old('kategori_pengeluaran') == 'Internet & Pulsa' ? 'selected' : '' }}>Internet & Pulsa</option>
                            <option value="Konsumsi & Dapur" {{ old('kategori_pengeluaran') == 'Konsumsi & Dapur' ? 'selected' : '' }}>Konsumsi & Dapur</option>
                            <option value="Lain-lain" {{ old('kategori_pengeluaran') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="jumlah" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nominal Pengeluaran (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" required min="1000" step="1000"
                               placeholder="Contoh: 150000"
                               class="w-full pl-10 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Deskripsi / Keterangan Beban <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" required
                              placeholder="Misal: Pembelian timah solder, pasta processor, dan obeng set teknisi..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label for="bukti" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Upload Bukti / Nota Pembayaran (Opsional)
                    </label>
                    <input type="file" name="bukti" id="bukti" accept="image/*,.pdf"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pengeluaran.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-rose-500/25 transition-all">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
