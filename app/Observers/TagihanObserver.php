<?php

namespace App\Observers;

use App\Models\Tagihan;
use App\Models\TagihanLog;

class TagihanObserver
{
    public function created(Tagihan $tagihan): void
    {
        TagihanLog::catat(
            aksi: 'buat',
            tagihanId: $tagihan->id,
            sebelum: null,
            sesudah: $tagihan->getAttributes(),
            keterangan: $this->keterangan($tagihan, 'Buat tagihan'),
        );
    }

    public function deleted(Tagihan $tagihan): void
    {
        TagihanLog::catat(
            aksi: 'hapus',
            tagihanId: $tagihan->id,
            sebelum: $tagihan->getOriginal(),
            sesudah: null,
            keterangan: $this->keterangan($tagihan, 'Hapus tagihan'),
        );
    }

    private function keterangan(Tagihan $tagihan, string $prefix): string
    {
        $detail = $tagihan->detail ?? [];
        if (count($detail) > 0) {
            $jenis = 'Multi (' . count($detail) . ' item)';
        } else {
            $jenis = $tagihan->jenisPembayaran?->nama ?? '—';
        }

        $periode = $tagihan->bulan
            ? $this->bulanLabel($tagihan->bulan) . ' ' . $tagihan->tahun
            : (string) $tagihan->tahun;

        $nama = $tagihan->siswa?->nama ?? '—';
        $nis  = $tagihan->siswa?->nis ?? '—';

        return "{$prefix}: {$jenis} — {$periode} — {$nama} ({$nis})";
    }

    private function bulanLabel(string $bulan): string
    {
        return [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ][$bulan] ?? $bulan;
    }
}