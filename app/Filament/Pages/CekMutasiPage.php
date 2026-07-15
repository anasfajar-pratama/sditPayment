<?php

namespace App\Filament\Pages;

use App\Models\KasHarian;
use App\Models\MasterRekeningTujuan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class CekMutasiPage extends Page
{
    protected static ?string $navigationIcon    = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup   = 'Pembayaran';
    protected static ?string $navigationLabel   = 'Cek Mutasi';
    protected static ?string $title             = 'Cek Mutasi';
    protected static ?int    $navigationSort    = 32;

    protected static string $view = 'filament.pages.cek-mutasi-page';

    public array $rekeningFilters = [];
    public string $searchGlobal = '';
    public string $filterRekening = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';

    public array $selectedIds = [];
    public bool $selectAll = false;
    public bool $showVerifModal = false;
    public string $verifPassword = '';
    public ?string $verifError = null;

    public function mount(): void
    {
        $this->rekeningFilters = MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label')->toArray();
    }

    public function resetFilter(): void
    {
        $this->searchGlobal = '';
        $this->filterRekening = '';
        $this->tanggalDari = '';
        $this->tanggalSampai = '';
        unset($this->transaksiPending, $this->transaksiTerverifikasi, $this->summaryPerRekening);
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
        if ($this->searchGlobal) {
            $s = $this->searchGlobal;
            $q->where(function ($q) use ($s) {
                $q->where('no_ref', 'like', "%{$s}%")
                  ->orWhere('nama_rekening_pengirim', 'like', "%{$s}%")
                  ->orWhere('uraian', 'like', "%{$s}%");
            });
        }
        if ($this->filterRekening) {
            $q->where('rekening_tujuan', $this->filterRekening);
        }

        return $q;
    }

    #[Computed]
    public function transaksiPending(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return (clone $this->queryDasar)
            ->whereNull('verified_at')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20, pageName: 'pendingPage');
    }

    #[Computed]
    public function transaksiTerverifikasi(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return (clone $this->queryDasar)
            ->whereNotNull('verified_at')
            ->with('verifiedBy')
            ->orderBy('verified_at', 'desc')
            ->paginate(20, pageName: 'verifiedPage');
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

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->transaksiPending->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
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

    public function openVerifModal(): void
    {
        $this->selectedIds = array_filter($this->selectedIds, fn($id) =>
            KasHarian::where('id', $id)->whereNull('verified_at')->exists()
        );

        if (empty($this->selectedIds)) {
            Notification::make()->title('Tidak ada transaksi valid yang dipilih')->warning()->send();
            return;
        }

        $this->verifPassword = '';
        $this->verifError = null;
        $this->showVerifModal = true;
    }

    public function closeVerifModal(): void
    {
        $this->showVerifModal = false;
        $this->verifPassword = '';
        $this->verifError = null;
    }

    public function submitVerifikasiMassal(): void
    {
        if (!Hash::check($this->verifPassword, auth()->user()->password)) {
            $this->verifError = 'Password salah.';
            return;
        }

        $updated = 0;
        foreach ($this->selectedIds as $id) {
            $row = KasHarian::where('id', $id)->whereNull('verified_at')->first();
            if ($row) {
                $row->update(['verified_at' => now(), 'verified_by' => auth()->id()]);
                $updated++;
            }
        }

        $this->selectedIds = [];
        $this->closeVerifModal();
        unset($this->transaksiPending, $this->transaksiTerverifikasi, $this->summaryPerRekening);

        Notification::make()->title("{$updated} transaksi berhasil diverifikasi")->success()->send();

        $this->redirect(request()->url());
    }
}