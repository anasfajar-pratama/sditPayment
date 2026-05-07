<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $isCaon = $this->record->is_calon;

        $tab = $isCaon ? 'calon' : 'siswa';

        return $this->getResource()::getUrl('index') . '?activeTab=' . $tab;
    }
}
