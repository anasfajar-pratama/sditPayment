<?php
// File: app/Filament/Pages/PengeluaranOperasionalPage.php

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

    // Daftar tab/kategori — sesuaikan jika berubah
    public const KATEGORI = [
        'TOKEN & PULSA',
        'PERLENGKAPAN',
        'MAINTENANCE & FC',
        'TRANSPORT & DINAS',
        'PEMBANGUNAN',
        'BUKU PAKET',
        'BANGKU & SERAGAM',
        // 'MAKAN & MINUM',
    ];

    #[Url] public string $filterBulan;
    #[Url] public string $filterTahun;
    #[Url] public string $activeTab = '';

    public function mount(): void
    {
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->format('Y');
        $this->activeTab   = self::KATEGORI[0];
    }

    public function updatedFilterBulan(): void { unset($this->entriesPerTab, $this->ringkasan); }
    public function updatedFilterTahun(): void { unset($this->entriesPerTab, $this->ringkasan); }
    public function setTab(string $tab): void  { $this->activeTab = $tab; }

    #[Computed]
    public function entriesPerTab(): array
    {
        // Ambil semua data sekaligus, kelompokkan di PHP
        $rows = KasHarian::with('akun')
            ->where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
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
            $summary[$kat] = (float) KasHarian::where('tahun', $this->filterTahun)
                ->where('bulan', $this->filterBulan)
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
        return ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'][$bulan] ?? $bulan;
    }
}
