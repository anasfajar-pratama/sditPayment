<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanController extends Controller
{
    private function loadTtd(string $file): string
    {
        $path = storage_path('app/private/ttd/' . $file);
        if (!file_exists($path)) return '';
        return base64_encode(file_get_contents($path));
    }

    public function pdf(Tagihan $tagihan)
    {
        $tagihan->load(['siswa', 'jenisPembayaran']);

        // Ambil semua pembayaran untuk tagihan ini (support cicilan)
        $historiPembayaran = Pembayaran::where('tagihan_id', $tagihan->id)
            ->orderBy('tanggal_bayar')
            ->get();

        $totalTerbayar = $historiPembayaran->sum('nominal');
        // $sisaTagihan   = max(0, $tagihan->nominal_tagihan - $totalTerbayar);
        // nominal_tagihan di DB sudah dikurangi setiap cicilan,
        // sehingga: nominal asli = sisa (DB) + sudah terbayar
        $sisaTagihan   = $tagihan->nominal_tagihan;           // sisa yg tersimpan di DB
        $nominalAsli   = $tagihan->nominal_tagihan + $totalTerbayar; // total tagihan awal
        $isLunas       = $tagihan->status === 'lunas';
        $isCicilan     = $totalTerbayar > 0 && ! $isLunas;
        $isBelumBayar  = $totalTerbayar == 0 && ! $isLunas;

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        // Kirim URL saja ke blade; QR di-generate inline di blade (SVG, tanpa imagick)
        // $urlTagihan   = url('/admin/tagihans/' . $tagihan->id);
        // URL publik (tanpa login) — sama persis dengan link yang disalin dari tombol salin_link
        $urlTagihan   = url('/tagihan/share/' . \App\Http\Controllers\TagihanPublicController::encryptId($tagihan->id));
        $cetakTanggal = now()->format('d M Y, H:i');

        $ttdBendahara = $this->loadTtd('bendahara.jpeg');
        $ttdKepsek    = $this->loadTtd('kepalasekolah.jpeg');

        $pdf = Pdf::loadView('tagihan.tagihan', compact(
            'tagihan',
            'historiPembayaran',
            'totalTerbayar',
            'sisaTagihan',
            'nominalAsli',
            'isLunas',
            'isCicilan',
            'isBelumBayar',
            'bulanLabels',
            'urlTagihan',
            'cetakTanggal',
            'ttdBendahara',
            'ttdKepsek',
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('Tagihan-' . $tagihan->siswa->nis . '.pdf');
    }
}
