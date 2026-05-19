<?php
// File: app/Filament/Resources/DonaturResource/Pages/CreateDonatur.php

namespace App\Filament\Resources\DonaturResource\Pages;

use App\Filament\Resources\DonaturResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonatur extends CreateRecord
{
    protected static string $resource = DonaturResource::class;

    public function getTitle(): string
    {
        return 'Tambah Donatur';
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
        return DonaturResource::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Donatur berhasil ditambahkan';
    }
}
