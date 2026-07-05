<?php

namespace App\Filament\Pages;

use App\Models\KasHarian;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class PengeluaranOperasionalPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $navigationLabel = 'Operasional';
    protected static ?string $title           = 'Pengeluaran Operasional';
    protected static ?int    $navigationSort  = 20;

    protected static string $view = 'filament.pages.pengeluaran-operasional-page';

    public const KATEGORI = [
        'TOKEN & PULSA',
        'PERLENGKAPAN',
        'MAINTENANCE & FC',
        'TRANSPORT & DINAS',
        'PEMBANGUNAN',
        'BUKU PAKET',
        'BANGKU & SERAGAM',
    ];

    #[Url] public string $filterStart = '';
    #[Url] public string $filterEnd   = '';
    #[Url] public string $activeTab = '';

    public function mount(): void
    {
        $now = now();
        $this->filterStart = $now->startOfMonth()->format('Y-m-d');
        $this->filterEnd   = $now->endOfMonth()->format('Y-m-d');
        $this->activeTab   = self::KATEGORI[0];
    }

    public function updatedFilterStart(): void { unset($this->entriesPerTab, $this->ringkasan); }
    public function updatedFilterEnd(): void   { unset($this->entriesPerTab, $this->ringkasan); }
    public function setTab(string $tab): void  { $this->activeTab = $tab; }

    #[Computed]
    public function entriesPerTab(): array
    {
        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $this->filterStart)
            ->whereDate('tanggal', '<=', $this->filterEnd)
            ->whereIn('sub_kategori', self::KATEGORI)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $grouped = [];

        foreach (self::KATEGORI as $kat) {
            $grouped[$kat] = [];
            $kumulatif = 0;
            $no = 1;

            foreach ($rows->where('sub_kategori', $kat) as $row) {
                $kumulatif += (float) ($row->kredit ?? 0);
                $grouped[$kat][] = [
                    'no'        => $no++,
                    'tanggal'   => $row->tanggal->format('d-M-y'),
                    'uraian'    => $row->uraian,
                    'jumlah'    => (float) ($row->kredit ?? 0),
                    'total'     => $kumulatif,
                    'id'        => $row->id,
                ];
            }
        }

        return $grouped;
    }

    #[Computed]
    public function ringkasan(): array
    {
        $summary = [];
        foreach (self::KATEGORI as $kat) {
            $summary[$kat] = (float) KasHarian::whereDate('tanggal', '>=', $this->filterStart)
                ->whereDate('tanggal', '<=', $this->filterEnd)
                ->where('sub_kategori', $kat)
                ->sum('kredit');
        }
        return $summary;
    }

    #[Computed]
    public function grandTotal(): float
    {
        return array_sum($this->ringkasan);
    }

    public function getBulanLabel(string $bulan): string
    {
        return match ($bulan) {
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            default => $bulan,
        };
    }
}
