<?php

namespace App\Filament\Pages;

use App\Filament\Traits\ManagesInputJurnal;
use App\Models\KasHarian;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class RingkasanKasHarianPage extends Page
{
    use ManagesInputJurnal;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Rekap Kas Harian';
    protected static ?string $title           = 'Rekap Kas Harian';
    protected static ?int    $navigationSort  = 19;

    protected static string $view = 'filament.pages.ringkasan-kas-harian-page';

    #[Url] public string $filterStart = '';
    #[Url] public string $filterEnd   = '';
    #[Url] public string $activeTab   = 'gabungan';

    public function mount(): void
    {
        $today = now()->toDateString();
        $this->filterStart = $today;
        $this->filterEnd   = $today;
        $this->activeTab   = 'gabungan';
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

    protected function getHeaderActions(): array
    {
        return [
            $this->inputJurnalAction(),
        ];
    }

    private function clearCache(): void
    {
        unset(
            $this->rows,
            $this->rowsSekolah,
            $this->totalTransfer,
            $this->totalCash,
            $this->totalKredit,
            $this->kasHariIni,
            $this->entriesTransfer,
            $this->entriesCash,
            $this->entriesKredit,
            $this->entriesGabungan,
            $this->entriesKasHariIni,
            $this->totalGabungan,
            $this->totalPendapatanYayasan,
            $this->totalPengeluaranYayasan,
            $this->totalYayasan,
            $this->totalNonYayasan,
            $this->totalSemua,
            $this->entriesNonYayasan,
            $this->entriesPendapatanYayasan,
            $this->entriesPengeluaranYayasan,
            $this->entriesYayasan,
            $this->entriesSemua,
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

    /**
     * Baris kas sekolah (tanpa transaksi yayasan).
     */
    #[Computed]
    public function rowsSekolah(): Collection
    {
        return $this->rows->filter(fn ($r) => ! $r->is_yayasan)->values();
    }

    #[Computed]
    public function totalTransfer(): float
    {
        return (float) $this->rowsSekolah
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0
                && $r->rekening_tujuan !== null
                && $r->rekening_tujuan !== 'Cash')
            ->sum(fn ($r) => (float) $r->debit);
    }

    #[Computed]
    public function totalCash(): float
    {
        return (float) $this->rowsSekolah
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0
                && ($r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash'))
            ->sum(fn ($r) => (float) $r->debit);
    }

    #[Computed]
    public function totalKredit(): float
    {
        return (float) $this->rowsSekolah->sum(fn ($r) => (float) ($r->kredit ?? 0));
    }

    #[Computed]
    public function kasHariIni(): float
    {
        return $this->totalCash - $this->totalKredit;
    }

    #[Computed]
    public function totalGabungan(): float
    {
        return $this->totalTransfer + $this->totalCash - $this->totalKredit;
    }

    #[Computed]
    public function totalPendapatanYayasan(): float
    {
        return (float) $this->rows
            ->filter(fn ($r) => $r->is_yayasan && (float) ($r->debit ?? 0) > 0)
            ->sum(fn ($r) => (float) $r->debit);
    }

    #[Computed]
    public function totalPengeluaranYayasan(): float
    {
        return (float) $this->rows
            ->filter(fn ($r) => $r->is_yayasan && (float) ($r->kredit ?? 0) > 0)
            ->sum(fn ($r) => (float) $r->kredit);
    }

    #[Computed]
    public function totalYayasan(): float
    {
        return $this->totalPendapatanYayasan - $this->totalPengeluaranYayasan;
    }

    #[Computed]
    public function totalNonYayasan(): float
    {
        return $this->totalGabungan;
    }

    #[Computed]
    public function totalSemua(): float
    {
        return (float) $this->rows
            ->sum(fn ($r) => (float) ($r->debit ?? 0) - (float) ($r->kredit ?? 0));
    }

    #[Computed]
    public function entriesTransfer(): array
    {
        $rows = $this->rowsSekolah
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0
                && $r->rekening_tujuan !== null
                && $r->rekening_tujuan !== 'Cash')
            ->values();

        return $this->mapEntries($rows, 'transfer');
    }

    #[Computed]
    public function entriesCash(): array
    {
        $rows = $this->rowsSekolah
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0
                && ($r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash'))
            ->values();

        return $this->mapEntries($rows, 'cash');
    }

    #[Computed]
    public function entriesKredit(): array
    {
        $rows = $this->rowsSekolah
            ->filter(fn ($r) => (float) ($r->kredit ?? 0) > 0)
            ->values();

        return $this->mapEntries($rows, 'kredit');
    }

    #[Computed]
    public function entriesKasHariIni(): array
    {
        $saldo = 0.0;
        $entries = [];

        foreach ($this->rowsSekolah as $r) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);
            $isCash = $r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash';

            if ($debit > 0 && $isCash) {
                $saldo += $debit;
                $entry = $this->mapRow($r, 'kashariini', 'Masuk', $debit, $saldo);
                $entry['no'] = count($entries) + 1;
                $entries[] = $entry;
            } elseif ($kredit > 0) {
                $saldo -= $kredit;
                $entry = $this->mapRow($r, 'kashariini', 'Keluar', $kredit, $saldo);
                $entry['no'] = count($entries) + 1;
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    #[Computed]
    public function entriesGabungan(): array
    {
        return $this->mapRunningSaldo($this->rowsSekolah, 'gabungan');
    }

    #[Computed]
    public function entriesNonYayasan(): array
    {
        $rows = $this->rows->filter(fn ($r) => ! $r->is_yayasan)->values();

        return $this->mapRunningSaldo($rows, 'non_yayasan');
    }

    #[Computed]
    public function entriesPendapatanYayasan(): array
    {
        $rows = $this->rows
            ->filter(fn ($r) => $r->is_yayasan && (float) ($r->debit ?? 0) > 0)
            ->values();

        return $this->mapEntries($rows, 'transfer');
    }

    #[Computed]
    public function entriesPengeluaranYayasan(): array
    {
        $rows = $this->rows
            ->filter(fn ($r) => $r->is_yayasan && (float) ($r->kredit ?? 0) > 0)
            ->values();

        return $this->mapEntries($rows, 'kredit');
    }

    #[Computed]
    public function entriesYayasan(): array
    {
        $rows = $this->rows->filter(fn ($r) => $r->is_yayasan)->values();

        return $this->mapRunningSaldo($rows, 'yayasan');
    }

    #[Computed]
    public function entriesSemua(): array
    {
        return $this->mapRunningSaldo($this->rows->values(), 'semua');
    }

    private function mapRunningSaldo(Collection $rows, string $mode): array
    {
        $saldo = 0.0;
        $entries = [];

        foreach ($rows as $r) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);

            if ($debit > 0) {
                $saldo += $debit;
                $entry = $this->mapRow($r, $mode, 'Masuk', $debit, $saldo);
                $entry['no'] = count($entries) + 1;
                $entries[] = $entry;
            } elseif ($kredit > 0) {
                $saldo -= $kredit;
                $entry = $this->mapRow($r, $mode, 'Keluar', $kredit, $saldo);
                $entry['no'] = count($entries) + 1;
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    private function mapEntries(Collection $rows, string $mode): array
    {
        $no = 1;
        return $rows->map(function ($r) use (&$no, $mode) {
            $entry = $this->mapRow($r, $mode);
            $entry['no'] = $no++;
            return $entry;
        })->values()->all();
    }

    private function mapRow($r, string $mode, string $tipe = null, float $jumlah = 0.0, float $saldo = 0.0): array
    {
        $debit  = (float) ($r->debit  ?? 0);
        $kredit = (float) ($r->kredit ?? 0);

        $jumlah = match ($mode) {
            'kredit'      => $kredit,
            'gabungan',
            'kashariini'  => $jumlah ?: ($debit > 0 ? $debit : $kredit),
            default       => $debit,
        };

        return [
            'no'           => 0,
            'tanggal'      => $r->tanggal->format('d-M-y'),
            'uraian'       => $r->uraian,
            'akun'         => $r->akun?->nama_akun ?? '—',
            'sub_kategori' => $r->sub_kategori,
            'rekening'     => $r->rekening_tujuan,
            'pengirim'     => $r->nama_rekening_pengirim,
            'tipe'         => $tipe ?: ($mode === 'kredit' ? 'Keluar' : 'Masuk'),
            'jumlah'       => $jumlah,
            'saldo'        => in_array($mode, ['gabungan', 'kashariini', 'non_yayasan', 'yayasan', 'semua']) ? $saldo : null,
            'id'           => $r->id,
            'is_yayasan'   => (bool) $r->is_yayasan,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}