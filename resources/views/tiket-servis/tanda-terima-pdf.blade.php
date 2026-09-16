<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tanda Terima Servis — {{ $tiketServis->no_tiket }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
        }
        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .brand-sub {
            font-size: 9px;
            color: #64748b;
        }
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            color: #4338ca;
        }
        .receipt-no {
            font-size: 11px;
            font-family: monospace;
            text-align: right;
            color: #0f172a;
        }
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-label {
            width: 25%;
            font-weight: bold;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-val {
            width: 75%;
            border: 1px solid #e2e8f0;
        }
        .terms {
            margin-top: 15px;
            padding: 8px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            font-size: 9px;
            color: #64748b;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
        }
        .signatures td {
            text-align: center;
            width: 50%;
        }
        .sig-line {
            margin-top: 50px;
            border-bottom: 1px solid #0f172a;
            width: 150px;
            display: inline-block;
        }
    </style>
</head>
<body>
    @php
        $perusahaan = $perusahaan ?? \App\Models\Perusahaan::first();
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
    <div class="header">
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <table style="width: auto;">
                        <tr>
                            @if($logoBase64)
                                <td style="vertical-align: top; padding-right: 10px; width: 50px;">
                                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 44px; max-width: 120px; object-fit: contain;">
                                </td>
                            @endif
                            <td style="vertical-align: top;">
                                <div class="brand-title">{{ $perusahaan->nama_perusahaan ?? 'Cekat Cell' }}</div>
                                <div class="brand-sub">{{ $perusahaan->alamat ?? 'Jl. Raya Servis No. 1' }} | Telp: {{ $perusahaan->telp ?? $perusahaan->telepon ?? '08123456789' }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <div class="receipt-title">TANDA TERIMA SERVIS</div>
                    <div class="receipt-no">NO: {{ $tiketServis->no_tiket }}</div>
                    <div class="brand-sub" style="text-align: right;">{{ $tiketServis->created_at->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Pelanggan</td>
            <td class="info-val"><strong>{{ $tiketServis->pelanggan->nama_pelanggan }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">No. Telepon / WA</td>
            <td class="info-val">{{ $tiketServis->pelanggan->telp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Unit Perangkat</td>
            <td class="info-val"><strong>{{ $tiketServis->perangkat }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Kelengkapan</td>
            <td class="info-val">{{ $tiketServis->kelengkapan ?: 'Unit only' }}</td>
        </tr>
        <tr>
            <td class="info-label">Keluhan Kerusakan</td>
            <td class="info-val">{{ $tiketServis->keluhan }}</td>
        </tr>
        <tr>
            <td class="info-label">Kondisi Fisik Awal</td>
            <td class="info-val">{{ $tiketServis->kondisi_awal ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Estimasi Biaya</td>
            <td class="info-val">
                <strong>{{ $tiketServis->estimasi_biaya ? 'Rp ' . number_format($tiketServis->estimasi_biaya, 0, ',', '.') : 'Menunggu Konfirmasi Pengecekan' }}</strong>
            </td>
        </tr>
    </table>

    <div class="terms">
        <strong>Ketentuan Pengambilan Unit:</strong>
        <ol style="margin: 3px 0 0 15px; padding: 0;">
            <li>Wajib membawa lembar tanda terima fisik ini saat pengambilan unit.</li>
            <li>Barang yang tidak diambil lebih dari 30 hari setelah pemberitahuan selesai, resiko kehilangan di luar tanggung jawab kami.</li>
            <li>Garansi servis berlaku sesuai nota resmi perbaikan. Kerusakan akibat human error (jatuh/kena air) membatalkan garansi.</li>
        </ol>
    </div>

    <table class="signatures">
        <tr>
            <td>
                Pelanggan,
                <br><br><br>
                <div class="sig-line"></div>
                <div>( {{ $tiketServis->pelanggan->nama_pelanggan }} )</div>
            </td>
            <td>
                Petugas Penerima,
                <br><br><br>
                <div class="sig-line"></div>
                <div>( {{ $tiketServis->teknisi?->name ?? 'Front Office Cekat Cell' }} )</div>
            </td>
        </tr>
    </table>
</body>
</html>
