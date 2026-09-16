<?php

namespace App\Exports;

use App\Models\Perusahaan;
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

class InvoiceExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithMapping, WithStyles, WithTitle
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
        return 'Laporan Invoice';
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Invoice',
            'Tanggal Transaksi',
            'Nama Pelanggan',
            'Subtotal (Rp)',
            'Diskon (Rp)',
            'Pajak (Rp)',
            'Total Tagihan (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->no_invoice,
            $row->tanggal_invoice ? $row->tanggal_invoice->format('d/m/Y H:i') : '-',
            $row->tiket?->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum',
            (float) $row->subtotal,
            (float) $row->diskon_nominal,
            (float) $row->pajak_nominal,
            (float) $row->total_tagihan,
            strtoupper($row->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0',
            'F' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $perusahaan = Perusahaan::first();
        $namaToko = $perusahaan?->nama_perusahaan ?? 'Cekat Cell';
        $telpToko = $perusahaan?->telp ? 'Telp: '.$perusahaan->telp : '';

        // 1. Header Information Block (Rows 1 to 3)
        $sheet->setCellValue('A1', strtoupper($namaToko));
        $sheet->setCellValue('A2', 'LAPORAN TRANSAKSI INVOICE');
        $sheet->setCellValue('A3', 'Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB'.($telpToko ? ' | '.$telpToko : ''));

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

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
        $sheet->getStyle('A5:I5')->applyFromArray([
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

        $sheet->getStyle('D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E5:H5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $highestRow = $sheet->getHighestRow();

        // 3. Data Rows Styling
        if ($highestRow >= 6) {
            for ($row = 6; $row <= $highestRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(20);

                // Alternating zebra striping
                $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
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
                $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Status font styling
                $statusVal = strtoupper(trim((string) $sheet->getCell("I{$row}")->getValue()));
                $statusColor = match ($statusVal) {
                    'PAID' => '047857',
                    'PARTIAL' => 'B45309',
                    'UNPAID' => 'B91C1C',
                    'VOID' => '64748B',
                    default => '334155',
                };
                $sheet->getStyle("I{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $statusColor], 'size' => 9.5],
                ]);
            }

            // Grid borders
            $sheet->getStyle("A5:I{$highestRow}")->applyFromArray([
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
            $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');
            $sheet->mergeCells("A{$totalRow}:D{$totalRow}");

            $sheet->setCellValue("E{$totalRow}", "=SUM(E6:E{$highestRow})");
            $sheet->setCellValue("F{$totalRow}", "=SUM(F6:F{$highestRow})");
            $sheet->setCellValue("G{$totalRow}", "=SUM(G6:G{$highestRow})");
            $sheet->setCellValue("H{$totalRow}", "=SUM(H6:H{$highestRow})");

            $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
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
            $sheet->getStyle("E{$totalRow}:H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->freezePane('A6');
        $sheet->setAutoFilter('A5:I5');

        return [];
    }
}
