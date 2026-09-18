<?php

namespace App\Exports;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalonSiswaPembayaranExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(protected int $tahun)
    {
    }

    public function headings(): array
    {
        $headings = ['No', 'Nama', 'Total Tagihan'];

        for ($i = 1; $i <= $this->maxCicilan(); $i++) {
            $headings[] = "Cicilan ke-{$i}";
        }

        return $headings;
    }

    public function array(): array
    {
        $rows = [];

        $siswaList = Siswa::where('is_calon', 1)
            ->whereHas('pembayarans', fn ($q) => $q
                ->where('jenis_pembayaran_id', 1)
                ->where('tahun', (string) $this->tahun))
            ->orderBy('nama')
            ->get();

        foreach ($siswaList as $no => $siswa) {
            $pembayarans = Pembayaran::where('siswa_id', $siswa->id)
                ->where('jenis_pembayaran_id', 1)
                ->where('tahun', (string) $this->tahun)
                ->orderBy('tanggal_bayar')
                ->orderBy('id')
                ->get();

            $tagihan = Tagihan::where('siswa_id', $siswa->id)
                ->where('jenis_pembayaran_id', 1)
                ->where('tahun', (string) $this->tahun)
                ->first();

            $totalTerbayar = (float) $pembayarans->sum('nominal');
            $sisaTagihan   = ($tagihan && $tagihan->status !== 'lunas') ? (float) $tagihan->nominal_tagihan : 0.0;

            $row = [
                $no + 1,
                $siswa->nama,
                $totalTerbayar + $sisaTagihan,
            ];

            foreach ($pembayarans as $p) {
                $tanggal = Carbon::parse($p->tgl_bayar_struk ?? $p->tanggal_bayar)->format('d M Y');
                $row[]   = 'Rp ' . number_format((float) $p->nominal, 0, ',', '.') . ' - ' . $tanggal;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    protected function maxCicilan(): int
    {
        return (int) Pembayaran::where('jenis_pembayaran_id', 1)
            ->where('tahun', (string) $this->tahun)
            ->selectRaw('siswa_id, COUNT(*) as jumlah')
            ->groupBy('siswa_id')
            ->get()
            ->max('jumlah');
    }

    public function styles(Worksheet $sheet)
    {
        $highestCol = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2563EB'],
            ],
        ]);

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
