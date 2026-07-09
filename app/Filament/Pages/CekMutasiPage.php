<?php

namespace App\Filament\Pages;

use App\Models\KasHarian;
use App\Models\MasterRekeningTujuan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class CekMutasiPage extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup   = 'Pembayaran';
    protected static ?string $navigationLabel   = 'Cek Mutasi';
    protected static ?string $title             = 'Cek Mutasi';
    protected static ?int    $navigationSort    = 2;

    protected static string $view = 'filament.pages.cek-mutasi-page';

    public array $rekeningFilters = [];
    public string $searchNoRef = '';
    public string $searchPengirim = '';
    public string $filterRekening = '';
    public string $filterVerified = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public function mount(): void
    {
        $this->rekeningFilters = MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label')->toArray();
    }

    #[Computed]
    public function queryDasar(): \Illuminate\Database\Eloquent\Builder
    {
        $q = KasHarian::where('debit', '>', 0);

        if ($this->tanggalDari) {
            $q->whereDate('tanggal', '>=', $this->tanggalDari);
        }
        if ($this->tanggalSampai) {
            $q->whereDate('tanggal', '<=', $this->tanggalSampai);
        }
        if ($this->searchNoRef) {
            $q->where('no_ref', 'like', "%{$this->searchNoRef}%");
        }
        if ($this->searchPengirim) {
            $q->where('nama_rekening_pengirim', 'like', "%{$this->searchPengirim}%");
        }
        if ($this->filterRekening) {
            $q->where('rekening_tujuan', $this->filterRekening);
        }

        return $q;
    }

    #[Computed]
    public function transaksiPending(): \Illuminate\Support\Collection
    {
        return (clone $this->queryDasar)
            ->whereNull('verified_at')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    #[Computed]
    public function transaksiTerverifikasi(): \Illuminate\Support\Collection
    {
        return (clone $this->queryDasar)
            ->whereNotNull('verified_at')
            ->with('verifiedBy')
            ->orderBy('verified_at', 'desc')
            ->get();
    }

    #[Computed]
    public function summaryPerRekening(): array
    {
        $rekening = MasterRekeningTujuan::orderBy('urutan')->pluck('label');

        $rows = KasHarian::where('debit', '>', 0)
            ->whereIn('rekening_tujuan', $rekening->toArray())
            ->selectRaw("
                rekening_tujuan,
                SUM(debit) as total_masuk,
                SUM(CASE WHEN verified_at IS NOT NULL THEN debit ELSE 0 END) as total_terverifikasi
            ")
            ->groupBy('rekening_tujuan')
            ->get()
            ->keyBy('rekening_tujuan');

        $result = [];
        foreach ($rekening as $label) {
            $result[$label] = [
                'total_masuk'         => (float) ($rows[$label]->total_masuk ?? 0),
                'total_terverifikasi' => (float) ($rows[$label]->total_terverifikasi ?? 0),
            ];
        }

        return $result;
    }

    public function toggleVerifikasi(int $id): void
    {
        $row = KasHarian::findOrFail($id);

        if ($row->verified_at) {
            $row->update([
                'verified_at' => null,
                'verified_by'  => null,
            ]);
        } else {
            $row->update([
                'verified_at' => now(),
                'verified_by'  => auth()->id(),
            ]);
        }

        unset($this->transaksiPending, $this->transaksiTerverifikasi, $this->summaryPerRekening);
    }
}