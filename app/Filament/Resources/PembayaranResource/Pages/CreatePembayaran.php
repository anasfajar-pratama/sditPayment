<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/PembayaranResource/Pages/CreatePembayaran.php
// (UPDATE: tambahkan auto-posting ke kas harian)
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use App\Models\KasHarian;
use App\Models\Tagihan;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaran extends CreateRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        $record->load(['siswa', 'jenisPembayaran']);

        // 1. Update status tagihan jika lunas
        if (($record->status === 'lunas' || $record->status === 'cicilan') && $record->tagihan_id) {
            if ($record->status === 'lunas') {
                Tagihan::where('id', $record->tagihan_id)->update(['status' => 'lunas']);
            } else {
                $sisa = Tagihan::find($record->tagihan_id)?->nominal_tagihan - $record->nominal;
                Tagihan::where('id', $record->tagihan_id)->update([
                    'nominal_tagihan' => max(0, $sisa),
                ]);
            }
        }

        // 2. Auto-posting ke kas harian
        KasHarian::postingDariPembayaran($record);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
