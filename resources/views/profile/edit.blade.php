<x-app-layout>
    <x-slot name="title">Profil & Keamanan Akun — Cekat Cell</x-slot>
    <x-slot name="header">Profil & Keamanan Akun</x-slot>
    <x-slot name="subtitle">Kelola informasi identitas akun staf, email, dan perbarui kata sandi</x-slot>

    <div class="space-y-6 max-w-4xl">
        <!-- Profile Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Informasi Profil Staf</h2>
                    <p class="text-xs text-slate-500">Perbarui nama lengkap dan alamat surel (email) aktif Anda.</p>
                </div>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Perbarui Kata Sandi</h2>
                    <p class="text-xs text-slate-500">Pastikan akun menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>
                </div>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</x-app-layout>

