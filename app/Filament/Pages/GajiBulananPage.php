<?php
// File: app/Filament/Pages/GajiBulananPage.php

namespace App\Filament\Pages;

use App\Models\AbsenHarian;
use App\Models\GajiBulanan;
use App\Models\Karyawan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class GajiBulananPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Karyawan';
    protected static ?string $navigationLabel = 'Gaji Bulanan';
    protected static ?string $title           = 'Pembayaran Gaji Bulanan';
    protected static ?int    $navigationSort  = 30;

    protected static string $view = 'filament.pages.gaji-bulanan-page';

    #[Url] public string $filterBulan;
    #[Url] public string $filterTahun;

    /**
     * Array form: [karyawan_id => ['nominal' => '', 'keterangan' => '']]
     */
    public array $gajiForm = [];

    public function mount(): void
    {
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->format('Y');
        $this->loadForm();
    }

    public function updatedFilterBulan(): void { $this->loadForm(); unset($this->guruList, $this->sudahAda); }
    public function updatedFilterTahun(): void { $this->loadForm(); unset($this->guruList, $this->sudahAda); }

    // ─── Computed ─────────────────────────────────────────────────────────────

    #[Computed]
    public function guruList(): \Illuminate\Database\Eloquent\Collection
    {
        return Karyawan::aktif()->guru()->orderBy('nama')->get();
    }

    /** Apakah data gaji bulan ini sudah pernah disimpan? */
    #[Computed]
    public function sudahAda(): bool
    {
        return GajiBulanan::where('bulan', $this->filterBulan)
                          ->where('tahun', $this->filterTahun)
                          ->exists();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Isi $gajiForm dari data yang sudah ada, atau buat kosong dari daftar guru.
     */
    public function loadForm(): void
    {
        $guru = Karyawan::aktif()->guru()->orderBy('nama')->get();

        $existing = GajiBulanan::where('bulan', $this->filterBulan)
                               ->where('tahun', $this->filterTahun)
                               ->get()
                               ->keyBy('karyawan_id');

        $this->gajiForm = [];

        foreach ($guru as $k) {
            $rec = $existing[$k->id] ?? null;

            // Hitung hari masuk langsung dari absen_harians
            $hariMasuk = AbsenHarian::where('karyawan_id', $k->id)
                ->whereYear('tanggal', $this->filterTahun)
                ->whereMonth('tanggal', (int) $this->filterBulan)
                ->whereIn('status', ['hadir', 'dinas'])
                ->count();

            $this->gajiForm[$k->id] = [
                'nama'        => $k->nama,
                'jabatan'     => $k->jabatan,
                'hari_masuk'  => $hariMasuk,
                'nominal'     => $rec ? (string) (int) $rec->nominal_gaji : '',
                'keterangan'  => $rec?->keterangan ?? '',
                'status_bayar'=> $rec?->status_bayar ?? 'belum',
            ];
        }
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    /**
     * Simpan semua gaji sekaligus (insert atau update).
     */
    public function simpanGaji(): void
    {
        foreach ($this->gajiForm as $karyawanId => $data) {
            $nominal = preg_replace('/[^0-9]/', '', $data['nominal'] ?? '');
            if ($nominal === '' || $nominal === '0') continue;

            GajiBulanan::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'bulan'       => $this->filterBulan,
                    'tahun'       => $this->filterTahun,
                ],
                [
                    'hari_masuk'   => $data['hari_masuk'],
                    'nominal_gaji' => (int) $nominal,
                    'keterangan'   => $data['keterangan'] ?? null,
                    'status_bayar' => $data['status_bayar'] ?? 'belum',
                    'created_by'   => auth()->id(),
                    'updated_by'   => auth()->id(),
                ]
            );
        }

        unset($this->sudahAda);
        $this->loadForm();
        Notification::make()->title('Gaji berhasil disimpan')->success()->send();
    }

    /**
     * Tandai semua gaji bulan ini sebagai "Sudah Dibayar".
     */
    public function tandaiSudahBayar(): void
    {
        GajiBulanan::where('bulan', $this->filterBulan)
                   ->where('tahun', $this->filterTahun)
                   ->update([
                       'status_bayar'  => 'sudah',
                       'tanggal_bayar' => now()->toDateString(),
                       'updated_by'    => auth()->id(),
                   ]);

        // Sync status_bayar di form
        foreach ($this->gajiForm as $id => $_) {
            $this->gajiForm[$id]['status_bayar'] = 'sudah';
        }

        unset($this->sudahAda);
        Notification::make()->title('Semua gaji ditandai sudah dibayar')->success()->send();
    }

    // ─── Label helpers ────────────────────────────────────────────────────────

    public function getBulanLabel(string $bulan): string
    {
        return ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'][$bulan] ?? $bulan;
    }

    public function totalGaji(): int
    {
        return (int) array_sum(array_map(
            fn ($d) => (int) preg_replace('/[^0-9]/', '', $d['nominal'] ?? '0'),
            $this->gajiForm
        ));
    }
}
