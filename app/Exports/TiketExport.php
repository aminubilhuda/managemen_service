<?php

namespace App\Exports;

use App\Models\Perusahaan;
use App\Models\TiketServis;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TiketExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(protected Collection $data) {}

    public function collection(): Collection
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return 'Laporan Tiket Servis';
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Tiket',
            'Tanggal Masuk',
            'Nama Pelanggan',
            'No. Telepon',
            'Perangkat / Tipe',
            'IMEI / Serial No',
            'Keluhan',
            'Status Pengerjaan',
            'Teknisi',
            'Estimasi Biaya (Rp)',
            'Biaya Final (Rp)',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $statusLabel = TiketServis::STATUSES[$row->status] ?? ucfirst(str_replace('_', ' ', $row->status));

        return [
            $this->rowNumber,
            $row->no_tiket,
            $row->created_at ? $row->created_at->format('d/m/Y H:i') : '-',
            $row->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum',
            $row->pelanggan?->no_telp ?? '-',
            $row->perangkat,
            $row->imei_sn ?? '-',
            $row->keluhan,
            $statusLabel,
            $row->teknisi?->name ?? '-',
            (float) ($row->estimasi_biaya ?? 0),
            (float) ($row->biaya_final ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => '#,##0',
            'L' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $perusahaan = Perusahaan::first();
        $namaToko = $perusahaan?->nama_perusahaan ?? 'Cekat Cell';
        $telpToko = $perusahaan?->telp ? 'Telp: '.$perusahaan->telp : '';

        // 1. Header Information Block (Rows 1 to 3)
        $sheet->setCellValue('A1', strtoupper($namaToko));
        $sheet->setCellValue('A2', 'LAPORAN DATA TIKET SERVIS');
        $sheet->setCellValue('A3', 'Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB'.($telpToko ? ' | '.$telpToko : ''));

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
        ]);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9.5, 'color' => ['rgb' => '64748B']],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(10); // Spacer row
        $sheet->getRowDimension(5)->setRowHeight(28); // Table Header row

        // 2. Table Column Headers (Row 5)
        $sheet->getStyle('A5:L5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10.5],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getStyle('D5:F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('K5:L5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $highestRow = $sheet->getHighestRow();

        // 3. Data Rows Styling
        if ($highestRow >= 6) {
            for ($row = 6; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(20);

                // Alternating zebra striping
                $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $bgColor],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Alignment
                $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("K{$row}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Status text color styling
                $statusVal = trim((string) $sheet->getCell("I{$row}")->getValue());
                $statusColor = match (true) {
                    str_contains(strtolower($statusVal), 'selesai') || str_contains(strtolower($statusVal), 'diambil') => '047857',
                    str_contains(strtolower($statusVal), 'dicek') || str_contains(strtolower($statusVal), 'diterima') => '2563EB',
                    str_contains(strtolower($statusVal), 'sparepart') => 'D97706',
                    str_contains(strtolower($statusVal), 'dikerjakan') => '4F46E5',
                    str_contains(strtolower($statusVal), 'batal') => 'DC2626',
                    default => '334155',
                };

                $sheet->getStyle("I{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColor], 'size' => 9.5],
                ]);
            }

            // Grid borders
            $sheet->getStyle("A5:L{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            // 4. Summary / Total Row
            $totalRow = $highestRow + 1;
            $sheet->getRowDimension($totalRow)->setRowHeight(24);
            $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN ('.($highestRow - 5).' TIKET)');
            $sheet->mergeCells("A{$totalRow}:J{$totalRow}");

            $sheet->setCellValue("K{$totalRow}", "=SUM(K6:K{$highestRow})");
            $sheet->setCellValue("L{$totalRow}", "=SUM(L6:L{$highestRow})");

            $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F172A']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '94A3B8'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '0F172A'],
                    ],
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("K{$totalRow}:L{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->freezePane('A6');
        $sheet->setAutoFilter('A5:L5');

        return [];
    }
}
