<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ListSiswaByJenjang extends Page
{
    protected static string $resource = SiswaResource::class;

    protected static string $view = 'filament.resources.siswa-resource.pages.list-siswa-by-jenjang';

    public string $jenjang = '';
    public string $filterTahunAjaran = '';
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

    public function defaultTahunAjaran(): string
    {
        $latest = SiswaKelasHistory::where('jenis_sekolah', $this->jenjang)
            ->where('is_current', true)
            ->orderByDesc('tahun_ajaran')
            ->value('tahun_ajaran');

        if ($latest) return $latest;

        $now   = now();
        $start = $now->month >= 7 ? $now->year : $now->year - 1;
        return "{$start}/" . ($start + 1);
    }

    public function tahunAjaranList(): array
    {
        return SiswaKelasHistory::where('jenis_sekolah', $this->jenjang)
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran')
            ->toArray();
    }

    protected function loadKelasData(): array
    {
        $query = SiswaKelasHistory::query()
            ->where('jenis_sekolah', $this->jenjang);

        if ($this->filterTahunAjaran) {
            $query->where('tahun_ajaran', $this->filterTahunAjaran);
        } else {
            $query->where('is_current', true);
        }

        $rows = $query
            ->selectRaw('kelas, COUNT(*) as jumlah')
            ->groupBy('kelas')
            ->orderByRaw('LENGTH(kelas), kelas')
            ->get();

        return $rows->map(fn ($row) => [
            'kelas'  => $row->kelas,
            'jumlah' => $row->jumlah,
            'url'    => $this->filterTahunAjaran
                ? SiswaResource::getUrl('kelas', [
                    'jenjang' => $this->jenjang,
                    'kelas'   => $row->kelas,
                ]) . '?tahun_ajaran=' . urlencode($this->filterTahunAjaran)
                : SiswaResource::getUrl('kelas', [
                    'jenjang' => $this->jenjang,
                    'kelas'   => $row->kelas,
                ]),
        ])->toArray();
    }

    public function getBreadcrumbs(): array
    {
        return [
            SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang]) => 'Siswa ' . $this->jenjang,
            '#' => 'Pilih Kelas',
        ];
    }

    public function getTitle(): string
    {
        return 'Siswa ' . $this->jenjang;
    }

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
