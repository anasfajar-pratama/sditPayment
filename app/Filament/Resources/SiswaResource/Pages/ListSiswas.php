<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/ListSiswas.php
// (versi sederhana — halaman ini sekarang hanya sebagai fallback/index)
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    public function getTitle(): string
    {
        return 'Semua Siswa';
    }

    // Redirect pengguna ke halaman jenjang pertama jika buka /siswas langsung
    // (opsional — hapus mount() ini jika ingin tetap tampilkan semua siswa)
    public function mount(): void
    {
        parent::mount();

        // Redirect ke SD sebagai halaman default
        // redirect(SiswaResource::getUrl('jenjang', ['jenjang' => 'SD']))->send();
        $this->redirect(SiswaResource::getUrl('jenjang', ['jenjang' => 'SD']), navigate: true);
        // exit;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_siswa')
                ->label('Tambah Siswa / Calon Siswa')
                ->icon('heroicon-o-plus')
                ->url(SiswaResource::getUrl('create')),
        ];
    }
}
