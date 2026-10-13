<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanController extends Controller
{
    private function loadTtd(string $name): string
    {
        $png = storage_path("app/private/ttd/{$name}.png");
        $jpeg = storage_path("app/private/ttd/{$name}.jpeg");
        $path = $png;
        if (!file_exists($png)) {
            $path = $jpeg;
        }
        if (!file_exists($path)) return '';
        $mime = str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    public function pdf(Tagihan $tagihan)
    {
        $tagihan->load(['siswa', 'jenisPembayaran']);
        $isMultiItem = !is_null($tagihan->detail) && count($tagihan->detail) > 0;

        // Ambil semua pembayaran untuk tagihan ini (support cicilan)
        $historiPembayaran = Pembayaran::where('tagihan_id', $tagihan->id)
            ->orderBy('tanggal_bayar')
            ->get();

        $totalTerbayar = $historiPembayaran->sum('nominal');
        $sisaTagihan   = $tagihan->nominal_tagihan;
        // Total tagihan asli: multi-item = jumlah nominal detail (biar sama dengan baris rincian),
        // tagihan biasa = sisa + terbayar. (Dulu multi-item memakai sisa → salah saat cicil/lunas.)
        $nominalAsli   = $isMultiItem
            ? (float) collect($tagihan->detail)->sum(fn ($i) => (float) ($i['nominal'] ?? 0))
            : (float) $tagihan->nominal_tagihan + (float) $totalTerbayar;
        $isLunas       = $tagihan->status === 'lunas';
        $isCicilan     = $totalTerbayar > 0 && ! $isLunas;
        $isBelumBayar  = $totalTerbayar == 0 && ! $isLunas;

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        // Build detail items untuk tampilan
        $detailItems = [];
        if ($isMultiItem) {
            foreach ($tagihan->detail as $item) {
                $jenis = $item['jenis'] ?? '—';
                $bulanLabel = ($item['bulan'] ?? null)
                    ? ($bulanLabels[$item['bulan']] ?? $item['bulan'])
                    : '—';
                $tahun = $item['tahun'] ?? '—';
                $nominal = (float) ($item['nominal'] ?? 0);
                $periode = $jenis === 'SPP'
                    ? trim($bulanLabel . ' ' . $tahun)
                    : ($jenis === 'Daftar Ulang' ? $tahun : trim($bulanLabel . ' ' . $tahun));
                $detailItems[] = [
                    'jenis'   => $jenis,
                    'periode' => $periode,
                    'nominal' => $nominal,
                ];
            }
        }

        $urlTagihan   = url('/tagihan/share/' . \App\Http\Controllers\TagihanPublicController::encryptId($tagihan->id));
        $cetakTanggal = now()->format('d M Y, H:i');

        $ttdBendahara = $this->loadTtd('ttd_bendahara');

        $pdf = Pdf::loadView('tagihan.tagihan', compact(
            'tagihan',
            'historiPembayaran',
            'totalTerbayar',
            'sisaTagihan',
            'nominalAsli',
            'isLunas',
            'isCicilan',
            'isBelumBayar',
            'isMultiItem',
            'detailItems',
            'bulanLabels',
            'urlTagihan',
            'cetakTanggal',
            'ttdBendahara',
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Tagihan-' . $tagihan->siswa->nis . '.pdf');
    }

    /**
     * Cetak gabungan seluruh tagihan belum bayar (termasuk cicilan) milik satu siswa.
     */
    public function gabungan(Siswa $siswa)
    {
        $siswa->load('kelasSaatIni');

        $tagihans = Tagihan::with('jenisPembayaran')
            ->where('siswa_id', $siswa->id)
            ->where('status', 'belum_bayar')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        $pembayaranByTagihan = Pembayaran::whereIn('tagihan_id', $tagihans->pluck('id'))
            ->get()
            ->groupBy('tagihan_id');

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $rows         = [];
        $totalAsli    = 0;
        $totalTerbayar = 0;
        $totalSisa    = 0;

        foreach ($tagihans as $t) {
            $terbayar = (float) ($pembayaranByTagihan->get($t->id)?->sum('nominal') ?? 0);
            $sisa     = (float) $t->nominal_tagihan;
            $asli     = $sisa + $terbayar;

            $rows[] = [
                'jenis'    => $t->jenisPembayaran?->nama ?? 'Tagihan',
                'periode'  => $this->periodeTagihan($t, $bulanLabels),
                'total'    => $asli,
                'terbayar' => $terbayar,
                'nominal'  => $sisa,
            ];

            $totalAsli     += $asli;
            $totalTerbayar += $terbayar;
            $totalSisa     += $sisa;
        }

        $cetakTanggal = now()->format('d M Y, H:i');
        $ttdBendahara = $this->loadTtd('ttd_bendahara');

        $pdf = Pdf::loadView('tagihan.tagihan-gabungan', compact(
            'siswa',
            'rows',
            'totalAsli',
            'totalTerbayar',
            'totalSisa',
            'cetakTanggal',
            'ttdBendahara',
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Tagihan-Gabungan-' . ($siswa->nis ?: $siswa->id) . '.pdf');
    }

    private function periodeTagihan(Tagihan $tagihan, array $bulanLabels): string
    {
        $detail = $tagihan->detail ?? [];

        if (count($detail) > 0) {
            $bulan = collect($detail)->pluck('bulan')->filter()->unique()->sort()->values();

            if ($bulan->isNotEmpty()) {
                $first = $bulanLabels[$bulan->first()] ?? $bulan->first();
                $last  = $bulanLabels[$bulan->last()] ?? $bulan->last();

                return $first === $last
                    ? trim($first . ' ' . $tagihan->tahun)
                    : $first . ' – ' . $last . ' ' . $tagihan->tahun;
            }
        }

        if ($tagihan->bulan) {
            return trim(($bulanLabels[$tagihan->bulan] ?? $tagihan->bulan) . ' ' . $tagihan->tahun);
        }

        return (string) ($tagihan->tahun ?: '—');
    }
}
