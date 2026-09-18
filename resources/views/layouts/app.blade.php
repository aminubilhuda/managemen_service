<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} — {{ $appPerusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko') }}</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            /* Custom sleek scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            /* Dark sleek scrollbar for sidebar */
            .sidebar-scroll::-webkit-scrollbar {
                width: 4px;
            }
            .sidebar-scroll::-webkit-scrollbar-track {
                background: transparent;
            }
            .sidebar-scroll::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 9999px;
            }
            .sidebar-scroll::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
        </style>

        @stack('styles')
    </head>
    <body class="h-full antialiased text-slate-800 bg-slate-50/80 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex flex-col lg:flex-row">
            <!-- Sidebar Overlay (Mobile) -->
            <div x-show="sidebarOpen" 
                 x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden" 
                 @click="sidebarOpen = false">
            </div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:self-start flex flex-col border-r border-slate-800/80 shadow-2xl lg:shadow-none">

                <!-- Logo & Brand -->
                <div class="p-5 border-b border-slate-800/80 flex items-center justify-between shrink-0">
                    @php
                        $appPerusahaan = $appPerusahaan ?? \App\Models\Perusahaan::first();
                        $namaToko = $appPerusahaan?->nama_perusahaan ?? config('app.name', 'Nama Toko');
                        $words = array_values(array_filter(explode(' ', trim($namaToko))));
                        $inisial = count($words) >= 2 
                            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                            : strtoupper(substr($namaToko, 0, 2));
                    @endphp
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0 flex-1">
                        @if(!empty($appPerusahaan?->logo))
                            <img src="{{ asset('storage/' . $appPerusahaan->logo) }}" alt="{{ $namaToko }}" class="w-10 h-10 rounded-xl object-contain bg-slate-900 p-1 border border-slate-800 shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-cyan-400 p-0.5 shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform shrink-0">
                                <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center font-black text-white text-sm">
                                    {{ $inisial }}
                                </div>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <h1 class="font-extrabold text-white text-sm tracking-tight leading-tight truncate" title="{{ $namaToko }}">
                                    {{ $namaToko }}
                                </h1>
                                <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400 shrink-0"></span>
                            </div>
                            <p class="text-[11px] font-medium text-slate-400 truncate">{{ $appPerusahaan?->deskripsi ?? 'Service Center ERP' }}</p>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 shrink-0 ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-3 space-y-1.5 overflow-y-auto sidebar-scroll">
                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Utama</p>

                    <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                        Dashboard
                    </x-sidebar-link>

                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mt-4 mb-1">Operasional</p>

                    @canany(['tiket.view', 'tiket.create'])
                    <x-sidebar-link href="{{ route('tiket-servis.index') }}" :active="request()->routeIs('tiket-servis.*')" icon="ticket">
                        Tiket Servis
                    </x-sidebar-link>
                    @endcanany

                    @canany(['invoice.view', 'invoice.create'])
                    <x-sidebar-link href="{{ route('invoice.index') }}" :active="request()->routeIs('invoice.*')" icon="receipt">
                        Kasir & Invoice
                    </x-sidebar-link>
                    @endcanany

                    @canany(['pelanggan.view', 'pelanggan.create'])
                    <x-sidebar-link href="{{ route('pelanggan.index') }}" :active="request()->routeIs('pelanggan.*')" icon="users">
                        Data Pelanggan
                    </x-sidebar-link>
                    @endcanany

                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mt-4 mb-1">Inventori & Kas</p>

                    @canany(['produk.view', 'kategori_produk.view'])
                    <x-sidebar-group title="Produk & Sparepart" icon="box" :active="request()->routeIs('produk.*') || request()->routeIs('kategori-produk.*')">
                        <x-sidebar-sublink href="{{ route('produk.index') }}" :active="request()->routeIs('produk.*')">
                            Katalog Suku Cadang
                        </x-sidebar-sublink>
                        <x-sidebar-sublink href="{{ route('kategori-produk.index') }}" :active="request()->routeIs('kategori-produk.*')">
                            Kategori Produk
                        </x-sidebar-sublink>
                    </x-sidebar-group>
                    @endcanany

                    @canany(['pengeluaran.view', 'pengeluaran.create'])
                    <x-sidebar-link href="{{ route('pengeluaran.index') }}" :active="request()->routeIs('pengeluaran.*')" icon="wallet">
                        Pengeluaran Kas
                    </x-sidebar-link>
                    @endcanany

                    @canany(['laporan.view'])
                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mt-4 mb-1">Laporan & Rekap</p>
                    <x-sidebar-group title="Laporan & Rekap" icon="chart-bar" :active="request()->routeIs('laporan.*')">
                        <x-sidebar-sublink href="{{ route('laporan.invoice') }}" :active="request()->routeIs('laporan.invoice')">
                            Laporan Transaksi / Faktur
                        </x-sidebar-sublink>
                        <x-sidebar-sublink href="{{ route('laporan.tiket') }}" :active="request()->routeIs('laporan.tiket')">
                            Laporan Servis Unit
                        </x-sidebar-sublink>
                        <x-sidebar-sublink href="{{ route('laporan.laba-rugi') }}" :active="request()->routeIs('laporan.laba-rugi')">
                            Laba Rugi Finansial
                        </x-sidebar-sublink>
                    </x-sidebar-group>
                    @endcanany

                    @canany(['pengaturan.view', 'user.view'])
                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mt-4 mb-1">Administrasi & Sistem</p>
                    <x-sidebar-group title="Pengaturan Sistem" icon="building" :active="request()->routeIs('pengaturan.*') || request()->routeIs('users.*')">
                        @can('pengaturan.view')
                        <x-sidebar-sublink href="{{ route('pengaturan.perusahaan') }}" :active="request()->routeIs('pengaturan.perusahaan')">
                            Identitas Toko & Nota
                        </x-sidebar-sublink>
                        <x-sidebar-sublink href="{{ route('pengaturan.pajak.index') }}" :active="request()->routeIs('pengaturan.pajak.*')">
                            Tarif Pajak PPN
                        </x-sidebar-sublink>
                        @endcan

                        @can('user.view')
                        <x-sidebar-sublink href="{{ route('users.index') }}" :active="request()->routeIs('users.*')">
                            Hak Akses & User Staf
                        </x-sidebar-sublink>
                        @endcan
                    </x-sidebar-group>
                    @endcanany

                    <p class="px-3 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mt-4 mb-1">Akun Saya</p>
                    <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')" icon="shield">
                        Profil & Sandi
                    </x-sidebar-link>
                </nav>

                <!-- User Footer Card -->
                <div class="p-3 border-t border-slate-800/80 bg-slate-900/40 shrink-0">
                    <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 transition">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 group" title="Buka Profil Saya">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-500 flex items-center justify-center text-white font-bold text-xs shadow-sm group-hover:scale-105 transition-transform shrink-0">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate group-hover:text-cyan-300 transition">{{ Auth::user()->name }}</p>
                                <span class="inline-block text-[9px] px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 font-semibold uppercase">
                                    {{ Auth::user()->roles->first()?->name ?? 'Staff' }}
                                </span>
                            </div>
                        </a>

                        <a href="{{ route('profile.edit') }}" class="p-1.5 rounded-lg text-slate-400 hover:text-cyan-400 hover:bg-slate-800 transition" title="Pengaturan Profil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Logout">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Workspace -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50/60">
                <!-- Top Sticky Navbar -->
                <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:text-slate-950 hover:bg-slate-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <div>
                            @isset($header)
                                <h1 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                                    {{ $header }}
                                </h1>
                            @endisset
                            @isset($subtitle)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                            @endisset
                        </div>
                    </div>

                    <!-- Right Top Actions -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Date Badge -->
                        <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100/80 border border-slate-200/60 text-slate-600 text-xs font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>

                        <!-- Quick Shortcuts -->
                        @can('tiket.create')
                            <a href="{{ route('tiket-servis.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span class="hidden sm:inline">Tiket Baru</span>
                            </a>
                        @endcan

                        @can('invoice.create')
                            <a href="{{ route('invoice.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                <span class="hidden sm:inline">Kasir POS</span>
                            </a>
                        @endcan

                        <!-- User Profile Dropdown -->
                        <div x-data="{ userMenuOpen: false }" class="relative ml-1">
                            <button @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false" type="button" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-600 to-cyan-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="hidden md:inline-block text-xs font-bold text-slate-700 max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="userMenuOpen" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="opacity-100 scale-100" 
                                 x-transition:leave-end="opacity-0 scale-95" 
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-slate-700">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 text-[9px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-semibold uppercase">
                                        {{ Auth::user()->roles->first()?->name ?? 'Staff' }}
                                    </span>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Profil & Sandi Saya
                                    </a>

                                    @can('pengaturan.view')
                                    <a href="{{ route('pengaturan.perusahaan') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        Pengaturan Toko & Nota
                                    </a>
                                    @endcan
                                </div>

                                <div class="border-t border-slate-100 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">
                                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Keluar (Logout)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @isset($headerActions)
                            <div class="flex items-center gap-2 pl-1 border-l border-slate-200">
                                {{ $headerActions }}
                            </div>
                        @endisset
                    </div>
                </header>

                <!-- Flash Notifications -->
                @if(session('success'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-900 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-emerald-700 hover:text-emerald-900 p-1 rounded-lg hover:bg-emerald-500/10 ml-4 transition" aria-label="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-900 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-rose-700 hover:text-rose-900 p-1 rounded-lg hover:bg-rose-500/10 ml-4 transition" aria-label="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4" x-data="{ show: true }" x-show="show">
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-900 px-4 py-3 rounded-xl flex items-start justify-between shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="text-xs sm:text-sm">
                                    <p class="font-bold">Terjadi kesalahan validasi:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-rose-800">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-700 hover:text-rose-900 p-1 rounded-lg hover:bg-rose-500/10 ml-4 transition" aria-label="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Page Content Body -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Toast Notification (for AJAX updates) -->
        <div x-data="toast()" x-on:show-toast.window="addToast($event.detail)" x-cloak
             class="fixed bottom-4 right-4 z-[100] space-y-2">
            <template x-for="t in toasts" :key="t.id">
                <div x-show="t.show"
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                     class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-semibold max-w-sm"
                     :class="t.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800'">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                         :class="t.type === 'success' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'">
                        <template x-if="t.type === 'success'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="t.type === 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                    </div>
                    <span x-text="t.message"></span>
                </div>
            </template>
        </div>

        <script>
            function toast() {
                return {
                    toasts: [],
                    addToast(detail) {
                        const id = Date.now();
                        this.toasts.push({ id, message: detail.message, type: detail.type || 'success', show: true });
                        setTimeout(() => {
                            const idx = this.toasts.findIndex(x => x.id === id);
                            if (idx !== -1) this.toasts[idx].show = false;
                            setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 300);
                        }, 4000);
                    }
                };
            }
        </script>

        @stack('scripts')
    </body>
</html>
