<x-guest-layout>
    @php
        $namaToko = $appPerusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko');
        $deskripsiToko = $appPerusahaan?->deskripsi ?? 'Pusat Layanan Servis & Point of Sales Terpadu';
        $words = array_values(array_filter(explode(' ', trim($namaToko))));
        $inisial = count($words) >= 2 
            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
            : strtoupper(substr($namaToko, 0, 2));
    @endphp
    <div class="min-h-screen flex">
        <!-- Left Branding Panel (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-12 flex-col justify-between border-r border-slate-800/80">
            <!-- Ambient glow effects -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Top brand header -->
            <div class="relative z-10 flex items-center gap-3.5">
                @if(!empty($appPerusahaan?->logo))
                    <img src="{{ asset('storage/' . $appPerusahaan->logo) }}" alt="{{ $namaToko }}" class="w-11 h-11 rounded-xl object-contain bg-slate-900 p-1 border border-slate-800 shrink-0">
                @else
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-cyan-400 p-0.5 shadow-lg shadow-indigo-500/25 shrink-0">
                        <div class="w-full h-full bg-slate-950/80 backdrop-blur rounded-[10px] flex items-center justify-center font-extrabold text-white text-lg">
                            {{ $inisial }}
                        </div>
                    </div>
                @endif
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                        {{ $namaToko }}
                        <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Enterprise ERP</span>
                    </h1>
                    <p class="text-xs text-slate-400">{{ $deskripsiToko }}</p>
                </div>
            </div>

            <!-- Central Hero Pitch -->
            <div class="relative z-10 my-auto py-12 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700/60 text-xs font-medium text-slate-300 mb-6 backdrop-blur">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Operasional Servis V2.0 Aktif
                </div>
                
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Kelola Servis Handphone & Laptop Lebih Cepat, Rapi & Transparan.
                </h2>
                <p class="mt-4 text-slate-400 text-sm leading-relaxed">
                    Mulai dari pencatatan tiket servis, dokumentasi foto fisik unit, invoice otomatis multi-termin, inventori suku cadang hingga laporan laba rugi dalam satu sistem cerdas.
                </p>

                <!-- Feature highlights -->
                <div class="mt-8 grid grid-cols-2 gap-4">
                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800/80 backdrop-blur">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h4 class="text-xs font-semibold text-white">Tiket & Tanda Terima</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Auto-nomor, cetak tanda terima PDF & timeline servis.</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800/80 backdrop-blur">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-xs font-semibold text-white">POS Kasir & Invoice</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Penjualan produk, suku cadang, PPN & multi-payment.</p>
                    </div>
                </div>
            </div>

            <!-- Footer quote/copyright -->
            <div class="relative z-10 flex items-center justify-between text-xs text-slate-500 border-t border-slate-800/80 pt-4">
                <span>&copy; {{ date('Y') }} {{ $namaToko }} Service ERP</span>
                <span>v2.1.0-release</span>
            </div>
        </div>

        <!-- Right Login Form Panel -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 lg:p-14 relative bg-slate-950">
            <div class="w-full max-w-md">
                <!-- Mobile brand header (shown on mobile only) -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    @if(!empty($appPerusahaan?->logo))
                        <img src="{{ asset('storage/' . $appPerusahaan->logo) }}" alt="{{ $namaToko }}" class="w-10 h-10 rounded-xl object-contain bg-slate-900 p-1 border border-slate-800 shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-cyan-400 p-0.5 shrink-0">
                            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center font-bold text-white text-base">
                                {{ $inisial }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-lg font-bold text-white leading-tight">{{ $namaToko }}</h1>
                        <p class="text-xs text-slate-400">{{ $deskripsiToko }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-white tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-sm text-slate-400 mt-1">Masukkan kredensial akun Anda untuk masuk ke sistem.</p>
                </div>

                <!-- Session Status / Errors -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email atau Username -->
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Email atau Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input id="login" 
                                   type="text" 
                                   name="login" 
                                   value="{{ old('login', old('email', 'admin')) }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="Email atau username akun"
                                   class="block w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   value="password"
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-10 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-colors">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition-colors">
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" checked
                                   class="rounded border-slate-700 bg-slate-900 text-indigo-500 focus:ring-indigo-500/40 focus:ring-offset-slate-950">
                            <span class="ml-2 text-xs text-slate-400">Ingat sesi saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 transition-all duration-200 transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>Masuk ke Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                @if(!app()->isProduction())
                <!-- Quick login shortcuts (Hanya tampil di mode non-production / local) -->
                <div class="mt-8 pt-6 border-t border-slate-800/80">
                    <p class="text-xs font-semibold text-slate-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Akses Cepat Akun Demo (Klik untuk autofill):
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="fillCredentials('admin', 'password')"
                                class="text-left px-3 py-2 rounded-lg bg-slate-900/80 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition text-xs">
                            <div class="font-medium text-slate-200">Super Admin</div>
                            <div class="text-[11px] text-slate-400">Username: admin</div>
                        </button>
                        <button type="button" onclick="fillCredentials('kasir', 'password')"
                                class="text-left px-3 py-2 rounded-lg bg-slate-900/80 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition text-xs">
                            <div class="font-medium text-slate-200">Admin Kasir</div>
                            <div class="text-[11px] text-slate-400">Username: kasir</div>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function fillCredentials(credential, pass) {
            const input = document.getElementById('login') || document.getElementById('email');
            if (input) {
                input.value = credential;
            }
            document.getElementById('password').value = pass;
        }

        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            if (passInput.type === 'password') {
                passInput.type = 'text';
            } else {
                passInput.type = 'password';
            }
        }
    </script>
</x-guest-layout>
