<?php

namespace App\Filament\Pages;

use App\Models\AbsenHarian;
use App\Models\GajiBulanan;
use App\Models\Karyawan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url as LivewireUrl;

class GajiBulananPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Karyawan';
    protected static ?string $navigationLabel = 'Gaji Bulanan';
    protected static ?string $title           = 'Pembayaran Gaji Bulanan';
    protected static ?int    $navigationSort  = 82;

    protected static string $view = 'filament.pages.gaji-bulanan-page';

    #[LivewireUrl] public string $filterBulan;
    #[LivewireUrl] public string $filterTahun;

    /**
     * Array form: [karyawan_id => [...fields]]
     */
    public array $gajiForm = [];

    // ─── Modal state ──────────────────────────────────────────────────────────
    public bool  $showModal       = false;
    public ?int  $modalKaryawanId = null;

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

            $hariMasuk = AbsenHarian::where('karyawan_id', $k->id)
                ->whereYear('tanggal', $this->filterTahun)
                ->whereMonth('tanggal', (int) $this->filterBulan)
                ->whereIn('status', ['hadir', 'dinas'])
                ->count();

            $this->gajiForm[$k->id] = [
                'nama'         => $k->nama,
                'jabatan'      => $k->jabatan,
                'hari_masuk'   => $hariMasuk,
                'gaji_pokok'   => $rec ? (string)(int)$rec->gaji_pokok  : '',
                'tunjangan'    => $rec ? (string)(int)$rec->tunjangan    : '',
                'transport'    => $rec ? (string)(int)$rec->transport    : '',
                'thr'          => $rec ? (string)(int)$rec->thr          : '',
                'potongan'     => $rec ? (string)(int)$rec->potongan     : '',
                'keterangan'   => $rec?->keterangan ?? '',
                'status_bayar' => $rec?->status_bayar ?? 'belum',
            ];
        }
    }

    /** Gross per guru = gaji_pokok + tunjangan + transport + thr */
    private function hitungGross(int $karyawanId): int
    {
        $d = $this->gajiForm[$karyawanId] ?? [];
        return (int)preg_replace('/[^0-9]/', '', $d['gaji_pokok'] ?? '0')
             + (int)preg_replace('/[^0-9]/', '', $d['tunjangan']  ?? '0')
             + (int)preg_replace('/[^0-9]/', '', $d['transport']  ?? '0')
             + (int)preg_replace('/[^0-9]/', '', $d['thr']        ?? '0');
    }

    /** Nominal (bersih) per guru = gross - potongan */
    public function hitungNominal(int $karyawanId): int
    {
        $d = $this->gajiForm[$karyawanId] ?? [];
        $gross = (int)preg_replace('/[^0-9]/', '', $d['gaji_pokok'] ?? '0')
               + (int)preg_replace('/[^0-9]/', '', $d['tunjangan']  ?? '0')
               + (int)preg_replace('/[^0-9]/', '', $d['transport']  ?? '0')
               + (int)preg_replace('/[^0-9]/', '', $d['thr']        ?? '0');
        $potongan = (int)preg_replace('/[^0-9]/', '', $d['potongan'] ?? '0');
        return $gross - $potongan;
    }

    public function totalGaji(): int
    {
        return array_sum(array_map(
            fn($id) => $this->hitungNominal($id),
            array_keys($this->gajiForm)
        ));
    }

    // ─── Modal ────────────────────────────────────────────────────────────────

    public function bukaModal(int $karyawanId): void
    {
        $this->modalKaryawanId = $karyawanId;
        $this->showModal       = true;
    }

    public function tutupModal(): void
    {
        $this->modalKaryawanId = null;
        $this->showModal       = false;
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    /**
     * Simpan semua gaji sekaligus (insert atau update).
     */
    public function simpanGaji(): void
    {
        foreach ($this->gajiForm as $karyawanId => $data) {
            $gajiPokok  = (int)preg_replace('/[^0-9]/', '', $data['gaji_pokok'] ?? '0');
            $tunjangan  = (int)preg_replace('/[^0-9]/', '', $data['tunjangan']  ?? '0');
            $transport  = (int)preg_replace('/[^0-9]/', '', $data['transport']  ?? '0');
            $thr        = (int)preg_replace('/[^0-9]/', '', $data['thr']        ?? '0');
            $potongan   = (int)preg_replace('/[^0-9]/', '', $data['potongan']   ?? '0');
            $nominalTotal = $gajiPokok + $tunjangan + $transport + $thr;

            if ($nominalTotal === 0) continue;

            GajiBulanan::updateOrCreate(
                [
                    'karyawan_id' => $karyawanId,
                    'bulan'       => $this->filterBulan,
                    'tahun'       => $this->filterTahun,
                ],
                [
                    'hari_masuk'   => $data['hari_masuk'],
                    'gaji_pokok'   => $gajiPokok,
                    'tunjangan'    => $tunjangan,
                    'transport'    => $transport,
                    'thr'          => $thr,
                    'nominal_gaji' => $nominalTotal,
                    'potongan'     => $potongan,
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

        foreach ($this->gajiForm as $id => $_) {
            $this->gajiForm[$id]['status_bayar'] = 'sudah';
        }

        unset($this->sudahAda);
        Notification::make()->title('Semua gaji ditandai sudah dibayar')->success()->send();
    }

    // ─── Label helpers ────────────────────────────────────────────────────────

    public function getBulanLabel(string $bulan): string
    {
        return [
            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember',
        ][$bulan] ?? $bulan;
    }

    /** URL untuk generate PDF slip gaji */
    public function urlSlipGaji(): string
    {
        return route('slip-gaji.pdf', [
            'bulan' => $this->filterBulan,
            'tahun' => $this->filterTahun,
        ]);
    }

    /** URL signed (30 hari) untuk slip gaji per karyawan */
    public function urlSlipGajiPerKaryawan(int $karyawanId): string
    {
        return URL::temporarySignedRoute('slip-gaji.share', now()->addDays(30), [
            'karyawanId' => $karyawanId,
            'bulan'      => $this->filterBulan,
            'tahun'      => $this->filterTahun,
        ]);
    }

    /** URL WhatsApp untuk share slip gaji per karyawan */
    public function getWhatsappUrlGaji(int $karyawanId): string
    {
        $data     = $this->gajiForm[$karyawanId] ?? [];
        $linkUrl  = $this->urlSlipGajiPerKaryawan($karyawanId);
        $bulanLbl = $this->getBulanLabel($this->filterBulan);

        $gajiPokok = (int) preg_replace('/[^0-9]/', '', $data['gaji_pokok'] ?? '0');
        $tunjangan = (int) preg_replace('/[^0-9]/', '', $data['tunjangan'] ?? '0');
        $transport = (int) preg_replace('/[^0-9]/', '', $data['transport'] ?? '0');
        $thr       = (int) preg_replace('/[^0-9]/', '', $data['thr'] ?? '0');
        $potongan  = (int) preg_replace('/[^0-9]/', '', $data['potongan'] ?? '0');
        $bersih    = $gajiPokok + $tunjangan + $transport + $thr - $potongan;

        $fmt = fn($n) => number_format($n, 0, ',', '.');

        $pesan = implode("\n", [
            'Assalamualaikum,',
            '',
            'Berikut kami sampaikan slip gaji:',
            '',
            "Nama       : {$data['nama']}",
            "Jabatan    : {$data['jabatan']}",
            "Periode    : {$bulanLbl} {$this->filterTahun}",
            "Hari Masuk : {$data['hari_masuk']} hari",
            '',
            'Rincian Gaji:',
            "Gaji Pokok : Rp {$fmt($gajiPokok)}",
            "Tunjangan  : Rp {$fmt($tunjangan)}",
            "Transport  : Rp {$fmt($transport)}",
            "THR        : Rp {$fmt($thr)}",
            "Potongan   : Rp {$fmt($potongan)}",
            "Total      : Rp {$fmt($bersih)}",
            '',
            'Silakan lihat slip gaji di tautan berikut:',
            $linkUrl,
            '',
            'Terima kasih.',
        ]);

        $teks = rawurlencode($pesan);
        return "https://wa.me/?text={$teks}";
    }
}
