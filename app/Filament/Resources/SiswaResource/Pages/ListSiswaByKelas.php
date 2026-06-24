<?php
// File: app/Filament/Resources/SiswaResource/Pages/ListSiswaByKelas.php
// Perubahan v2: klik baris → halaman detail siswa (bukan edit)

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListSiswaByKelas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    public string $jenjang = '';
    public string $kelas   = '';

    public function mount(): void
    {
        $this->jenjang = strtoupper(request()->route('jenjang') ?? '');
        $this->kelas   = strtoupper(request()->route('kelas') ?? '');

        parent::mount();
    }

    // ─── Override recordUrl → halaman detail, bukan edit ──────────────────────
    // Cara Filament v4: override table() dan set ->recordUrl() di sini

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl(
                fn (Siswa $record): string =>
                    SiswaResource::getUrl('detail', ['record' => $record])
            );
    }

    // ─── Filter query ─────────────────────────────────────────────────────────

    protected function getTableQuery(): Builder
    {
        return Siswa::query()
            ->where('jenis_sekolah', $this->jenjang)
            ->where('kelas', $this->kelas)
            ->where('is_calon', 0);
    }

    // ─── Breadcrumb ───────────────────────────────────────────────────────────

    public function getBreadcrumbs(): array
    {
        return [
            SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang])
                => 'Siswa ' . $this->jenjang,
            SiswaResource::getUrl('kelas', ['jenjang' => $this->jenjang, 'kelas' => $this->kelas])
                => 'Kelas ' . $this->kelas,
        ];
    }

    // ─── Title ────────────────────────────────────────────────────────────────

    public function getTitle(): string
    {
        return 'Kelas ' . $this->kelas . ' — ' . $this->jenjang;
    }

    // ─── Header actions ───────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('← Kembali ke ' . $this->jenjang)
                ->color('gray')
                ->url(SiswaResource::getUrl('jenjang', ['jenjang' => $this->jenjang])),

            Action::make('tambah_siswa')
                ->label('Tambah Siswa')
                ->icon('heroicon-o-plus')
                ->url(SiswaResource::getUrl('create')),
        ];
    }
}
