<?php

namespace App\Filament\Pages;

use App\Models\AbsenHarian;
use App\Models\Donasi;
use App\Models\GajiBulanan;
use App\Models\KasHarian;
use App\Models\Karyawan;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class Dashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Dashboard';
    protected static ?int    $navigationSort  = -1;

    protected static string $view = 'filament.pages.dashboard';

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function bulan(): string { return now()->format('m'); }
    private function tahun(): string { return now()->format('Y'); }

    // ─── SISWA ────────────────────────────────────────────────────────────────

    #[Computed]
    public function totalSiswaAktif(): int
    {
        return Siswa::where('is_calon', false)->where('status_aktif', true)->count();
    }

    #[Computed]
    public function siswaPerJenjang(): array
    {
        $jenjang = ['SD', 'SMP', 'DTA', 'PAUD'];
        $result  = [];
        foreach ($jenjang as $j) {
            $result[$j] = Siswa::where('is_calon', false)
                ->where('status_aktif', true)
                ->where('jenis_sekolah', $j)
                ->count();
        }
        return $result;
    }

    #[Computed]
    public function totalCalonSiswa(): int
    {
        return Siswa::where('is_calon', true)->count();
    }

    #[Computed]
    public function calonPerJenjang(): array
    {
        return Siswa::where('is_calon', true)
            ->selectRaw('calon_jenis, COUNT(*) as total')
            ->groupBy('calon_jenis')
            ->pluck('total', 'calon_jenis')
            ->toArray();
    }

    // ─── TAGIHAN ──────────────────────────────────────────────────────────────

    #[Computed]
    public function totalTagihanBelumBayar(): int
    {
        return Tagihan::where('status', 'belum_bayar')->count();
    }

    #[Computed]
    public function nominalTagihanBelumBayar(): float
    {
        return (float) Tagihan::where('status', 'belum_bayar')->sum('nominal_tagihan');
    }

    // ─── PEMBAYARAN ───────────────────────────────────────────────────────────

    #[Computed]
    public function pemasukanHariIni(): float
    {
        // return (float) Pembayaran::whereDate('tanggal_bayar', today())->sum('nominal');
        return (float) Pembayaran::whereDate('tanggal_bayar', today())
            ->where('bulan', sprintf('%02d', now()->month))
            ->where('tahun', now()->year)
            ->sum('nominal');
    }

    #[Computed]
    public function pemasukanBulanIni(): float
    {
        return (float) KasHarian::where('bulan', sprintf('%02d', $this->bulan()))
            ->where('tahun', $this->tahun())
            ->whereNotNull('debit')
            ->sum('debit');
    }

    #[Computed]
    public function pengeluaranBulanIni(): float
    {
        return (float) KasHarian::whereMonth('tanggal', $this->bulan())
            ->whereYear('tanggal', $this->tahun())
            ->whereNotNull('kredit')
            ->sum('kredit');
    }

    #[Computed]
    public function saldoBulanIni(): float
    {
        return $this->pemasukanBulanIni - $this->pengeluaranBulanIni;
    }

    #[Computed]
    public function donasiBulanIni(): float
    {
        return (float) KasHarian::where('bulan', sprintf('%02d', $this->bulan()))
            ->where('tahun', $this->tahun())
            ->where('akun_id','7')
            ->sum('debit');
    }

    #[Computed]
    public function pembayaranTerbaru(): \Illuminate\Support\Collection
    {
        return Pembayaran::with(['siswa', 'jenisPembayaran'])
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    // ─── KARYAWAN ─────────────────────────────────────────────────────────────

    #[Computed]
    public function totalKaryawanAktif(): int
    {
        return Karyawan::where('status', 'aktif')->count();
    }

    #[Computed]
    public function absensiHariIni(): array
    {
        $data = AbsenHarian::whereDate('tanggal', today())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'hadir' => $data['hadir'] ?? 0,
            'izin'  => $data['izin']  ?? 0,
            'sakit' => $data['sakit'] ?? 0,
            'alpha' => $data['alpha'] ?? 0,
        ];
    }

    #[Computed]
    public function gajiTerbayarBulanIni(): float
    {
        return (float) GajiBulanan::where('bulan', $this->bulan())
            ->where('tahun', $this->tahun())
            ->where('status_bayar', 'sudah_bayar')
            ->sum('nominal_gaji');
    }

    // ─── GRAFIK 6 BULAN ───────────────────────────────────────────────────────

    #[Computed]
    public function kasEnamBulan(): array
    {
        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $tgl    = now()->subMonths($i);
            $bulan  = $tgl->format('m');
            $tahun  = $tgl->format('Y');
            $masuk  = (float) KasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->whereNotNull('debit')->sum('debit');
            $keluar = (float) KasHarian::whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->whereNotNull('kredit')->sum('kredit');
            $result[] = [
                'label'  => $this->getBulanShort($bulan),
                'masuk'  => $masuk,
                'keluar' => $keluar,
            ];
        }
        return $result;
    }

    // ─── Helper label ─────────────────────────────────────────────────────────

    public function getBulanLabel(): string
    {
        return [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ][$this->bulan()] . ' ' . $this->tahun();
    }

    public function getBulanShort(string $bulan): string
    {
        return [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Ags',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ][$bulan] ?? $bulan;
    }
}
