<?php
// File: app/Filament/Resources/TokenListrikResource/Pages/CreateTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Filament\Resources\TokenListrikResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTokenListrik extends CreateRecord
{
    protected static string $resource = TokenListrikResource::class;

    public function getTitle(): string
    {
        return 'Tambah Token Listrik';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->label('Simpan & Tambah Lagi');
    }

    protected function getRedirectUrl(): string
    {
        return TokenListrikResource::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Token listrik berhasil ditambahkan';
    }
}
