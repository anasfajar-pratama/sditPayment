<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/CreateSiswa.php
// Redirect setelah save → list dengan tab yang sesuai
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    // Label tombol submit utama
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    // Label tombol "Create & create another"
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    /**
     * Setelah berhasil create, redirect ke list dengan tab yang tepat:
     * - is_calon = 1 → tab calon
     * - is_calon = 0 → tab siswa
     */
    protected function getRedirectUrl(): string
    {
        $isCaon = $this->record->is_calon;

        $tab = $isCaon ? 'calon' : 'siswa';

        return $this->getResource()::getUrl('index') . '?activeTab=' . $tab;
    }

    /**
     * Setelah "Simpan & Tambah Lagi", form direset ke tab yang sama
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        $isCaon = $this->record->is_calon;
        return $isCaon ? 'Calon siswa berhasil ditambahkan' : 'Siswa berhasil ditambahkan';
    }
}
