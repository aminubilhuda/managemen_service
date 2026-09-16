<x-app-layout>
    <x-slot name="title">Buat Invoice Baru</x-slot>
    <x-slot name="header">Buat Invoice Penjualan / Servis</x-slot>
    <x-slot name="subtitle">Penerbitan tagihan biaya jasa servis, suku cadang, dan aksesoris</x-slot>

    <div x-data="invoiceForm({{ json_encode($pajakList) }}, {{ json_encode($produkList) }})" class="max-w-5xl">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm mb-1.5">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Gagal menerbitkan invoice. Mohon periksa isian berikut:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 ml-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoice.store') }}">
            @csrf
            <input type="hidden" name="pajak_id" :value="selectedPajakId">
            <input type="hidden" name="pajak_persen" :value="pajakPersen">

            <!-- Header Card: Tiket Link -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tiket_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Hubungkan ke Tiket Servis (Opsional)
                        </label>
                        <select name="tiket_id" id="tiket_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                            <option value="">-- Bukan dari Tiket (Penjualan Bebas) --</option>
                            @foreach($tiketList as $t)
                                <option value="{{ $t->id }}" {{ (old('tiket_id', $tiket?->id) == $t->id) ? 'selected' : '' }}>
                                    {{ $t->no_tiket }} — {{ $t->perangkat }} ({{ $t->pelanggan?->nama_pelanggan ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih nomor tiket servis pelanggan atau biarkan kosong untuk penjualan langsung di kasir.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            Tanggal Invoice
                        </label>
                        <input type="text" readonly value="{{ now()->translatedFormat('d F Y') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm bg-slate-50 text-slate-600 font-medium">
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base">Rincian Jasa & Suku Cadang</h4>
                    <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold text-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Baris Item
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/40 relative space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-500 uppercase" x-text="'Item #' + (index + 1)"></span>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <!-- Quick Product Select -->
                                    <div class="md:col-span-4">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Produk / Suku Cadang</label>
                                        <select :name="'items[' + index + '][produk_id]'" x-model="item.produk_id" @change="onProductSelect(index)" class="w-full rounded-xl border border-slate-200 py-2 px-3 text-xs">
                                            <option value="">-- Custom / Manual (Bukan Katalog) --</option>
                                            <template x-for="p in products" :key="p.id">
                                                <option :value="p.id" x-text="p.nama_produk + (p.tipe === 'sparepart' ? ' (Stok: ' + (p.stok ?? 0) + ')' : ' (Jasa)')"></option>
                                            </template>
                                        </select>
                                    </div>


                                    <!-- Deskripsi -->
                                    <div class="md:col-span-4">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Deskripsi Item <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="'items[' + index + '][deskripsi]'" x-model="item.deskripsi" required placeholder="Nama jasa / sparepart..." class="w-full rounded-xl border border-slate-200 py-2 px-3 text-xs">
                                    </div>

                                    <!-- Qty -->
                                    <div class="md:col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Qty</label>
                                        <input type="number" :name="'items[' + index + '][qty]'" x-model.number="item.qty" min="1" required class="w-full rounded-xl border border-slate-200 py-2 px-2 text-xs text-center font-bold">
                                    </div>

                                    <!-- Harga Satuan -->
                                    <div class="md:col-span-3">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Harga Satuan (Rp)</label>
                                        <input type="number" :name="'items[' + index + '][harga_satuan]'" x-model.number="item.harga_satuan" min="0" step="500" required class="w-full rounded-xl border border-slate-200 py-2 px-3 text-xs font-bold text-right">
                                        <!-- Hidden Modal Satuan for HPP -->
                                        <input type="hidden" :name="'items[' + index + '][harga_modal_satuan]'" :value="item.harga_modal_satuan">
                                    </div>
                                </div>

                                <div class="text-right text-xs font-bold text-slate-700 pt-1">
                                    Subtotal Baris: <span class="text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.qty * item.harga_satuan)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Financial Calculation Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div></div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-medium">Subtotal Item:</span>
                        <span class="font-bold text-slate-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateSubtotal())"></span>
                    </div>

                    <div class="flex items-center justify-between text-sm gap-4">
                        <span class="text-slate-500 font-medium">Diskon Potongan (Rp):</span>
                        <input type="number" name="diskon" x-model.number="diskon" min="0" step="1000" class="w-40 rounded-xl border border-slate-200 py-1.5 px-3 text-sm text-right font-bold">
                    </div>

                    <!-- Dynamic Tax Calculation Section -->
                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between text-sm gap-3">
                            <span class="text-slate-500 font-medium">Pajak Faktur:</span>
                            <div class="flex items-center gap-2">
                                <select @change="onTaxSelect($event)" class="rounded-xl border border-slate-200 py-1.5 px-3 text-xs font-bold text-slate-800 bg-slate-50 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="auto" :selected="isPajakAuto">⚡ Otomatis Sesuai Nominal</option>
                                    <template x-for="tax in taxes" :key="tax.id">
                                        <option :value="tax.id" :selected="!isPajakAuto && selectedPajakId == tax.id" x-text="tax.nama_pajak + ' (' + parseFloat(tax.persentase) + '%)'"></option>
                                    </template>
                                    <option value="0" :selected="!isPajakAuto && selectedPajakId === '' && pajakPersen === 0">Bebas Pajak (0%)</option>
                                </select>
                                <span class="text-xs font-black text-indigo-600 w-12 text-right" x-text="pajakPersen + '%'"></span>
                            </div>
                        </div>

                        <!-- Dynamic Explanation Badge -->
                        <div class="flex justify-end" x-show="pajakKeterangan">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-lg border transition-all"
                                  :class="pajakPersen > 5 ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-amber-50 text-amber-700 border-amber-200'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="pajakPersen > 5 ? 'bg-indigo-500' : 'bg-amber-500'"></span>
                                <span x-text="namaPajak + ' (' + pajakPersen + '%) — ' + pajakKeterangan"></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Nominal Pajak:</span>
                            <span class="font-bold text-slate-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTaxAmount())"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-base sm:text-lg">
                        <span class="font-black text-slate-900">Total Tagihan Final:</span>
                        <span class="font-black text-2xl text-indigo-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(calculateTotal())"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('invoice.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-sm font-bold shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95">
                    Terbitkan Invoice & Potong Stok
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function invoiceForm(taxesList, productsList) {
            return {
                taxes: taxesList || [],
                products: productsList || [],
                diskon: 0,
                selectedPajakId: '',
                pajakPersen: 0,
                namaPajak: 'Pajak',
                isPajakAuto: true,
                pajakKeterangan: '',
                items: [
                    { produk_id: '', deskripsi: '', qty: 1, harga_satuan: 0, harga_modal_satuan: 0 }
                ],
                init() {
                    this.updateTaxAutomatic();
                },
                addItem() {
                    this.items.push({ produk_id: '', deskripsi: '', qty: 1, harga_satuan: 0, harga_modal_satuan: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                onProductSelect(index) {
                    const selectedId = this.items[index].produk_id;
                    const prod = this.products.find(p => p.id == selectedId);
                    if (prod) {
                        this.items[index].deskripsi = prod.nama_produk;
                        this.items[index].harga_satuan = parseFloat(prod.harga_jual) || 0;
                        this.items[index].harga_modal_satuan = parseFloat(prod.harga_modal) || 0;
                    }
                },
                calculateSubtotal() {
                    return this.items.reduce((sum, item) => sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.harga_satuan) || 0)), 0);
                },
                netSubtotal() {
                    return Math.max(0, this.calculateSubtotal() - (parseFloat(this.diskon) || 0));
                },
                updateTaxAutomatic() {
                    if (!this.isPajakAuto) {
                        return;
                    }

                    const net = this.netSubtotal();

                    // 1. Cek diatas_nominal (net >= nominal_batas)
                    const diatas = this.taxes
                        .filter(t => t.tipe_aturan === 'diatas_nominal' && net >= parseFloat(t.nominal_batas))
                        .sort((a, b) => parseFloat(b.nominal_batas) - parseFloat(a.nominal_batas))[0];

                    if (diatas) {
                        this.selectedPajakId = diatas.id;
                        this.pajakPersen = parseFloat(diatas.persentase);
                        this.namaPajak = diatas.nama_pajak;
                        this.pajakKeterangan = `Otomatis: Subtotal ≥ Rp ${new Intl.NumberFormat('id-ID').format(diatas.nominal_batas)}`;
                        return;
                    }

                    // 2. Cek dibawah_nominal (net < nominal_batas)
                    const dibawah = this.taxes
                        .filter(t => t.tipe_aturan === 'dibawah_nominal' && net < parseFloat(t.nominal_batas))
                        .sort((a, b) => parseFloat(a.nominal_batas) - parseFloat(b.nominal_batas))[0];

                    if (dibawah) {
                        this.selectedPajakId = dibawah.id;
                        this.pajakPersen = parseFloat(dibawah.persentase);
                        this.namaPajak = dibawah.nama_pajak;
                        this.pajakKeterangan = `Otomatis: Subtotal < Rp ${new Intl.NumberFormat('id-ID').format(dibawah.nominal_batas)}`;
                        return;
                    }

                    // 3. Fallback aturan umum 'semua'
                    const semua = this.taxes.find(t => t.tipe_aturan === 'semua');
                    if (semua) {
                        this.selectedPajakId = semua.id;
                        this.pajakPersen = parseFloat(semua.persentase);
                        this.namaPajak = semua.nama_pajak;
                        this.pajakKeterangan = 'Tarif Umum';
                        return;
                    }

                    // Fallback pertama
                    if (this.taxes.length > 0) {
                        this.selectedPajakId = this.taxes[0].id;
                        this.pajakPersen = parseFloat(this.taxes[0].persentase);
                        this.namaPajak = this.taxes[0].nama_pajak;
                        this.pajakKeterangan = '';
                    } else {
                        this.selectedPajakId = '';
                        this.pajakPersen = 0;
                        this.namaPajak = 'Pajak';
                        this.pajakKeterangan = '';
                    }
                },
                onTaxSelect(e) {
                    const val = e.target.value;
                    if (val === 'auto') {
                        this.isPajakAuto = true;
                        this.updateTaxAutomatic();
                    } else if (val === '0') {
                        this.isPajakAuto = false;
                        this.selectedPajakId = '';
                        this.pajakPersen = 0;
                        this.namaPajak = 'Bebas Pajak';
                        this.pajakKeterangan = 'Manual: Bebas Pajak';
                    } else {
                        this.isPajakAuto = false;
                        const found = this.taxes.find(t => t.id == val);
                        if (found) {
                            this.selectedPajakId = found.id;
                            this.pajakPersen = parseFloat(found.persentase);
                            this.namaPajak = found.nama_pajak;
                            this.pajakKeterangan = 'Manual: Dipilih pengguna';
                        }
                    }
                },
                calculateTaxAmount() {
                    this.updateTaxAutomatic();
                    return this.netSubtotal() * ((parseFloat(this.pajakPersen) || 0) / 100);
                },
                calculateTotal() {
                    return Math.round(this.netSubtotal() + this.calculateTaxAmount());
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
