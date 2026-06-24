<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/EditSiswa.php
// Perubahan: redirect ke halaman jenjang baru, bukan activeTab lama
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\Tagihan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected ?string $nominalBiayaPendaftaran = null;
    protected ?string $statusBayar             = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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

    // ─── Isi form dengan data tagihan (untuk calon siswa) ────────────────────

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['is_calon'])) {
            $tagihan = Tagihan::where('siswa_id', $data['id'])
                ->where('jenis_pembayaran_id', 1)
                ->first();

            $data['nominal_biaya_pendaftaran'] = $tagihan?->nominal_tagihan;
            $data['status']                    = $tagihan?->status;
        }

        return $data;
    }

    // ─── Ambil nominal dari form sebelum disimpan ─────────────────────────────

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['nominal_biaya_pendaftaran'])) {
            $this->nominalBiayaPendaftaran = $data['nominal_biaya_pendaftaran'];
            $this->statusBayar             = $data['status'] ?? null;
            unset($data['nominal_biaya_pendaftaran']);
        }

        return $data;
    }

    // ─── Update tagihan setelah simpan ───────────────────────────────────────

    protected function afterSave(): void
    {
        if (! $this->record->is_calon || $this->nominalBiayaPendaftaran === null) {
            return;
        }

        $tagihan = Tagihan::where('siswa_id', $this->record->id)
            ->where('jenis_pembayaran_id', 1)
            ->first();

        // Proteksi: jika sudah lunas, jangan ubah nominal
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
