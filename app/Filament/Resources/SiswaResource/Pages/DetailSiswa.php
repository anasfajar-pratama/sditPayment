<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

class DetailSiswa extends Page
{
    protected static string $resource = SiswaResource::class;
    protected static string $view     = 'filament.resources.siswa-resource.pages.detail-siswa';

    public Siswa $siswa;

    public function mount(int|string $record): void
    {
        $this->siswa = Siswa::with('kelasSaatIni')->findOrFail($record);
    }

    public function getHistoryTagihanProperty(): Collection
    {
        return Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_siswa')
                ->label('Edit Siswa')
                ->icon('heroicon-o-pencil-square')
                ->url(SiswaResource::getUrl('edit', ['record' => $this->siswa])),

            Action::make('kembali')
                ->label('← Kembali ke Kelas')
                ->color('gray')
                ->url(fn () => $this->kembaliUrl()),
        ];
    }

    protected function kembaliUrl(): string
    {
        $current = $this->siswa->kelasSaatIni;
        if ($current) {
            return SiswaResource::getUrl('kelas', [
                'jenjang' => $current->jenis_sekolah,
                'kelas'   => $current->kelas,
            ]);
        }

        $calonJenis = $this->siswa->calon_jenis;
        if ($calonJenis) {
            return SiswaResource::getUrl('jenjang', ['jenjang' => strtoupper($calonJenis)]);
        }

        return SiswaResource::getUrl('index');
    }

    public function getTitle(): string
    {
        return $this->siswa->nama;
    }

    public function getBreadcrumbs(): array
    {
        $current = $this->siswa->kelasSaatIni;
        $jenjang = $current?->jenis_sekolah;
        $kelas   = $current?->kelas;

        if (! $jenjang) {
            return ['#' => $this->siswa->nama];
        }

        return [
            SiswaResource::getUrl('jenjang', ['jenjang' => $jenjang]) => 'Siswa ' . $jenjang,
            SiswaResource::getUrl('kelas', ['jenjang' => $jenjang, 'kelas' => $kelas]) => 'Kelas ' . $kelas,
            '#' => $this->siswa->nama,
        ];
    }
}
