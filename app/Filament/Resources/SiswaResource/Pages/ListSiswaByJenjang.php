<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/ListSiswaByJenjang.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ListSiswaByJenjang extends Page
{
    protected static string $resource = SiswaResource::class;

    // Blade view yang akan dipakai — lihat: resources/views/filament/resources/siswa-resource/pages/list-siswa-by-jenjang.blade.php
    protected static string $view = 'filament.resources.siswa-resource.pages.list-siswa-by-jenjang';

    // Parameter dari URL: /admin/siswas/jenjang/{jenjang}
    public string $jenjang = '';

    // Filter tahun ajaran
    public string $filterTahunAjaran = '';

    // Data kelas yang akan dikirim ke view
    public array $kelasData = [];

    public function mount(string $jenjang): void
    {
        $this->jenjang = strtoupper($jenjang);
        $this->filterTahunAjaran = $this->defaultTahunAjaran();
        $this->kelasData = $this->loadKelasData();
    }

    public function updatedFilterTahunAjaran(): void
    {
        $this->kelasData = $this->loadKelasData();
    }

    // ─── Default tahun ajaran (terbaru dari DB, atau hitung dari tanggal) ────

    public function defaultTahunAjaran(): string
    {
        $latest = Siswa::where('jenis_sekolah', $this->jenjang)
            ->whereNotNull('tahun_ajaran')
            ->orderByDesc('tahun_ajaran')
            ->value('tahun_ajaran');

        if ($latest) return $latest;

        $now   = now();
        $start = $now->month >= 7 ? $now->year : $now->year - 1;
        return "{$start}/" . ($start + 1);
    }

    // ─── Daftar tahun ajaran yang tersedia ──────────────────────────────────

    public function tahunAjaranList(): array
    {
        return Siswa::where('jenis_sekolah', $this->jenjang)
            ->whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran')
            ->toArray();
    }

    // ─── Muat data kelas dari DB ──────────────────────────────────────────────

    protected function loadKelasData(): array
    {
        $query = Siswa::query()
            ->where('jenis_sekolah', $this->jenjang)
            ->where('is_calon', 0)
            ->whereNotNull('kelas');

        if ($this->filterTahunAjaran) {
            $query->where('tahun_ajaran', $this->filterTahunAjaran);
        }

        $rows = $query
            ->selectRaw('kelas, COUNT(*) as jumlah')
            ->groupBy('kelas')
            ->orderByRaw('LENGTH(kelas), kelas')
            ->get();

        return $rows->map(fn ($row) => [
            'kelas'  => $row->kelas,
            'jumlah' => $row->jumlah,
            'url'    => SiswaResource::getUrl('kelas', [
                'jenjang' => $this->jenjang,
                'kelas'   => $row->kelas,
            ]),
        ])->toArray();
    }

    // ─── Breadcrumb ───────────────────────────────────────────────────────────

    public function getBreadcrumbs(): array
    {
        return [
            SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang]) => 'Siswa ' . $this->jenjang,
            '#' => 'Pilih Kelas',
        ];
    }

    // ─── Title halaman ────────────────────────────────────────────────────────

    public function getTitle(): string
    {
        return 'Siswa ' . $this->jenjang;
    }

    // ─── Warna badge per jenjang ─────────────────────────────────────────────

    public function getJenjangColor(): string
    {
        return match ($this->jenjang) {
            'SD'   => 'text-green-700 bg-green-100',
            'SMP'  => 'text-blue-700 bg-blue-100',
            'DTA'  => 'text-yellow-700 bg-yellow-100',
            'PAUD' => 'text-red-700 bg-red-100',
            default => 'text-gray-700 bg-gray-100',
        };
    }

    // ─── Action button ────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_siswa')
                ->label('Tambah Siswa ' . $this->jenjang)
                ->icon('heroicon-o-plus')
                ->url(SiswaResource::getUrl('create')),
        ];
    }
}
