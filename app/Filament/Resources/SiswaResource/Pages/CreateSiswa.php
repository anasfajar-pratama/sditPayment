<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/CreateSiswa.php
// Perubahan: redirect ke halaman jenjang baru, bukan activeTab lama
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\SiswaKelasHistory;
use App\Models\Tagihan;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswa extends CreateRecord
{
    protected static string $resource = SiswaResource::class;

    protected ?string $nominalBiayaPendaftaran = null;
    protected array $akademikData = [];

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

        $this->akademikData = [
            '_kelas'         => $data['_kelas'] ?? null,
            '_jenis_sekolah' => $data['_jenis_sekolah'] ?? null,
            '_tahun_ajaran'  => $data['_tahun_ajaran'] ?? $this->defaultTahunAjaran(),
        ];
        unset($data['_kelas'], $data['_jenis_sekolah'], $data['_tahun_ajaran']);

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

        if (! $this->record->is_calon && $this->akademikData['_kelas']) {
            $parts = explode('/', $this->akademikData['_tahun_ajaran']);
            $tahunMulai = (int) ($parts[0] ?? now()->year);

            SiswaKelasHistory::create([
                'siswa_id'      => $this->record->id,
                'kelas'         => $this->akademikData['_kelas'],
                'tingkat'       => (int) $this->akademikData['_kelas'],
                'jenis_sekolah' => $this->akademikData['_jenis_sekolah'],
                'tahun_ajaran'  => $this->akademikData['_tahun_ajaran'],
                'tahun_mulai'   => $tahunMulai,
                'mutasi'        => 'naik',
                'is_current'    => true,
                'created_by'    => auth()->id() ?? 1,
            ]);
        }
    }

    protected function defaultTahunAjaran(): string
    {
        $now = now();
        $start = $now->month >= 7 ? $now->year : $now->year - 1;
        return "{$start}/" . ($start + 1);
    }

    // ─── Redirect setelah simpan ──────────────────────────────────────────────

    protected function getRedirectUrl(): string
    {
        if ($this->record->is_calon) {
            return SiswaResource::getUrl('calon');
        }

        $jenjang = $this->akademikData['_jenis_sekolah'] ?? 'SD';
        return SiswaResource::getUrl('jenjang', ['jenjang' => $jenjang]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return $this->record->is_calon
            ? 'Calon siswa & tagihan biaya pendaftaran berhasil ditambahkan'
            : 'Siswa berhasil ditambahkan';
    }
}
