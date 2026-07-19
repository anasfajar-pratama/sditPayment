<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
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
        $nominalAsli   = $isMultiItem ? $tagihan->nominal_tagihan : $tagihan->nominal_tagihan + $totalTerbayar;
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
}
