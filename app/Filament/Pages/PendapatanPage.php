<?php

namespace App\Filament\Pages;

use App\Models\Akun;
use App\Models\KasHarian;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class PendapatanPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pendapatan';
    protected static ?string $title           = 'Pendapatan';
    protected static ?int    $navigationSort  = 18;

    protected static string $view = 'filament.pages.pendapatan-page';

    #[Url] public string $filterStart = '';
    #[Url] public string $filterEnd   = '';
    #[Url] public string $activeTab   = '';

    public function mount(): void
    {
        $today = now()->toDateString();
        $this->filterStart = $today;
        $this->filterEnd   = $today;

        $first = $this->akunList[0] ?? null;
        $this->activeTab = (string) ($first['id'] ?? '');
    }

    public function updatedFilterStart(): void
    {
        $this->clearCache();
    }

    public function updatedFilterEnd(): void
    {
        $this->clearCache();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    private function clearCache(): void
    {
        unset(
            $this->rows,
            $this->akunList,
            $this->ringkasan,
            $this->grandTotal,
            $this->entriesPerTab,
        );
    }

    #[Computed]
    public function rows(): Collection
    {
        return KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $this->filterStart)
            ->whereDate('tanggal', '<=', $this->filterEnd)
            ->orderBy('tanggal')->orderBy('id')
            ->get();
    }

    #[Computed]
    public function akunList(): array
    {
        $akuns = Akun::where('kelompok', 'Pendapatan')
            ->orderBy('kode_akun')
            ->get()
            ->keyBy('id');

        $used = $this->rows
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0)
            ->map(fn ($r) => $r->akun)
            ->filter(fn ($a) => $a !== null && $a->kelompok === 'Pendapatan');

        foreach ($used as $a) {
            if (! $akuns->has($a->id)) {
                $akuns->put($a->id, $a);
            }
        }

        return $akuns
            ->sortBy('kode_akun')
            ->values()
            ->map(fn ($a) => [
                'id'   => $a->id,
                'kode' => $a->kode_akun,
                'nama' => $a->nama_akun,
            ])
            ->all();
    }

    #[Computed]
    public function ringkasan(): array
    {
        $byAkun = $this->rows
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0)
            ->groupBy('akun_id')
            ->map(fn ($list) => (float) $list->sum('debit'));

        $result = [];
        foreach ($this->akunList as $akun) {
            $result[$akun['id']] = (float) ($byAkun->get($akun['id']) ?? 0);
        }
        return $result;
    }

    #[Computed]
    public function grandTotal(): float
    {
        return (float) array_sum($this->ringkasan);
    }

    #[Computed]
    public function entriesPerTab(): array
    {
        $akunId = (int) $this->activeTab;
        $no = 1;

        return $this->rows
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0 && (int) $r->akun_id === $akunId)
            ->values()
            ->map(function ($r) use (&$no) {
                return [
                    'no'       => $no++,
                    'tanggal'  => $r->tanggal->format('d-M-y'),
                    'uraian'   => $r->uraian,
                    'rekening' => $r->rekening_tujuan,
                    'pengirim' => $r->nama_rekening_pengirim,
                    'jumlah'   => (float) $r->debit,
                    'id'       => $r->id,
                ];
            })
            ->all();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}