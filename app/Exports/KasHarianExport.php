<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KasHarianExport implements FromArray, WithColumnWidths, WithStrictNullComparison, WithStyles
{
    private const HEADINGS = [
        'No',
        'Tanggal',
        'Uraian',
        'Akun',
        'Rekening Tujuan',
        'Sub Kategori',
        'Debit',
        'Kredit',
        'Saldo',
    ];

    private array $rows = [];

    private int $headerRow = 4;

    private int $totalRow = 0;

    public function __construct(
        private readonly array $entries,
        private readonly string $judulPeriode,
        private readonly float $totalDebit,
        private readonly float $totalKredit,
        private readonly float $saldoAkhir,
    ) {
        $this->build();
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 44,
            'D' => 22,
            'E' => 24,
            'F' => 20,
            'G' => 14,
            'H' => 14,
            'I' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->rows);

        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRange = "A{$this->headerRow}:I{$this->headerRow}";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($headerRange)->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $firstRow = $this->headerRow + 1;

        $sheet->getStyle("A{$firstRow}:I{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $totalRange = "A{$this->totalRow}:I{$this->totalRow}";

        $sheet->getStyle($totalRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F2937'],
            ],
        ]);

        $sheet->getStyle("A{$firstRow}:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("B{$firstRow}:B{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("G{$firstRow}:I{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("G{$firstRow}:I{$lastRow}")
            ->getNumberFormat()->setFormatCode('#,##0');

        return [];
    }

    private function build(): void
    {
        $blank = array_fill(0, 9, '');

        $this->rows[] = ['LAPORAN KAS GABUNGAN'];
        $this->rows[] = ['Periode: '.$this->judulPeriode];
        $this->rows[] = $blank;
        $this->rows[] = self::HEADINGS;

        foreach ($this->entries as $index => $entry) {
            $this->rows[] = [
                $index + 1,
                $entry['tanggal'],
                $entry['uraian'],
                $entry['akun'] ?? '—',
                $entry['rekening_tujuan'] ?: '—',
                $entry['sub_kategori'] ?: '—',
                (float) ($entry['debit'] ?? 0),
                (float) ($entry['kredit'] ?? 0),
                (float) $entry['saldo'],
            ];
        }

        $this->totalRow = count($this->rows) + 1;

        $this->rows[] = [
            '',
            '',
            '',
            '',
            '',
            'TOTAL',
            $this->totalDebit,
            $this->totalKredit,
            $this->saldoAkhir,
        ];
    }
}
