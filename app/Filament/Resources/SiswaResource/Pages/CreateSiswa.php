<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/CreateSiswa.php
// Perubahan: redirect ke halaman jenjang baru, bukan activeTab lama
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Tagihan;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected ?string $nominalBiayaPendaftaran = null;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->label('Simpan & Tambah Lagi');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['nominal_biaya_pendaftaran'])) {
            $this->nominalBiayaPendaftaran = $data['nominal_biaya_pendaftaran'];
            unset($data['nominal_biaya_pendaftaran']);
        }

        return $data;
    }

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

    // ─── Redirect setelah simpan ──────────────────────────────────────────────

    protected function getRedirectUrl(): string
    {
        // Calon siswa → halaman Calon Siswa
        if ($this->record->is_calon) {
            return SiswaResource::getUrl('calon');
        }

        // Siswa aktif → halaman kartu kelas sesuai jenjang
        $jenjang = $this->record->jenis_sekolah ?? 'SD';

        return SiswaResource::getUrl('jenjang', ['jenjang' => $jenjang]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->record->is_calon
            ? 'Calon siswa & tagihan biaya pendaftaran berhasil ditambahkan'
            : 'Siswa berhasil ditambahkan';
    }
}
