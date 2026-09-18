<x-app-layout>
    <x-slot name="title">Tambah User Baru</x-slot>
    <x-slot name="header">Tambah User Staf / Teknisi</x-slot>
    <x-slot name="subtitle">Pendaftaran akun pengguna baru dan penetapan peran hak akses</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Rian Teknisi"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Username <span class="text-slate-400 font-normal">(Opsional - dibuat otomatis jika dikosongkan)</span>
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           placeholder="Contoh: rian_teknisi"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('username') border-rose-500 @enderror">
                    @error('username')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           placeholder="staf@email.com"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Peran & Hak Akses (Role) <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>
                                {{ strtoupper(str_replace('_', ' ', $r->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Password Akun <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                           placeholder="Minimal 8 karakter"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
