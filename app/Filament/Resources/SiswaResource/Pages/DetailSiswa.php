<?php
// File: app/Filament/Resources/SiswaResource/Pages/DetailSiswa.php

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

    // ── Record ────────────────────────────────────────────────────────────────
    public Siswa $siswa;

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(int|string $record): void
    {
        // Tidak perlu eager load tagihans lagi — data diambil dari tabel pembayaran
        $this->siswa = Siswa::findOrFail($record);
    }

    // ─── History Pembayaran ───────────────────────────────────────────────────

    public function getHistoryTagihanProperty(): Collection
    {
        return Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $this->siswa->id)
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->orderByDesc('id')
            ->get();
    }

    // ─── Header actions ───────────────────────────────────────────────────────

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
                ->url(SiswaResource::getUrl('kelas', [
                    'jenjang' => $this->siswa->jenis_sekolah,
                    'kelas'   => $this->siswa->kelas,
                ])),
        ];
    }

    // ─── Title & Breadcrumb ───────────────────────────────────────────────────

    public function getTitle(): string
    {
        return $this->siswa->nama;
    }

    public function getBreadcrumbs(): array
    {
        $jenjang = $this->siswa->jenis_sekolah;
        $kelas   = $this->siswa->kelas;

        return [
            SiswaResource::getUrl('jenjang', ['jenjang' => $jenjang]) => 'Siswa ' . $jenjang,
            SiswaResource::getUrl('kelas', ['jenjang' => $jenjang, 'kelas' => $kelas]) => 'Kelas ' . $kelas,
            '#' => $this->siswa->nama,
        ];
    }
}
