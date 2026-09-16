<x-app-layout>
    <x-slot name="title">Manajemen User</x-slot>
    <x-slot name="header">Manajemen User & Hak Akses</x-slot>
    <x-slot name="subtitle">Kelola staf, teknisi, kasir, owner, dan perizinan sistem ERP</x-slot>

    <x-slot name="headerActions">
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah User Baru
        </a>
    </x-slot>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                    <tr>
                        <th class="px-6 py-3.5">Nama Staf</th>
                        <th class="px-6 py-3.5">Email Akun</th>
                        <th class="px-6 py-3.5">Peran / Role</th>
                        <th class="px-6 py-3.5">Terdaftar Sejak</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center text-xs border border-indigo-100 flex-shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                        @if($u->username)
                                            <div class="text-[11px] text-indigo-600 font-medium">@<span>{{ $u->username }}</span></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $u->email }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $role = $u->roles->first()?->name ?? 'User';
                                    $roleBadges = [
                                        'super_admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'admin_kasir' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'teknisi' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'owner' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border uppercase {{ $roleBadges[$role] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $u->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('users.edit', $u) }}" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Akun">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if(Auth::id() !== $u->id)
                                        <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Yakin ingin menghapus user ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
