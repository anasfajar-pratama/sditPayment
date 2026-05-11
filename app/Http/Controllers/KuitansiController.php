<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;

class KuitansiController extends Controller
{
    public function cetak(Pembayaran $pembayaran)
    {
        $pembayaran->load(['siswa', 'jenisPembayaran']);

        $isSpp = strtolower($pembayaran->jenisPembayaran?->nama ?? '') === 'spp';

        if ($pembayaran->tagihan_id) {

            $pembayaran = Pembayaran::with(['siswa', 'jenisPembayaran'])
                ->where('tagihan_id', $pembayaran->tagihan_id)
                ->latest('tanggal_bayar') // pembayaran terbaru
                ->latest('id') // backup jika tanggal sama
                ->first();
        }

        // Semua transaksi pembayaran untuk jenis + siswa + bulan/tahun yang sama
        $historiCicilan = Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $pembayaran->siswa_id)
            ->where('jenis_pembayaran_id', $pembayaran->jenis_pembayaran_id)
            ->when($pembayaran->bulan, fn ($q) => $q->where('bulan', $pembayaran->bulan))
            ->when($pembayaran->tahun, fn ($q) => $q->where('tahun', $pembayaran->tahun))
            ->orderBy('tanggal_bayar')
            ->get();

        $totalTerbayar = $historiCicilan->sum('nominal');
                   

        // Tagihan asli (untuk mendapatkan nominal asal & sisa)
        $tagihan = $pembayaran->tagihan_id
            ? Tagihan::find($pembayaran->tagihan_id)
            : Tagihan::where('siswa_id', $pembayaran->siswa_id)
                ->where('jenis_pembayaran_id', $pembayaran->jenis_pembayaran_id)
                ->when($pembayaran->bulan, fn ($q) => $q->where('bulan', $pembayaran->bulan))
                ->when($pembayaran->tahun, fn ($q) => $q->where('tahun', $pembayaran->tahun))
                ->first();

        // Nominal tagihan asli (sebelum cicilan mengurangi)
        $nominalAsli = $tagihan
            ? $tagihan->nominal_tagihan + ($tagihan->status === 'lunas' ? 0 : 0)
            : $totalTerbayar;

        // Hitung nominal asli = sisa tagihan + total terbayar
        if ($tagihan && $tagihan->status !== 'lunas') {
            $nominalAsli = $tagihan->nominal_tagihan + $totalTerbayar;
        } elseif ($tagihan && $tagihan->status === 'lunas') {
            $nominalAsli = $totalTerbayar; // sudah lunas semua
        }

        $sisaTagihan = max(0, $nominalAsli - $totalTerbayar);

        $bulanLabels = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
        ];

        $data = [
            'pembayaran'     => $pembayaran,
            'isSpp'          => $isSpp,
            'historiCicilan' => $historiCicilan,
            'totalTerbayar'  => $totalTerbayar,
            'nominalAsli'    => $nominalAsli,
            'sisaTagihan'    => $sisaTagihan,
            'bulanLabels'    => $bulanLabels,
            'isLunas'        => $pembayaran->status === 'lunas',
            'isCicilan'      => $pembayaran->status === 'cicilan',
            'cetakTanggal'   => now()->format('d M Y H:i'),
        ];

        $pdf = Pdf::loadView('pdf.kuitansi', $data)
            ->setPaper('a5', 'portrait');

        $filename = 'kuitansi-' . $pembayaran->siswa->nis . '-' . $pembayaran->id . '.pdf';

        return $pdf->stream($filename);
    }
}
