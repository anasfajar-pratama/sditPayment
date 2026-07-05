<?php
// ════════════════════════════════════════════════════════════
// File: app/Filament/Resources/SiswaResource/Pages/EditSiswa.php
// Perubahan: redirect ke halaman jenjang baru, bukan activeTab lama
// ════════════════════════════════════════════════════════════

namespace App\Filament\Resources\SiswaResource\Pages;

use App\Filament\Resources\SiswaResource;
use App\Models\SiswaKelasHistory;
use App\Models\Tagihan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiswa extends EditRecord
{
    protected static string $resource = SiswaResource::class;

    protected ?string $nominalBiayaPendaftaran = null;
    protected ?string $statusBayar             = null;
    protected array $akademikData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // ─── Redirect setelah simpan ──────────────────────────────────────────────

    protected function getRedirectUrl(): string
    {
        if ($this->record->is_calon) {
            return SiswaResource::getUrl('calon');
        }

        $jenjang = $this->akademikData['_jenis_sekolah']
            ?? $this->record->kelasSaatIni?->jenis_sekolah
            ?? 'SD';

        return SiswaResource::getUrl('jenjang', ['jenjang' => $jenjang]);
    }

    // ─── Isi form dengan data tagihan & history ─────────────────────────────

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['is_calon'])) {
            $tagihan = Tagihan::where('siswa_id', $data['id'])
                ->where('jenis_pembayaran_id', 1)
                ->first();

            $data['nominal_biaya_pendaftaran'] = $tagihan?->nominal_tagihan;
            $data['status']                    = $tagihan?->status;
        }

        $current = $this->record->kelasSaatIni;
        $data['_kelas']         = $current?->kelas;
        $data['_jenis_sekolah'] = $current?->jenis_sekolah;
        $data['_tahun_ajaran']  = $current?->tahun_ajaran;

        return $data;
    }

    // ─── Ambil nominal & akademik dari form sebelum disimpan ────────────────

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['nominal_biaya_pendaftaran'])) {
            $this->nominalBiayaPendaftaran = $data['nominal_biaya_pendaftaran'];
            $this->statusBayar             = $data['status'] ?? null;
            unset($data['nominal_biaya_pendaftaran']);
        }

        $this->akademikData = [
            '_kelas'         => $data['_kelas'] ?? null,
            '_jenis_sekolah' => $data['_jenis_sekolah'] ?? null,
            '_tahun_ajaran'  => $data['_tahun_ajaran'] ?? null,
        ];
        unset($data['_kelas'], $data['_jenis_sekolah'], $data['_tahun_ajaran']);

        return $data;
    }

    // ─── Update tagihan & history setelah simpan ────────────────────────────

    protected function afterSave(): void
    {
        if ($this->record->is_calon) {
            if ($this->nominalBiayaPendaftaran !== null) {
                $tagihan = Tagihan::where('siswa_id', $this->record->id)
                    ->where('jenis_pembayaran_id', 1)
                    ->first();

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
                    ['nominal_tagihan' => $this->nominalBiayaPendaftaran]
                );
            }
            return;
        }

        if ($this->akademikData['_kelas'] && $this->akademikData['_tahun_ajaran']) {
            $parts = explode('/', $this->akademikData['_tahun_ajaran']);
            $tahunMulai = (int) ($parts[0] ?? now()->year);

            SiswaKelasHistory::updateOrCreate(
                ['siswa_id' => $this->record->id, 'tahun_ajaran' => $this->akademikData['_tahun_ajaran']],
                [
                    'kelas'         => $this->akademikData['_kelas'],
                    'tingkat'       => (int) $this->akademikData['_kelas'],
                    'jenis_sekolah' => $this->akademikData['_jenis_sekolah'],
                    'tahun_mulai'   => $tahunMulai,
                    'mutasi'        => 'naik',
                    'is_current'    => true,
                    'created_by'    => auth()->id() ?? 1,
                ]
            );
        }
    }
}
