<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Pages/RekapBulananPage.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Pages;

use App\Models\Akun;
use App\Models\KasHarian;
use App\Models\SaldoAwalBulan;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class RekapBulananPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Rekap Bulanan';
    protected static ?string $title           = 'Rekap Bulanan';
    protected static ?int    $navigationSort  = 11;

    protected static string $view = 'filament.pages.rekap-bulanan-page';

    #[Url]
    public string $filterBulan;

    #[Url]
    public string $filterTahun;

    public function mount(): void
    {
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->format('Y');
    }

    public function updatedFilterBulan(): void { unset($this->rekapAkun, $this->ringkasan); }
    public function updatedFilterTahun(): void { unset($this->rekapAkun, $this->ringkasan); }

    #[Computed]
    public function ringkasan(): array
    {
        $saldoAwal   = SaldoAwalBulan::getSaldo($this->filterBulan, $this->filterTahun);
        $totalDebit  = (float) KasHarian::where('tahun', $this->filterTahun)
                            ->where('bulan', $this->filterBulan)->sum('debit');
        $totalKredit = (float) KasHarian::where('tahun', $this->filterTahun)
                            ->where('bulan', $this->filterBulan)->sum('kredit');

        return [
            'saldo_awal'   => $saldoAwal,
            'total_debit'  => $totalDebit,
            'total_kredit' => $totalKredit,
            'saldo_akhir'  => $saldoAwal + $totalDebit - $totalKredit,
        ];
    }

    #[Computed]
    public function rekapAkun(): \Illuminate\Support\Collection
    {
        // Ambil semua entri bulan ini + join akun
        $entries = KasHarian::with('akun')
            ->where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
            ->get();

        // Kelompokkan per akun
        return $entries
            ->groupBy(fn ($e) => $e->akun_id ?? 0)
            ->map(function ($rows, $akunId) {
                $akun        = $rows->first()->akun;
                $totalDebit  = $rows->sum('debit');
                $totalKredit = $rows->sum('kredit');

                return [
                    'akun_id'      => $akunId,
                    'kode_akun'    => $akun?->kode_akun ?? '—',
                    'nama_akun'    => $akun?->nama_akun ?? 'Tanpa Akun',
                    'kelompok'     => $akun?->kelompok ?? 'Lainnya',
                    'total_debit'  => (float) $totalDebit,
                    'total_kredit' => (float) $totalKredit,
                    'jumlah_trans' => $rows->count(),
                ];
            })
            ->sortBy('kode_akun')
            ->values();
    }

    #[Computed]
    public function rekapPerKelompok(): \Illuminate\Support\Collection
    {
        return $this->rekapAkun
            ->groupBy('kelompok')
            ->map(function ($items, $kelompok) {
                return [
                    'kelompok'     => $kelompok,
                    'total_debit'  => $items->sum('total_debit'),
                    'total_kredit' => $items->sum('total_kredit'),
                    'items'        => $items,
                ];
            })
            ->sortBy(fn ($g) => match($g['kelompok']) {
                'Aset'      => 1,
                'Pendapatan'=> 2,
                'Beban'     => 3,
                default     => 9,
            })
            ->values();
    }

    public function getBulanLabel(string $bulan): string
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ][$bulan] ?? $bulan;
    }
}
