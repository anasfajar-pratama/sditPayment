<?php

namespace App\Exports;

use App\Models\TokenListrik;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TokenListrikExport implements FromArray, WithColumnWidths, WithStrictNullComparison, WithStyles
{
    private const HEADINGS = ['No', 'Tanggal', 'Nomor Token', 'KWH', 'Nominal (Rp)', 'Catatan'];

    private array $rows = [];

    private array $sectionRows = [];

    private array $subtotalRows = [];

    private ?int $totalRow = null;

    private int $headerRow = 0;

    private string $judulPeriode;

    private array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function __construct(
        private readonly string $mode,
        private readonly int $bulan,
        private readonly int $tahun,
        private readonly ?int $tokenListrikId = null,
    ) {
        $this->judulPeriode = ($this->namaBulan[$bulan] ?? $bulan).' '.$tahun;

        if ($this->mode === 'per_ruangan') {
            $this->buildPerRuangan();
        } else {
            $this->buildSemuaRuangan();
        }
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 15,
            'C' => 34,
            'D' => 12,
            'E' => 20,
            'F' => 42,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->rows);

        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($this->mode === 'per_ruangan') {
            $sheet->mergeCells('A3:F3');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $headerRange = "A{$this->headerRow}:F{$this->headerRow}";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFA16207'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle($headerRange)->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach ($this->sectionRows as $row) {
            $sheet->mergeCells("A{$row}:F{$row}");

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF92400E'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFDE68A'],
                ],
            ]);
        }

        foreach ($this->subtotalRows as $row) {
            $sheet->mergeCells("A{$row}:C{$row}");

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF92400E'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFEF3C7'],
                ],
            ]);
        }

        if ($this->mode === 'per_ruangan') {
            $sheet->getStyle("A{$this->headerRow}:F{$lastRow}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        } else {
            foreach ($this->subtotalRows as $index => $row) {
                $start = $this->sectionRows[$index];

                $sheet->getStyle("A{$start}:F{$row}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            if ($this->totalRow !== null) {
                $sheet->mergeCells("A{$this->totalRow}:C{$this->totalRow}");

                $sheet->getStyle("A{$this->totalRow}:F{$this->totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFA16207'],
                    ],
                ]);

                $sheet->getStyle("A{$this->totalRow}:F{$this->totalRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        $firstRow = $this->headerRow + 1;

        $sheet->getStyle("A{$firstRow}:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("B{$firstRow}:B{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("D{$firstRow}:E{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("D{$firstRow}:D{$lastRow}")
            ->getNumberFormat()->setFormatCode('#,##0.00');

        $sheet->getStyle("E{$firstRow}:E{$lastRow}")
            ->getNumberFormat()->setFormatCode('"Rp" #,##0');

        return [];
    }

    private function buildPerRuangan(): void
    {
        $token = TokenListrik::findOrFail($this->tokenListrikId);

        $this->rows[] = ['LAPORAN PEMBELIAN TOKEN LISTRIK'];
        $this->rows[] = [$this->ruanganLabel($token)];
        $this->rows[] = ['Periode: '.$this->judulPeriode];
        $this->rows[] = ['', '', '', '', '', ''];

        $this->headerRow = count($this->rows) + 1;
        $this->rows[] = self::HEADINGS;

        $pembelians = $this->pembelians($token);

        if ($pembelians->isEmpty()) {
            $this->rows[] = ['', 'Tidak ada pembelian pada periode ini.', '', '', '', ''];
        } else {
            foreach ($pembelians as $index => $pembelian) {
                $this->rows[] = [
                    $index + 1,
                    $pembelian->tanggal->format('d/m/Y'),
                    $pembelian->nomor_token ?: '-',
                    $pembelian->kwh !== null ? (float) $pembelian->kwh : null,
                    (float) $pembelian->nominal,
                    $pembelian->note ?: '-',
                ];
            }
        }

        $this->subtotalRows[] = count($this->rows) + 1;
        $this->rows[] = [
            'TOTAL',
            '',
            '',
            (float) $pembelians->sum('kwh'),
            (float) $pembelians->sum('nominal'),
            $pembelians->count().' transaksi',
        ];
    }

    private function buildSemuaRuangan(): void
    {
        $this->rows[] = ['LAPORAN PEMBELIAN TOKEN LISTRIK - SELURUH RUANGAN'];
        $this->rows[] = ['Periode: '.$this->judulPeriode];
        $this->rows[] = ['', '', '', '', '', ''];

        $this->headerRow = count($this->rows) + 1;
        $this->rows[] = self::HEADINGS;

        $tokens = TokenListrik::orderBy('nama_ruangan')->get();

        $grandTotal = 0.0;
        $grandKwh = 0.0;
        $grandCount = 0;

        foreach ($tokens as $token) {
            $pembelians = $this->pembelians($token);
            $total = (float) $pembelians->sum('nominal');
            $kwh = (float) $pembelians->sum('kwh');
            $count = $pembelians->count();

            $this->sectionRows[] = count($this->rows) + 1;
            $this->rows[] = [$this->ruanganLabel($token)];

            if ($pembelians->isEmpty()) {
                $this->rows[] = ['', 'Tidak ada pembelian pada periode ini.', '', '', '', ''];
            } else {
                foreach ($pembelians as $index => $pembelian) {
                    $this->rows[] = [
                        $index + 1,
                        $pembelian->tanggal->format('d/m/Y'),
                        $pembelian->nomor_token ?: '-',
                        $pembelian->kwh !== null ? (float) $pembelian->kwh : null,
                        (float) $pembelian->nominal,
                        $pembelian->note ?: '-',
                    ];
                }
            }

            $this->subtotalRows[] = count($this->rows) + 1;
            $this->rows[] = [
                'Subtotal '.$token->nama_ruangan,
                '',
                '',
                $kwh,
                $total,
                $count.' transaksi',
            ];

            $this->rows[] = ['', '', '', '', '', ''];

            $grandTotal += $total;
            $grandKwh += $kwh;
            $grandCount += $count;
        }

        $this->totalRow = count($this->rows) + 1;
        $this->rows[] = [
            'TOTAL SELURUH RUANGAN',
            '',
            '',
            $grandKwh,
            $grandTotal,
            $grandCount.' transaksi',
        ];
    }

    private function pembelians(TokenListrik $token): Collection
    {
        return $token->pembelians()
            ->whereMonth('tanggal', $this->bulan)
            ->whereYear('tanggal', $this->tahun)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();
    }

    private function ruanganLabel(TokenListrik $token): string
    {
        $label = 'Ruangan: '.$token->nama_ruangan;

        if ($token->nomor_meter) {
            $label .= ' | No. Meter: '.$token->nomor_meter;
        }

        return $label;
    }
}
