<?php
// File: app/Filament/Resources/DonaturResource/Pages/EditDonatur.php

namespace App\Filament\Resources\DonaturResource\Pages;

use App\Filament\Resources\DonaturResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDonatur extends EditRecord
{
    protected static string $resource = DonaturResource::class;

    public function getTitle(): string
    {
        return 'Edit Donatur: ' . $this->record->nama;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('detail')
                ->label('Lihat Detail & Donasi')
                ->icon('heroicon-o-eye')
                ->url(DonaturResource::getUrl('detail', ['record' => $this->record])),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return DonaturResource::getUrl('detail', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data donatur berhasil diperbarui';
    }
}
