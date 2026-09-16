<x-app-layout>
    <x-slot name="title">Edit Pelanggan — {{ $pelanggan->nama_pelanggan }}</x-slot>
    <x-slot name="header">Edit Pelanggan</x-slot>
    <x-slot name="subtitle">Perbarui rincian kontak dan profil data pelanggan</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('pelanggan.update', $pelanggan) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_pelanggan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama Lengkap Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('nama_pelanggan') border-rose-500 @enderror">
                    @error('nama_pelanggan')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nomor Telepon / WA -->
                    <div>
                        <label for="telp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="telp" id="telp" value="{{ old('telp', $pelanggan->telp) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('telp') border-rose-500 @enderror">
                        @error('telp')
                            <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Alamat Email (Opsional)
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $pelanggan->email) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label for="alamat" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Lengkap (Opsional)
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('alamat') border-rose-500 @enderror">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                    @error('alamat')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pelanggan.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Perbarui Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
