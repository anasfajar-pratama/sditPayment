<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\Tagihan;
use Filament\Resources\Pages\EditRecord;

class EditPembayaran extends EditRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        if ($record->tagihan_id) {
            $statusTagihan = $record->status === 'lunas' ? 'lunas' : 'belum_bayar';
            Tagihan::where('id', $record->tagihan_id)
                ->update(['status' => $statusTagihan]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}