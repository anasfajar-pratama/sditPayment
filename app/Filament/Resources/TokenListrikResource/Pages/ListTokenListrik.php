<?php
// File: app/Filament/Resources/TokenListrikResource/Pages/ListTokenListrik.php

namespace App\Filament\Resources\TokenListrikResource\Pages;

use App\Filament\Resources\TokenListrikResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTokenListrik extends ListRecords
{
    protected static string $resource = TokenListrikResource::class;

    public function getTitle(): string
    {
        return 'Daftar Token Listrik';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Token Listrik'),
        ];
    }
}
