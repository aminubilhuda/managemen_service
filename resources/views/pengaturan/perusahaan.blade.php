<x-app-layout>
    <x-slot name="title">Identitas Toko & Perusahaan</x-slot>
    <x-slot name="header">Identitas Toko / Nota</x-slot>
    <x-slot name="subtitle">Atur nama brand, alamat, kontak WhatsApp, NPWP, dan logo resmi untuk kop faktur nota</x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('pengaturan.perusahaan.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama_perusahaan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama Perusahaan / Toko Servis <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="telp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nomor Telepon / WhatsApp
                        </label>
                        <input type="text" name="telp" id="telp" value="{{ old('telp', $perusahaan->telp) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <div>
                        <label for="npwp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            NPWP Toko / Usaha
                        </label>
                        <input type="text" name="npwp" id="npwp" value="{{ old('npwp', $perusahaan->npwp) }}"
                               placeholder="Contoh: 01.234.567.8-901.000"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Alamat Workshop / Service Center
                    </label>
                    <textarea name="alamat" id="alamat" rows="3"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('alamat', $perusahaan->alamat) }}</textarea>
                </div>

                <div>
                    <label for="logo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Upload Logo Toko (PNG / JPG)
                    </label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    @if($perusahaan->logo)
                        <div class="mt-3">
                            <img src="{{ asset('storage/' . $perusahaan->logo) }}" alt="Logo Toko" class="h-14 rounded-lg object-contain border border-slate-200 p-1">
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-7 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Simpan Identitas Toko
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
