<?php
// File: app/Filament/Resources/DonaturResource/Pages/ListDonatur.php

namespace App\Filament\Resources\DonaturResource\Pages;

use App\Filament\Resources\DonaturResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonatur extends ListRecords
{
    protected static string $resource = DonaturResource::class;

    public function getTitle(): string
    {
        return 'Daftar Donatur';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Donatur'),
        ];
    }
}
