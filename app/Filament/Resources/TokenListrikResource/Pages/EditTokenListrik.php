<?php
// File: app/Filament/Resources/TokenListrikResource/Pages/EditTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Filament\Resources\TokenListrikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTokenListrik extends EditRecord
{
    protected static string $resource = TokenListrikResource::class;

    public function getTitle(): string
    {
        return 'Edit Token: ' . $this->record->nama_ruangan;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('detail')
                ->label('Lihat Detail & History')
                ->icon('heroicon-o-eye')
                ->url(TokenListrikResource::getUrl('detail', ['record' => $this->record])),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return TokenListrikResource::getUrl('detail', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data token listrik berhasil diperbarui';
    }
}
