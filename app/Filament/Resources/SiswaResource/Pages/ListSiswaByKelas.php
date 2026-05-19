<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/ListSiswaByKelas.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSiswaByKelas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    // Parameter dari URL: /admin/siswas/jenjang/{jenjang}/kelas/{kelas}
    public string $jenjang = '';
    public string $kelas   = '';

    public function mount(): void
    {
        // Ambil parameter dari URL route, bukan dari method signature
        // (ListRecords::mount() tidak boleh punya parameter)
        $this->jenjang = strtoupper(request()->route('jenjang') ?? '');
        $this->kelas   = strtoupper(request()->route('kelas') ?? '');

        parent::mount();
    }

    // ─── Filter query berdasarkan jenjang + kelas ─────────────────────────────

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

    // ─── Actions ──────────────────────────────────────────────────────────────

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
