<?php

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

    #[Url] public string $filterStart = '';
    #[Url] public string $filterEnd   = '';
    #[Url] public string $activeTab = '';

    public function mount(): void
    {
        $now = now();
        $this->filterStart = $now->startOfMonth()->format('Y-m-d');
        $this->filterEnd   = $now->endOfMonth()->format('Y-m-d');
        $this->activeTab = $this->penerimas[0] ?? '';
    }

    public function updatedFilterStart(): void { unset($this->penerimas, $this->entriesPerTab, $this->ringkasan, $this->grandTotal); }
    public function updatedFilterEnd(): void   { unset($this->penerimas, $this->entriesPerTab, $this->ringkasan, $this->grandTotal); }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    #[Computed]
    public function penerimas(): array
    {
        return KasHarian::whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereDate('tanggal', '>=', $this->filterStart)
            ->whereDate('tanggal', '<=', $this->filterEnd)
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
            ->whereDate('tanggal', '>=', $this->filterStart)
            ->whereDate('tanggal', '<=', $this->filterEnd)
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
            $summary[$p] = (float) KasHarian::whereDate('tanggal', '>=', $this->filterStart)
                ->whereDate('tanggal', '<=', $this->filterEnd)
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
}
