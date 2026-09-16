<x-app-layout>
    <x-slot name="title">Penerimaan Unit Servis Baru</x-slot>
    <x-slot name="header">Buat Tiket Servis Baru</x-slot>
    <x-slot name="subtitle">Pencatatan data unit handphone / laptop masuk, keluhan, dan kelengkapan fisik</x-slot>

    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('tiket-servis.store') }}" class="space-y-6">
                @csrf

                <!-- Pelanggan Selection -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="pelanggan_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Pilih Pelanggan <span class="text-rose-500">*</span>
                        </label>
                        <a href="{{ route('pelanggan.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            + Tambah Pelanggan Baru
                        </a>
                    </div>
                    <select name="pelanggan_id" id="pelanggan_id" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('pelanggan_id') border-rose-500 @enderror">
                        <option value="">-- Pilih Pelanggan Terdaftar --</option>
                        @foreach($pelangganList as $p)
                            <option value="{{ $p->id }}" {{ (old('pelanggan_id', request('pelanggan_id')) == $p->id) ? 'selected' : '' }}>
                                {{ $p->nama_pelanggan }} ({{ $p->telp ?? 'No Telp -' }})
                            </option>
                        @endforeach
                    </select>
                    @error('pelanggan_id')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Perangkat -->
                    <div>
                        <label for="perangkat" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nama Perangkat / Tipe Handphone / Laptop <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="perangkat" id="perangkat" value="{{ old('perangkat') }}" required
                               placeholder="Contoh: iPhone 13 Pro 256GB Sierra Blue"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('perangkat') border-rose-500 @enderror">
                        @error('perangkat')
                            <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kelengkapan -->
                    <div>
                        <label for="kelengkapan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Kelengkapan yang Diserahkan
                        </label>
                        <input type="text" name="kelengkapan" id="kelengkapan" value="{{ old('kelengkapan', 'Unit only') }}"
                               placeholder="Contoh: Unit + Charger original + Dus"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <!-- Keluhan Pelanggan -->
                <div>
                    <label for="keluhan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Keluhan Kerusakan / Masalah <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="keluhan" id="keluhan" rows="3" required
                              placeholder="Jelaskan secara detail kerusakan yang dikeluhkan pelanggan (misal: Layar bergaris hijau setelah jatuh, sentuh sebagian tidak respon)..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('keluhan') border-rose-500 @enderror">{{ old('keluhan') }}</textarea>
                    @error('keluhan')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kondisi Awal Fisik -->
                <div>
                    <label for="kondisi_awal" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Kondisi Fisik Awal Unit Saat Masuk
                    </label>
                    <textarea name="kondisi_awal" id="kondisi_awal" rows="2"
                              placeholder="Catatan lecet, retak kaca belakang, tombol volume keras, dll..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('kondisi_awal') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Teknisi Penanggung Jawab -->
                    <div>
                        <label for="teknisi_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Tugaskan ke Teknisi
                        </label>
                        <select name="teknisi_id" id="teknisi_id"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($teknisiList as $tek)
                                <option value="{{ $tek->id }}" {{ old('teknisi_id') == $tek->id ? 'selected' : '' }}>{{ $tek->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estimasi Biaya -->
                    <div>
                        <label for="estimasi_biaya" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Estimasi Biaya Awal (Rp)
                        </label>
                        <input type="number" name="estimasi_biaya" id="estimasi_biaya" value="{{ old('estimasi_biaya', 0) }}" min="0" step="5000"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <!-- Estimasi Selesai -->
                    <div>
                        <label for="estimasi_selesai" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Estimasi Waktu Selesai
                        </label>
                        <input type="date" name="estimasi_selesai" id="estimasi_selesai" value="{{ old('estimasi_selesai') }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('tiket-servis.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Buat Tiket Servis (Auto Nomor)
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
