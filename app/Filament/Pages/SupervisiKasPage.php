<?php

namespace App\Filament\Pages;

use App\Models\KasHarian;
use App\Models\LogDanaMasuk;
use App\Models\MasterRekeningTujuan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class SupervisiKasPage extends Page
{
    use WithPagination;


    protected static ?string $navigationIcon    = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup   = 'Pembayaran';
    protected static ?string $navigationLabel   = 'Supervisi Kas';
    protected static ?string $title             = 'Supervisi Kas';
    protected static ?int    $navigationSort    = 33;

    protected static string $view = 'filament.pages.supervisi-kas-page';

    public string $tab = 'transaksi';

    public string $search = '';
    public string $filterVerified = '';

    public string $logSearch = '';

    public ?int $editId = null;
    public string $editNoRef = '';
    public string $editRekeningTujuan = '';
    public string $editNamaPengirim = '';
    public string $editTanggal = '';
    public string $editNominal = '';
    public string $editPotongan = '';
    public string $editNominalAwal = '';
    public string $editSisaTagihan = '';
    public bool $editIsCicilan = false;
    public string $editPassword = '';
    public ?string $editError = null;
    public ?int $logDetailId = null;

    public bool $showVerifModal = false;
    public ?int $verifToggleId = null;
    public bool $verifToggleTo = false;
    public string $verifPassword = '';
    public ?string $verifError = null;

    public ?string $editSource = null;
    public bool $showResetConfirm = false;
    public string $resetPassword = '';
    public ?string $resetError = null;

    protected function queryDasar()
    {
        $q = KasHarian::where('debit', '>', 0);

        if ($this->search) {
            $s = $this->search;
            $q->where(function ($q) use ($s) {
                $q->where('no_ref', 'like', "%{$s}%")
                  ->orWhere('nama_rekening_pengirim', 'like', "%{$s}%")
                  ->orWhere('rekening_tujuan', 'like', "%{$s}%")
                  ->orWhere('uraian', 'like', "%{$s}%");
            });
        }
        if ($this->filterVerified === 'verified') {
            $q->whereNotNull('verified_at');
        } elseif ($this->filterVerified === 'pending') {
            $q->whereNull('verified_at');
        }

        return $q;
    }

    #[Computed]
    public function transaksiList(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryDasar()
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);
    }

    #[Computed]
    public function logList(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $q = LogDanaMasuk::with('createdBy')
            ->orderByDesc('created_at');

        if ($this->logSearch) {
            $s = $this->logSearch;
            $q->where(function ($q) use ($s) {
                $q->where('action', 'like', "%{$s}%")
                  ->orWhere('uraian', 'like', "%{$s}%")
                  ->orWhere('data_lama', 'like', "%{$s}%")
                  ->orWhere('data_baru', 'like', "%{$s}%");
            });
        }

        return $q->paginate(20, pageName: 'logPage');
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->filterVerified = '';
    }

    public function toggleVerifikasi(int $id): void
    {
        $row = KasHarian::findOrFail($id);
        $this->verifToggleId = $id;
        $this->verifToggleTo = is_null($row->verified_at);
        $this->verifPassword = '';
        $this->verifError = null;
        $this->showVerifModal = true;
    }

    public function closeVerifModal(): void
    {
        $this->showVerifModal = false;
        $this->verifToggleId = null;
        $this->verifPassword = '';
        $this->verifError = null;
    }

    public function submitVerifikasi(): void
    {
        if (!Hash::check($this->verifPassword, auth()->user()->password)) {
            $this->verifError = 'Password salah.';
            return;
        }

        $row = KasHarian::findOrFail($this->verifToggleId);

        if ($this->verifToggleTo) {
            $row->update([
                'verified_at' => now(),
                'verified_by'  => auth()->id(),
            ]);
        } else {
            $dataLama = $row->only(['no_ref', 'rekening_tujuan', 'nama_rekening_pengirim', 'debit']);
            $row->update([
                'verified_at' => null,
                'verified_by'  => null,
            ]);
            LogDanaMasuk::create([
                'kas_harian_id' => $row->id,
                'action'         => 'unverify',
                'uraian'         => $row->uraian,
                'data_lama'      => $dataLama,
                'data_baru'      => null,
                'created_by'     => auth()->id(),
            ]);
        }

        $this->closeVerifModal();
        unset($this->transaksiList);
    }

    public function openEdit(int $id): void
    {
        $row = KasHarian::findOrFail($id);
        $this->editId = $row->id;
        $this->editNoRef = $row->no_ref ?? '';
        $this->editRekeningTujuan = $row->rekening_tujuan ?? '';
        $this->editNamaPengirim = $row->nama_rekening_pengirim ?? '';
        $this->editTanggal = $row->tanggal ? $row->tanggal->format('Y-m-d') : now()->format('Y-m-d');
        $this->editNominal = (string) ((int) ($row->debit ?? 0));
        $this->editPassword = '';
        $this->editError = null;
        $this->editSource = $row->source;
        $this->showResetConfirm = false;
        $this->resetPassword = '';
        $this->resetError = null;

        if ($row->source === 'pembayaran' && $row->source_id) {
            $pembayaran = Pembayaran::find($row->source_id);
            if ($pembayaran) {
                $this->editIsCicilan = $pembayaran->status === 'cicilan';
                $this->editPotongan = $this->editIsCicilan ? '0' : (string) ((int) ($pembayaran->potongan ?? 0));
                $this->editNominalAwal = (string) ((int) ($pembayaran->nominal + $pembayaran->potongan));

                if ($pembayaran->tagihan_id) {
                    $tagihan = Tagihan::find($pembayaran->tagihan_id);
                    if ($tagihan) {
                        $allBayar = Pembayaran::where('tagihan_id', $pembayaran->tagihan_id)->get();
                        $totalOriginal = (float) $tagihan->nominal_tagihan + $allBayar->sum('nominal');
                        $this->editSisaTagihan = (string) ((int) max(0, $totalOriginal));
                    } else {
                        $this->editSisaTagihan = '0';
                    }
                } else {
                    $this->editSisaTagihan = '0';
                }
                return;
            }
        }
        $this->editPotongan = '0';
        $this->editNominalAwal = $this->editNominal;
        $this->editSisaTagihan = '0';
    }

    public function closeEdit(): void
    {
        $this->editId = null;
        $this->editPassword = '';
        $this->editError = null;
        $this->editPotongan = '';
        $this->editNominalAwal = '';
        $this->editSisaTagihan = '';
        $this->editIsCicilan = false;
        $this->editSource = null;
        $this->showResetConfirm = false;
        $this->resetPassword = '';
        $this->resetError = null;
    }

    public function confirmReset(): void
    {
        $this->showResetConfirm = true;
        $this->resetPassword = '';
        $this->resetError = null;
    }

    public function cancelReset(): void
    {
        $this->showResetConfirm = false;
        $this->resetPassword = '';
        $this->resetError = null;
    }

    public function executeReset(): void
    {
        if (! Hash::check($this->resetPassword, auth()->user()->password)) {
            $this->resetError = 'Password yang anda masukkan salah';
            return;
        }

        $row = KasHarian::findOrFail($this->editId);

        if ($row->source !== 'pembayaran') {
            $this->resetError = 'Reset hanya tersedia untuk transaksi pembayaran';
            return;
        }

        $pembayaran = Pembayaran::find($row->source_id);
        if (! $pembayaran) {
            $this->resetError = 'Data pembayaran tidak ditemukan';
            return;
        }

        LogDanaMasuk::create([
            'kas_harian_id' => $row->id,
            'action'         => 'reset',
            'uraian'         => $row->uraian,
            'data_lama'      => [
                'no_ref'                => $row->no_ref,
                'rekening_tujuan'       => $row->rekening_tujuan,
                'nama_rekening_pengirim'=> $row->nama_rekening_pengirim,
                'tanggal'               => $row->tanggal?->format('Y-m-d'),
                'nominal'               => (float) $row->debit,
                'potongan'              => (float) ($pembayaran->potongan ?? 0),
                'tagihan_id'            => $pembayaran->tagihan_id,
                'batch_uuid'            => $pembayaran->batch_uuid,
            ],
            'data_baru'      => null,
            'created_by'     => auth()->id(),
        ]);

        $tagihanId = $pembayaran->tagihan_id;

        if ($tagihanId) {
            $allPembayaran = Pembayaran::where('tagihan_id', $tagihanId)->get();
            $totalRestore = $allPembayaran->sum('nominal') + $allPembayaran->sum('potongan');

            $tagihan = Tagihan::find($tagihanId);
            if ($tagihan) {
                $tagihan->update([
                    'nominal_tagihan' => $tagihan->nominal_tagihan + $totalRestore,
                    'status'          => 'belum_bayar',
                ]);
            }

            foreach ($allPembayaran as $p) {
                KasHarian::where('source', 'pembayaran')
                    ->where('source_id', $p->id)
                    ->delete();
                $p->delete();
            }
        } else {
            $toDelete = $pembayaran->batch_uuid
                ? Pembayaran::where('batch_uuid', $pembayaran->batch_uuid)->get()
                : collect([$pembayaran]);

            foreach ($toDelete as $p) {
                KasHarian::where('source', 'pembayaran')
                    ->where('source_id', $p->id)
                    ->delete();
                $p->delete();
            }
        }

        $this->closeEdit();
        $this->cancelReset();

        Notification::make()
            ->title('Transaksi berhasil di-reset')
            ->body('Data pembayaran dan posting kas telah dihapus. Silakan input ulang dari menu Pembayaran.')
            ->success()
            ->send();

        unset($this->transaksiList);
    }

    public function updatedEditPotongan($value): void
    {
        $nominalAwal = max(0, (int) ($this->editNominalAwal ?? 0));
        $potongan = max(0, (int) ($value ?? 0));
        $this->editNominal = (string) max(0, $nominalAwal - $potongan);
    }

    public function saveEdit(): void
    {
        if (! Hash::check($this->editPassword, auth()->user()->password)) {
            $this->editError = 'Password yang anda masukkan salah';
            return;
        }

        $row = KasHarian::findOrFail($this->editId);

        if ($row->source === 'pembayaran' && $row->source_id) {
            $pembayaran = Pembayaran::find($row->source_id);
            $nominalBaru = (float) ($this->editNominal ?? 0);
            $potonganBaru = (float) ($this->editPotongan ?? 0);

            if ($pembayaran && $pembayaran->tagihan_id) {
                $tagihan = Tagihan::find($pembayaran->tagihan_id);
                if ($tagihan) {
                    $oldNominal = (float) $pembayaran->nominal;
                    $maxAllowed = (float) $tagihan->nominal_tagihan + $oldNominal;
                    if (($nominalBaru + $potonganBaru) > $maxAllowed && abs(($nominalBaru + $potonganBaru) - $maxAllowed) > 1) {
                        $this->editError = 'Nominal bayar (Rp ' . number_format($nominalBaru, 0, ',', '.')
                            . ') + potongan (Rp ' . number_format($potonganBaru, 0, ',', '.')
                            . ') melebihi batas maksimal (Rp ' . number_format($maxAllowed, 0, ',', '.')
                            . ') untuk pembayaran ini.';
                        return;
                    }
                }
            } else {
                $nominalAwal = (float) ($this->editNominalAwal ?? 0);
                if (abs(($nominalBaru + $potonganBaru) - $nominalAwal) > 1) {
                    $this->editError = 'Nominal bayar (Rp ' . number_format($nominalBaru, 0, ',', '.')
                        . ') + potongan (Rp ' . number_format($potonganBaru, 0, ',', '.')
                        . ') harus sama dengan total tagihan (Rp ' . number_format($nominalAwal, 0, ',', '.') . ').';
                    return;
                }
            }
        }

        $potonganLama = 0;
        if ($row->source === 'pembayaran' && $row->source_id) {
            $pembayaran = Pembayaran::find($row->source_id);
            $potonganLama = (float) ($pembayaran->potongan ?? 0);
        }

        $dataLama = [
            'no_ref'              => $row->no_ref,
            'rekening_tujuan'     => $row->rekening_tujuan,
            'nama_rekening_pengirim' => $row->nama_rekening_pengirim,
            'tanggal'             => $row->tanggal?->format('Y-m-d'),
            'nominal'             => (float) $row->debit,
            'potongan'            => $potonganLama,
        ];

        $dataBaru = [
            'no_ref'              => $this->editNoRef ?: null,
            'rekening_tujuan'     => $this->editRekeningTujuan ?: null,
            'nama_rekening_pengirim' => $this->editNamaPengirim ?: null,
            'tanggal'             => $this->editTanggal,
            'nominal'             => (float) $this->editNominal,
            'potongan'            => (float) ($this->editPotongan ?? 0),
        ];

        $row->update([
            'no_ref'                => $this->editNoRef ?: null,
            'rekening_tujuan'       => $this->editRekeningTujuan ?: null,
            'nama_rekening_pengirim'=> $this->editNamaPengirim ?: null,
            'tanggal'               => $this->editTanggal,
            'debit'                 => (float) $this->editNominal,
        ]);

        if ($row->source === 'pembayaran' && $row->source_id) {
            Pembayaran::where('id', $row->source_id)->update([
                'no_ref'                => $this->editNoRef ?: null,
                'rekening_tujuan'       => $this->editRekeningTujuan ?: null,
                'nama_rekening_pengirim'=> $this->editNamaPengirim ?: null,
                'tanggal_bayar'         => $this->editTanggal,
                'tgl_bayar_struk'       => $this->editTanggal,
                'nominal'               => (float) $this->editNominal,
                'potongan'              => (float) ($this->editPotongan ?? 0),
            ]);

            $pembayaranFresh = Pembayaran::find($row->source_id);
            if ($pembayaranFresh && $pembayaranFresh->tagihan_id) {
                $tagihan = Tagihan::find($pembayaranFresh->tagihan_id);
                if ($tagihan) {
                    $oldNominal = (float) ($dataLama['nominal'] ?? 0);
                    $newNominal = (float) $pembayaranFresh->nominal;
                    $newPotongan = (float) ($pembayaranFresh->potongan ?? 0);

                    $nominalTagihanBaru = (float) $tagihan->nominal_tagihan + $oldNominal - $newNominal;

                    if ($nominalTagihanBaru <= 0) {
                        $tagihan->update([
                            'nominal_tagihan' => 0,
                            'status'          => 'lunas',
                        ]);
                        if ($pembayaranFresh->status !== 'lunas') {
                            $pembayaranFresh->update(['status' => 'lunas']);
                        }
                    } else {
                        $tagihan->update([
                            'nominal_tagihan' => $nominalTagihanBaru,
                            'status'          => 'belum_bayar',
                        ]);
                    }
                }
            }
        } elseif ($row->source === 'donasi' && $row->source_id) {
            \App\Models\Donasi::where('id', $row->source_id)->update([
                'no_ref'                => $this->editNoRef ?: null,
                'rekening_tujuan'       => $this->editRekeningTujuan ?: null,
                'nama_rekening_pengirim'=> $this->editNamaPengirim ?: null,
                'tanggal'               => $this->editTanggal,
                'nominal'               => (float) $this->editNominal,
            ]);
        }

        LogDanaMasuk::create([
            'kas_harian_id' => $row->id,
            'action'         => 'update',
            'uraian'         => $row->uraian,
            'data_lama'      => $dataLama,
            'data_baru'      => $dataBaru,
            'created_by'     => auth()->id(),
        ]);

        $this->closeEdit();
        Notification::make()->title('Data berhasil diperbarui')->success()->send();

        unset($this->transaksiList);
    }

    public function openLogDetail(int $id): void
    {
        $this->logDetailId = $id;
    }

    public function closeLogDetail(): void
    {
        $this->logDetailId = null;
    }

    public function getRekeningOptionsProperty()
    {
        return MasterRekeningTujuan::orderBy('urutan')->pluck('label', 'label')->toArray();
    }

    #[Computed]
    public function logDetail(): ?LogDanaMasuk
    {
        if (! $this->logDetailId) return null;
        return LogDanaMasuk::find($this->logDetailId);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}