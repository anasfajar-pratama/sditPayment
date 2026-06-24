<?php
// File: app/Filament/Pages/PengeluaranUpahPage.php

namespace App\Filament\Pages;

use App\Models\KasHarian;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class PengeluaranUpahPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Pengeluaran';
    protected static ?string $navigationLabel = 'Upah';
    protected static ?string $title           = 'Pengeluaran Upah';
    protected static ?int    $navigationSort  = 22;

    protected static string $view = 'filament.pages.pengeluaran-upah-page';

    #[Url] public string $filterBulan;
    #[Url] public string $filterTahun;
    #[Url] public string $activeTab = '';

    public function mount(): void
    {
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->format('Y');
        // Tab aktif = penerima pertama yang ada, atau kosong
        $this->activeTab = $this->penerimas[0] ?? '';
    }

    public function updatedFilterBulan(): void { unset($this->penerimas, $this->entriesPerTab, $this->ringkasan); }
    public function updatedFilterTahun(): void { unset($this->penerimas, $this->entriesPerTab, $this->ringkasan); }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // Daftar penerima upah — dinamis dari data yang sudah diinput
    #[Computed]
    public function penerimas(): array
    {
        return KasHarian::whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereNotNull('sub_kategori')
            ->distinct()
            ->orderBy('sub_kategori')
            ->pluck('sub_kategori')
            ->toArray();
    }

    #[Computed]
    public function entriesPerTab(): array
    {
        if (empty($this->penerimas)) return [];

        $rows = KasHarian::with('akun')
            ->where('tahun', $this->filterTahun)
            ->where('bulan', $this->filterBulan)
            ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereNotNull('sub_kategori')
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $grouped = [];

        foreach ($this->penerimas as $penerima) {
            $grouped[$penerima] = [];
            $kumulatif = 0;
            $no = 1;

            foreach ($rows->where('sub_kategori', $penerima) as $row) {
                $kumulatif += (float) ($row->kredit ?? 0);
                $grouped[$penerima][] = [
                    'no'      => $no++,
                    'tanggal' => $row->tanggal->format('d-M-y'),
                    'uraian'  => $row->uraian,
                    'jumlah'  => (float) ($row->kredit ?? 0),
                    'total'   => $kumulatif,
                    'id'      => $row->id,
                ];
            }
        }

        return $grouped;
    }

    #[Computed]
    public function ringkasan(): array
    {
        if (empty($this->penerimas)) return [];

        $summary = [];
        foreach ($this->penerimas as $p) {
            $summary[$p] = (float) KasHarian::where('tahun', $this->filterTahun)
                ->where('bulan', $this->filterBulan)
                ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
                ->where('sub_kategori', $p)
                ->sum('kredit');
        }
        return $summary;
    }

    #[Computed]
    public function grandTotal(): float
    {
        return array_sum($this->ringkasan);
    }

    // Total upah per penerima SEPANJANG TAHUN (bukan per bulan)
    #[Computed]
    public function totalTahunPerPenerima(): array
    {
        if (empty($this->penerimas)) return [];

        $result = [];
        foreach ($this->penerimas as $p) {
            $result[$p] = (float) KasHarian::where('tahun', $this->filterTahun)
                ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
                ->where('sub_kategori', $p)
                ->sum('kredit');
        }
        return $result;
    }

    public function getBulanLabel(string $bulan): string
    {
        return ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'][$bulan] ?? $bulan;
    }
}
