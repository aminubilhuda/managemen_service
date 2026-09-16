<x-app-layout>
    <x-slot name="title">Tambah Tarif Pajak</x-slot>
    <x-slot name="header">Tambah Tarif Pajak Baru</x-slot>
    <x-slot name="subtitle">Pencatatan persentase tarif PPN baru</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('pengaturan.pajak.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="nama_pajak" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Nama Pajak <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_pajak" id="nama_pajak" value="{{ old('nama_pajak', 'PPN') }}" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="persentase" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Persentase (%) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="persentase" id="persentase" value="{{ old('persentase', 11) }}" required min="0" max="100" step="0.1"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-slate-400">%</span>
                        </div>
                    </div>

                    <div>
                        <label for="berlaku_mulai" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Berlaku Mulai Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="berlaku_mulai" id="berlaku_mulai" value="{{ old('berlaku_mulai', date('Y-m-d')) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div x-data="{ tipeAturan: '{{ old('tipe_aturan', 'semua') }}' }" class="space-y-4">
                    <div>
                        <label for="tipe_aturan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Syarat & Kondisi Penggunaan Pajak <span class="text-rose-500">*</span>
                        </label>
                        <select name="tipe_aturan" id="tipe_aturan" x-model="tipeAturan" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="semua">Semua Transaksi (Tanpa Batas Nilai Nominal)</option>
                            <option value="diatas_nominal">Khusus Transaksi di Atas / Sama Dengan Batas (≥ Nominal)</option>
                            <option value="dibawah_nominal">Khusus Transaksi di Bawah Batas (&lt; Nominal)</option>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Pilih apakah pajak ini berlaku umum untuk semua invoice atau otomatis aktif jika memenuhi batas nominal tertentu.
                        </p>
                    </div>

                    <div x-show="tipeAturan !== 'semua'" x-cloak class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <label for="nominal_batas" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Batas Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="nominal_batas" id="nominal_batas" value="{{ old('nominal_batas', 2000000) }}" min="0" step="50000"
                                   placeholder="2000000"
                                   class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        </div>
                        <p class="text-[11px] text-slate-500" x-show="tipeAturan === 'diatas_nominal'">
                            Tarif ini akan otomatis dipilih oleh sistem jika subtotal invoice <strong>≥ nominal di atas</strong> (misal: ≥ Rp 2.000.000).
                        </p>
                        <p class="text-[11px] text-slate-500" x-show="tipeAturan === 'dibawah_nominal'">
                            Tarif ini akan otomatis dipilih oleh sistem jika subtotal invoice <strong>&lt; nominal di atas</strong> (misal: &lt; Rp 2.000.000).
                        </p>
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="aktif" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-700">Aktifkan tarif pajak ini (otomatis menggantikan tarif lain yang berkondisi sama)</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('pengaturan.pajak.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Simpan Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
