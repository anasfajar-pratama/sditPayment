<?php
// File: app/Filament/Pages/AbsenHarianPage.php

namespace App\Filament\Pages;

use App\Models\AbsenHarian;
use App\Models\Karyawan;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class AbsenHarianPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Karyawan';
    protected static ?string $navigationLabel = 'Absen Harian';
    protected static ?string $title           = 'Absen Harian Guru';
    protected static ?int    $navigationSort  = 81;

    protected static string $view = 'filament.pages.absen-harian-page';

    #[Url] public string $filterTanggal = '';

    public function mount(): void
    {
        $this->filterTanggal = now()->toDateString();
    }

    public function updatedFilterTanggal(): void
    {
        unset($this->absenHari, $this->rekapHari);
    }

    // ─── Computed ─────────────────────────────────────────────────────────────

    #[Computed]
    public function guru(): \Illuminate\Database\Eloquent\Collection
    {
        return Karyawan::aktif()->guru()
                       ->orderBy('nama')
                       ->get();
    }

    /** Array [karyawan_id => status] untuk tanggal yang dipilih */
    #[Computed]
    public function absenHari(): array
    {
        return AbsenHarian::where('tanggal', $this->filterTanggal)
                          ->pluck('status', 'karyawan_id')
                          ->toArray();
    }

    /** Rekap hitung per status hari ini */
    #[Computed]
    public function rekapHari(): array
    {
        $all = array_values($this->absenHari);
        return [
            'hadir' => count(array_filter($all, fn ($s) => $s === 'hadir')),
            'izin'  => count(array_filter($all, fn ($s) => $s === 'izin')),
            'sakit' => count(array_filter($all, fn ($s) => $s === 'sakit')),
            'alpha' => count(array_filter($all, fn ($s) => $s === 'alpha')),
            'dinas' => count(array_filter($all, fn ($s) => $s === 'dinas')),
            'belum' => $this->guru->count() - count($all),
        ];
    }

    // ─── Actions ──────────────────────────────────────────────────────────────

    /**
     * Simpan/ubah status absen satu guru — dipanggil langsung saat klik tombol status.
     */
    public function setStatus(int $karyawanId, string $status): void
    {
        AbsenHarian::updateOrCreate(
            [
                'karyawan_id' => $karyawanId,
                'tanggal'     => $this->filterTanggal,
            ],
            [
                'status'     => $status,
                'updated_by' => auth()->id(),
                'created_by' => auth()->id(),
            ]
        );

        unset($this->absenHari, $this->rekapHari);
    }

    /**
     * Tandai semua guru yang BELUM diabsen sebagai Hadir sekaligus.
     */
    public function hadirSemua(): void
    {
        $sudahAbsen = array_keys($this->absenHari);

        $this->guru
            ->whereNotIn('id', $sudahAbsen)
            ->each(function (Karyawan $k) {
                AbsenHarian::updateOrCreate(
                    ['karyawan_id' => $k->id, 'tanggal' => $this->filterTanggal],
                    ['status' => 'hadir', 'created_by' => auth()->id(), 'updated_by' => auth()->id()]
                );
            });

        unset($this->absenHari, $this->rekapHari);
        Notification::make()->title('Semua guru ditandai Hadir')->success()->send();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function statusList(): array
    {
        return ['hadir', 'izin', 'sakit', 'alpha', 'dinas', 'libur'];
    }

    public function statusConfig(string $status): array
    {
        return AbsenHarian::statusColor()[$status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280', 'border' => '#e5e7eb'];
    }

    public function statusLabel(string $status): string
    {
        return AbsenHarian::statusLabel()[$status] ?? $status;
    }

    public function judulHari(): string
    {
        $tgl = Carbon::parse($this->filterTanggal);
        $hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
                 'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        return ($hari[$tgl->format('l')] ?? '') . ', ' . $tgl->format('d F Y');
    }
}
