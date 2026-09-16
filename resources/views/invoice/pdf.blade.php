<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->no_invoice }}</title>
    <style>
        @page {
            margin: 25px 35px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 19px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .brand-sub {
            font-size: 10px;
            color: #475569;
            line-height: 1.35;
        }
        .inv-title {
            font-size: 30px;
            font-weight: 800;
            color: #24529a;
            text-align: right;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .meta-table {
            width: 240px;
            margin-left: auto;
            margin-right: 0;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 10px;
            line-height: 1.3;
        }
        .meta-label {
            color: #64748b;
            width: 80px;
            text-align: left;
        }
        .meta-val {
            font-weight: bold;
            color: #0f172a;
            text-align: right;
        }

        /* 2-column info */
        .info-table {
            margin-top: 5px;
            margin-bottom: 20px;
            clear: both;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 2px solid #334155;
            padding-bottom: 3px;
            margin-bottom: 6px;
            display: inline-block;
            width: 90%;
        }
        .info-content {
            font-size: 10.5px;
            color: #334155;
            line-height: 1.4;
        }

        /* Items table */
        .items-table {
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #24529a;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            padding: 8px 10px;
            border: none;
        }
        .items-table td {
            padding: 9px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
            color: #0f172a;
        }
        .item-warranty {
            font-size: 9px;
            color: #64748b;
            font-style: italic;
            margin-top: 2px;
        }

        /* Footer section */
        .footer-table {
            margin-top: 15px;
        }
        .footer-table td {
            vertical-align: top;
        }
        .ket-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .ket-content {
            font-size: 10px;
            color: #475569;
            line-height: 1.45;
            padding-right: 25px;
        }

        .calc-table {
            width: 100%;
        }
        .calc-table td {
            padding: 2.5px 0;
            font-size: 10.5px;
        }
        .calc-label {
            color: #475569;
        }
        .calc-val {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .calc-total {
            border-top: 2px solid #0f172a;
            margin-top: 4px;
            padding-top: 5px;
            font-size: 13px;
            font-weight: 800;
        }

        .date-center {
            margin-top: 35px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
    </style>
</head>
<body>
    @php
        $totalDibayar = $invoice->pembayaran->sum('jumlah_dibayar');
        $sisaTagihan = max(0, $invoice->total_tagihan - $totalDibayar);
    @endphp

    <!-- Header -->
    @php
        $perusahaan = $invoice->perusahaan ?? \App\Models\Perusahaan::first();
        $logoBase64 = null;
        if (!empty($perusahaan?->logo)) {
            $logoPath = null;
            if (file_exists(public_path('storage/' . $perusahaan->logo))) {
                $logoPath = public_path('storage/' . $perusahaan->logo);
            } elseif (file_exists(storage_path('app/public/' . $perusahaan->logo))) {
                $logoPath = storage_path('app/public/' . $perusahaan->logo);
            }

            if ($logoPath && is_readable($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoMime = mime_content_type($logoPath) ?: 'image/png';
                $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
            }
        }
    @endphp
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <table style="width: auto;">
                    <tr>
                        <td style="vertical-align: top; padding-right: 12px; width: 55px;">
                            @if($logoBase64)
                                <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 52px; max-width: 140px; object-fit: contain;">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" viewBox="0 0 100 100">
                                    <polygon points="50,5 90,26 90,74 50,95 10,74 10,26" fill="#1e4e8c" />
                                    <polygon points="50,14 82,31 82,69 50,86 18,69 18,31" fill="#2563eb" />
                                    <circle cx="50" cy="50" r="22" fill="#ffffff" />
                                    <circle cx="50" cy="50" r="13" fill="#1e3a8a" />
                                </svg>
                            @endif
                        </td>
                        <td style="vertical-align: top;">
                            <div class="brand-title">{{ $perusahaan?->nama_perusahaan ?? 'Cekat Cell' }}</div>
                            <div class="brand-sub">{{ $perusahaan?->alamat ?? 'Jl. Kebangsaan, No. 45, Jakarta Utara' }}</div>
                            <div class="brand-sub">{{ $perusahaan?->telp ?? '088898888' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <div class="inv-title">Invoice</div>
                <table class="meta-table" align="right">
                    <tr>
                        <td class="meta-label">Referensi</td>
                        <td class="meta-val">{{ $invoice->no_invoice }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal</td>
                        <td class="meta-val">{{ $invoice->tanggal_invoice->format('d/m/Y, H.i.s') }}</td>
                    </tr>
                    @if($invoice->tiket)
                        <tr>
                            <td class="meta-label">No. Tiket</td>
                            <td class="meta-val">{{ $invoice->tiket->no_tiket }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-val">{{ strtoupper($invoice->status) }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Garansi</td>
                        <td class="meta-val">
                            {{ $invoice->tiket?->garansi_sampai ? 's/d ' . $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : ($invoice->tiket ? 's/d 30 hari ke depan' : '-') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 1px solid #cbd5e1; margin-top: 15px; margin-bottom: 18px; clear: both;"></div>

    <!-- 2 Column: Info Perusahaan & Tagihan Untuk -->
    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Info Perusahaan</div>
                <div class="info-content">
                    <strong style="font-size: 11px; display: block; margin-bottom: 2px;">{{ $invoice->perusahaan?->nama_perusahaan ?? 'Cekat Cell' }}</strong>
                    <div>{{ $invoice->perusahaan?->alamat ?? 'Jl. Kebangsaan, No. 45, Jakarta Utara' }}</div>
                    <div>Telp: {{ $invoice->perusahaan?->telp ?? '088898888' }}</div>
                </div>
            </td>
            <td>
                <div class="section-title">Tagihan Untuk</div>
                <div class="info-content">
                    <strong style="font-size: 11px; display: block; margin-bottom: 2px;">{{ $invoice->tiket?->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum' }}</strong>
                    <div>{{ $invoice->tiket?->pelanggan?->alamat ?? 'Jl. Veteran No. 5, Karawang' }}</div>
                    <div>Telp: {{ $invoice->tiket?->pelanggan?->telp ?? '-' }}</div>
                    @if($invoice->tiket)
                        <div><strong>Perangkat:</strong> {{ $invoice->tiket->perangkat }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Table of Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="text-align: left; width: 28%;">Produk</th>
                <th style="text-align: left; width: 34%;">Deskripsi</th>
                <th style="text-align: center; width: 8%;">Qty</th>
                <th style="text-align: right; width: 15%;">Harga</th>
                <th style="text-align: right; width: 15%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->detail as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->produk?->nama_produk ?? $item->deskripsi }}</div>
                        @if($item->produk && $item->produk->tipe === 'sparepart')
                            <div class="item-warranty">
                                (Garansi Part s/d {{ $invoice->tiket?->garansi_sampai ? $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : now()->addDays(30)->translatedFormat('d F Y') }})
                            </div>
                        @endif
                    </td>
                    <td style="color: #475569;">{{ $item->deskripsi }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->qty }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #0f172a;">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer Summary Area -->
    <table class="footer-table">
        <tr>
            <td style="width: 55%;">
                <div class="ket-title">Keterangan</div>
                <div class="ket-content">
                    <p style="margin: 0 0 4px 0;">{{ $invoice->keterangan ?: 'Terima kasih telah mempercayakan service kepada kami.' }}</p>
                    <p style="margin: 0 0 4px 0;">- Syarat Garansi: Tidak boleh merusak segel</p>
                    <p style="margin: 0;">Garansi service berlaku sampai {{ $invoice->tiket?->garansi_sampai ? $invoice->tiket->garansi_sampai->translatedFormat('d F Y') : now()->addDays(30)->translatedFormat('d F Y') }}.</p>
                </div>
            </td>
            <td style="width: 45%;">
                <table class="calc-table">
                    <tr>
                        <td class="calc-label">Total Biaya</td>
                        <td class="calc-val">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->diskon_nominal > 0)
                        <tr>
                            <td class="calc-label" style="color: #059669;">Diskon</td>
                            <td class="calc-val" style="color: #059669;">- Rp {{ number_format($invoice->diskon_nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($invoice->pajak_nominal > 0)
                        <tr>
                            <td class="calc-label">{{ $invoice->pajak?->nama_pajak ?? 'Pajak' }} ({{ $invoice->pajak_persen }}%)</td>
                            <td class="calc-val">+ Rp {{ number_format($invoice->pajak_nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="calc-label">Telah Dibayar</td>
                        <td class="calc-val">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="calc-label">Status</td>
                        <td class="calc-val" style="text-transform: uppercase;">{{ strtoupper($invoice->status) }}</td>
                    </tr>
                    <tr class="calc-total">
                        <td style="font-weight: bold; padding-top: 6px;">Sisa Tagihan</td>
                        <td style="text-align: right; font-weight: 800; font-size: 13px; padding-top: 6px;">
                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Bottom Center Date -->
    <div class="date-center">
        {{ $invoice->tanggal_invoice->translatedFormat('d F Y') }}
    </div>
</body>
</html>
