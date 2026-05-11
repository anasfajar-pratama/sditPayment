<?php

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Tagihan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;
    protected ?string $nominalBiayaPendaftaran = null;
    protected ?string $statusBayar = null;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['is_calon'])) {
            $tagihan = Tagihan::where('siswa_id', $data['id'])
                ->where('jenis_pembayaran_id', 1)
                ->first();

            $data['nominal_biaya_pendaftaran'] = $tagihan?->nominal_tagihan;
            $data['status'] = $tagihan?->status;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['nominal_biaya_pendaftaran'])) {
            // Simpan sebagai string — tidak di-cast ke int/float agar tidak kehilangan presisi
            $this->nominalBiayaPendaftaran = $data['nominal_biaya_pendaftaran'];
            $this->statusBayar = $data['status'];
            unset($data['nominal_biaya_pendaftaran']);
           
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->record->is_calon || $this->nominalBiayaPendaftaran === null) {
            return;
        }

        $tagihan = Tagihan::where('siswa_id', $this->record->id)
            ->where('jenis_pembayaran_id', 1)
            ->first();

        // Proteksi server-side: jika sudah lunas, jangan ubah nominal
        if ($tagihan && $tagihan->status === 'lunas') {
            return;
        }

        Tagihan::updateOrCreate(
            [
                'siswa_id'            => $this->record->id,
                'jenis_pembayaran_id' => 1,
                'bulan'               => now()->format('m'),  
                'tahun'               => now()->format('Y'), 
            ],
            [
                'nominal_tagihan' => $this->nominalBiayaPendaftaran,
            ]
        );
    }

}
