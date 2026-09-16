<?php

namespace App\Exports;

use App\Models\DetailInvoice;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LabaRugiExport implements FromArray, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithStyles, WithTitle
{
    private array $sectionHeaderRows = [];

    private array $subtotalRows = [];

    private ?int $grossProfitRow = null;

    private ?int $netProfitRow = null;

    private bool $isNetProfitPositive = true;

    public function __construct(protected string $bulan) {}

    public function startCell(): string
    {
        return 'A5';
    }

    public function title(): string
    {
        return 'Laba Rugi';
    }

    public function headings(): array
    {
        return [
            'Pos Keuangan / Deskripsi Akun',
            'Rincian (Rp)',
            'Jumlah Total (Rp)',
        ];
    }

    public function array(): array
    {
        $start = Carbon::parse($this->bulan)->startOfMonth();
        $end = Carbon::parse($this->bulan)->endOfMonth();

        $pendapatan = (float) DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
            $q->whereIn('status', ['paid', 'partial'])->whereBetween('tanggal_invoice', [$start, $end]);
        })->sum('jumlah');

        $hpp = (float) DetailInvoice::whereHas('invoice', function ($q) use ($start, $end) {
            $q->whereIn('status', ['paid', 'partial'])->whereBetween('tanggal_invoice', [$start, $end]);
        })->sum(DB::raw('qty * harga_modal_satuan'));

        $labaKotor = $pendapatan - $hpp;

        $beban = Pengeluaran::whereBetween('tanggal', [$start, $end])
            ->select('kategori_pengeluaran', DB::raw('SUM(jumlah) as total'))
            ->groupBy('kategori_pengeluaran')
            ->get();

        $totalBeban = (float) $beban->sum('total');
        $labaBersih = $labaKotor - $totalBeban;
        $this->isNetProfitPositive = ($labaBersih >= 0);

        $ppn = (float) Invoice::whereIn('status', ['paid', 'partial'])
            ->whereBetween('tanggal_invoice', [$start, $end])
            ->sum('pajak_nominal');

        $rows = [];
        $currentRow = 6; // Data starts at row 6 (row 5 is table headings)

        // 1. PENDAPATAN OPERASIONAL
        $this->sectionHeaderRows[] = $currentRow;
        $rows[] = ['1. PENDAPATAN OPERASIONAL', '', ''];
        $currentRow++;

        $rows[] = ['    Pendapatan Jasa Servis & Penjualan Unit/Sparepart', $pendapatan, ''];
        $currentRow++;

        $this->subtotalRows[] = $currentRow;
        $rows[] = ['TOTAL PENDAPATAN OPERASIONAL', '', $pendapatan];
        $currentRow++;

        $rows[] = ['', '', '']; // Spacer
        $currentRow++;

        // 2. HARGA POKOK PENJUALAN (HPP)
        $this->sectionHeaderRows[] = $currentRow;
        $rows[] = ['2. HARGA POKOK PENJUALAN (HPP)', '', ''];
        $currentRow++;

        $rows[] = ['    Harga Modal Sparepart & Komponen Terpakai', $hpp, ''];
        $currentRow++;

        $this->subtotalRows[] = $currentRow;
        $rows[] = ['TOTAL HARGA POKOK PENJUALAN (HPP)', '', $hpp];
        $currentRow++;

        $rows[] = ['', '', '']; // Spacer
        $currentRow++;

        // 3. LABA KOTOR (GROSS PROFIT)
        $this->grossProfitRow = $currentRow;
        $rows[] = ['LABA KOTOR (GROSS PROFIT)', '', $labaKotor];
        $currentRow++;

        $rows[] = ['', '', '']; // Spacer
        $currentRow++;

        // 4. BEBAN OPERASIONAL
        $this->sectionHeaderRows[] = $currentRow;
        $rows[] = ['3. BEBAN BIAYA OPERASIONAL', '', ''];
        $currentRow++;

        if ($beban->isEmpty()) {
            $rows[] = ['    Tidak ada beban operasional tercatat pada periode ini', 0, ''];
            $currentRow++;
        } else {
            foreach ($beban as $b) {
                $rows[] = ['    Biaya '.ucwords(str_replace('_', ' ', $b->kategori_pengeluaran)), (float) $b->total, ''];
                $currentRow++;
            }
        }

        $this->subtotalRows[] = $currentRow;
        $rows[] = ['TOTAL BEBAN BIAYA OPERASIONAL', '', $totalBeban];
        $currentRow++;

        $rows[] = ['', '', '']; // Spacer
        $currentRow++;

        // 5. LABA BERSIH (NET PROFIT / LOSS)
        $this->netProfitRow = $currentRow;
        $labelBersih = $this->isNetProfitPositive ? 'LABA BERSIH (NET PROFIT)' : 'RUGI BERSIH (NET LOSS)';
        $rows[] = [$labelBersih, '', $labaBersih];
        $currentRow++;

        $rows[] = ['', '', '']; // Spacer
        $currentRow++;

        // 6. INFORMASI PERPAJAKAN
        $this->sectionHeaderRows[] = $currentRow;
        $rows[] = ['4. INFORMASI PERPAJAKAN (PPN / PPh TERPUNGUT)', '', ''];
        $currentRow++;

        $rows[] = ['    Total Pajak Tercatat dari Transaksi Invoice', $ppn, ''];
        $currentRow++;

        return $rows;
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0',
            'C' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $perusahaan = Perusahaan::first();
        $namaToko = $perusahaan?->nama_perusahaan ?? 'Cekat Cell';
        $start = Carbon::parse($this->bulan)->startOfMonth();

        // 1. Company and Report Header (Rows 1 to 3)
        $sheet->setCellValue('A1', strtoupper($namaToko));
        $sheet->setCellValue('A2', 'LAPORAN LABA RUGI KOMPREHENSIF');
        $sheet->setCellValue('A3', 'Periode: '.$start->translatedFormat('F Y').' | Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB');

        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');

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
        $sheet->getRowDimension(4)->setRowHeight(10); // Spacer
        $sheet->getRowDimension(5)->setRowHeight(28); // Table Header

        // 2. Table Column Headers (Row 5)
        $sheet->getStyle('A5:C5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10.5],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $highestRow = $sheet->getHighestRow();

        // 3. Style data rows
        for ($row = 6; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(21);
            $sheet->getStyle("A{$row}:C{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Section Headers Styling
        foreach ($this->sectionHeaderRows as $sRow) {
            $sheet->getStyle("A{$sRow}:C{$sRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1E293B'], 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($sRow)->setRowHeight(23);
        }

        // Subtotal Rows Styling
        foreach ($this->subtotalRows as $subRow) {
            $sheet->getStyle("A{$subRow}:C{$subRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }

        // Laba Kotor (Gross Profit) Highlight
        if ($this->grossProfitRow) {
            $gp = $this->grossProfitRow;
            $sheet->getStyle("A{$gp}:C{$gp}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '065F46'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D1FAE5'], // Soft emerald green
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '10B981'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '10B981'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($gp)->setRowHeight(24);
        }

        // Laba Bersih (Net Profit) Banner
        if ($this->netProfitRow) {
            $np = $this->netProfitRow;
            $bgColor = $this->isNetProfitPositive ? '047857' : 'DC2626';
            $sheet->getStyle("A{$np}:C{$np}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11.5],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '0F172A'],
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color' => ['rgb' => '0F172A'],
                    ],
                ],
            ]);
            $sheet->getRowDimension($np)->setRowHeight(26);
        }

        // Outer table border
        $sheet->getStyle("A5:C{$highestRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '94A3B8'],
                ],
            ],
        ]);

        $sheet->freezePane('A6');

        return [];
    }
}
