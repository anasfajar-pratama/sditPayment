<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/CreateSiswa.php
// Redirect setelah save → list dengan tab yang sesuai
// + otomatis buat tagihan biaya pendaftaran untuk calon siswa
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Tagihan;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    /**
     * Simpan sementara nominal biaya pendaftaran sebelum record dibuat.
     * Field ini tidak ada di tabel siswa, jadi diambil manual dari form data.
     */
    protected ?string $nominalBiayaPendaftaran = null;

    // Label tombol submit utama
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    // Label tombol "Create & create another"
    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    /**
     * Sebelum record Siswa dibuat:
     * - Ambil & simpan nominal_biaya_pendaftaran dari form data
     * - Hapus field tersebut dari data agar tidak error saat insert ke tabel siswa
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['nominal_biaya_pendaftaran'])) {
            $this->nominalBiayaPendaftaran = $data['nominal_biaya_pendaftaran'];
            unset($data['nominal_biaya_pendaftaran']);
        }

        return $data;
    }

    /**
     * Setelah record Siswa berhasil dibuat:
     * - Jika is_calon = true, buat row tagihan biaya pendaftaran (jenis_pembayaran_id = 1)
     */
    protected function afterCreate(): void
    {
        if ($this->record->is_calon && $this->nominalBiayaPendaftaran !== null) {
            Tagihan::create([
                'siswa_id'            => $this->record->id,
                'jenis_pembayaran_id' => 1,
                'bulan'               => now()->format('m'),
                'tahun'               => now()->format('Y'),
                'nominal_tagihan'     => $this->nominalBiayaPendaftaran,
                'status'              => 'belum_bayar',
            ]);
        }
    }

    /**
     * Setelah berhasil create, redirect ke list dengan tab yang tepat:
     * - is_calon = 1 → tab calon
     * - is_calon = 0 → tab siswa
     */
    protected function getRedirectUrl(): string
    {
        $isCalon = $this->record->is_calon;

        $tab = $isCalon ? 'calon' : 'siswa';

        return $this->getResource()::getUrl('index') . '?activeTab=' . $tab;
    }

    /**
     * Notifikasi setelah simpan
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        $isCalon = $this->record->is_calon;
        return $isCalon
            ? 'Calon siswa & Tagihan Biaya Pendaftaran Berhasil ditambahkan'
            : 'Siswa berhasil ditambahkan';
    }
}
