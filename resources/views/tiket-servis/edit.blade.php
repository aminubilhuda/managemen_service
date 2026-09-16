<x-app-layout>
    <x-slot name="title">Edit Tiket — {{ $tiketServis->no_tiket }}</x-slot>
    <x-slot name="header">Edit Tiket Servis</x-slot>
    <x-slot name="subtitle">Perbarui data perangkat, keluhan, teknisi penanggung jawab, atau estimasi biaya</x-slot>

    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <form method="POST" action="{{ route('tiket-servis.update', $tiketServis) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="pelanggan_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <select name="pelanggan_id" id="pelanggan_id" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                        @foreach($pelangganList as $p)
                            <option value="{{ $p->id }}" {{ old('pelanggan_id', $tiketServis->pelanggan_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_pelanggan }} ({{ $p->telp ?? 'No Telp -' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="perangkat" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Nama Perangkat <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="perangkat" id="perangkat" value="{{ old('perangkat', $tiketServis->perangkat) }}" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <div>
                        <label for="kelengkapan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Kelengkapan Fisik
                        </label>
                        <input type="text" name="kelengkapan" id="kelengkapan" value="{{ old('kelengkapan', $tiketServis->kelengkapan) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div>
                    <label for="keluhan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Keluhan Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="keluhan" id="keluhan" rows="3" required
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('keluhan', $tiketServis->keluhan) }}</textarea>
                </div>

                <div>
                    <label for="kondisi_awal" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Kondisi Fisik Awal Unit
                    </label>
                    <textarea name="kondisi_awal" id="kondisi_awal" rows="2"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('kondisi_awal', $tiketServis->kondisi_awal) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="teknisi_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Teknisi Penanggung Jawab
                        </label>
                        <select name="teknisi_id" id="teknisi_id"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($teknisiList as $tek)
                                <option value="{{ $tek->id }}" {{ old('teknisi_id', $tiketServis->teknisi_id) == $tek->id ? 'selected' : '' }}>{{ $tek->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="estimasi_biaya" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Estimasi Biaya (Rp)
                        </label>
                        <input type="number" name="estimasi_biaya" id="estimasi_biaya" value="{{ old('estimasi_biaya', $tiketServis->estimasi_biaya) }}" min="0" step="5000"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <div>
                        <label for="estimasi_selesai" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Estimasi Selesai
                        </label>
                        <input type="date" name="estimasi_selesai" id="estimasi_selesai" value="{{ old('estimasi_selesai', $tiketServis->estimasi_selesai?->format('Y-m-d')) }}"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('tiket-servis.show', $tiketServis) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all">
                        Perbarui Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
