<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/ListSiswas.php
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Siswa;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListSiswas extends ListRecords
{
    protected static string $resource = SiswaResource::class;

    // ─── Tab aktif diekspose agar bisa dibaca oleh kolom hidden() ─────────────
    // Filament menyimpannya di property 'activeTab' secara otomatis
    // tidak perlu dideklarasikan manual — sudah ada di parent class.

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Siswa / Calon Siswa'),
        ];
    }

    // ─── Definisi Tab ─────────────────────────────────────────────────────────

    public function getTabs(): array
    {
        return [
            'siswa' => Tab::make('Siswa')
                ->badge(Siswa::where('is_calon', 0)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $query->where('is_calon', 0)),

            'calon' => Tab::make('Calon Siswa')
                ->badge(Siswa::where('is_calon', 1)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn ($query) => $query->where('is_calon', 1)),
        ];
    }
}
