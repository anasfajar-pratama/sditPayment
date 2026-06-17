<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;

class KuitansiController extends Controller
{
    // ── Method lama (butuh login) ─────────────────────────────
    public function cetak(Pembayaran $pembayaran)
    {
        $data = $this->buildData($pembayaran);
        $pdf = Pdf::loadView('pdf.kuitansi', $data)->setPaper('a5', 'landscape');
        $filename = 'kuitansi-' . $data['pembayaran']->siswa->nis . '-' . $data['pembayaran']->id . '.pdf';
        return $pdf->stream($filename);
    }

    // ── Method baru (publik, untuk barcode scan) ──────────────
    public function pdf(Pembayaran $pembayaran)
    {
        $token = request('_token');

        \DB::table('pdf_links')
        ->where('token', $token)
        ->increment('jumlah_view');

        if (!request()->has('_internal')) {
            abort(404);
        }

        $link = \DB::table('pdf_links')
            ->where('token', $token)
            ->where('jenis', 'kuitansi')
            ->where('expired_at', '>', now())
            ->first();

        if($pembayaran->id <> $link->pdf_id){
            abort(404);
        }

        $data = $this->buildData($pembayaran);
        $pdf = Pdf::loadView('pdf.kuitansi', $data)->setPaper('a5', 'landscape');
        $filename = 'kuitansi-' . $data['pembayaran']->siswa->nis . '-' . $data['pembayaran']->id . '.pdf';
        return $pdf->stream($filename);
    }

    // ── Logic bersama ─────────────────────────────────────────
    private function loadTtd(string $file): string
    {
        $path = storage_path('app/private/ttd/' . $file);
        if (!file_exists($path)) return '';
        return base64_encode(file_get_contents($path));
    }

    private function buildData(Pembayaran $pembayaran): array
    {
        $pembayaran->load(['siswa', 'jenisPembayaran']);
        $isSpp = strtolower($pembayaran->jenisPembayaran?->nama ?? '') === 'spp';

        if ($pembayaran->tagihan_id) {
            $pembayaran = Pembayaran::with(['siswa', 'jenisPembayaran'])
                ->where('tagihan_id', $pembayaran->tagihan_id)
                ->latest('tanggal_bayar')
                ->latest('id')
                ->first();
        }

        $historiCicilan = Pembayaran::with('jenisPembayaran')
            ->where('siswa_id', $pembayaran->siswa_id)
            ->where('jenis_pembayaran_id', $pembayaran->jenis_pembayaran_id)
            ->when($pembayaran->bulan, fn($q) => $q->where('bulan', $pembayaran->bulan))
            ->when($pembayaran->tahun, fn($q) => $q->where('tahun', $pembayaran->tahun))
            ->orderBy('tanggal_bayar')
            ->get();

        $totalTerbayar = $historiCicilan->sum('nominal');

        $tagihan = $pembayaran->tagihan_id
            ? Tagihan::find($pembayaran->tagihan_id)
            : Tagihan::where('siswa_id', $pembayaran->siswa_id)
                ->where('jenis_pembayaran_id', $pembayaran->jenis_pembayaran_id)
                ->when($pembayaran->bulan, fn($q) => $q->where('bulan', $pembayaran->bulan))
                ->when($pembayaran->tahun, fn($q) => $q->where('tahun', $pembayaran->tahun))
                ->first();

        $nominalAsli = $tagihan ? $tagihan->nominal_tagihan : $totalTerbayar;
        if ($tagihan && $tagihan->status !== 'lunas') {
            $nominalAsli = $tagihan->nominal_tagihan + $totalTerbayar;
        } elseif ($tagihan && $tagihan->status === 'lunas') {
            $nominalAsli = $totalTerbayar;
        }

        $sisaTagihan = max(0, $nominalAsli - $totalTerbayar);

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        // Generate nomor kuitansi: idPembayaran.idTagihan.ddmmyyyy
        $tglBayar = \Carbon\Carbon::parse($pembayaran->tanggal_bayar);
        $nomorKuitansi = $pembayaran->id
            . '.' . ($pembayaran->tagihan_id ?? '0')
            . '.' . $tglBayar->format('dmY');

        // URL publik untuk barcode
        $urlKuitansi = route('kuitansi.pdf', $pembayaran->id);
        $terbilang = ucfirst($this->terbilang((int) $pembayaran->nominal)) . ' Rupiah';

        return [
            'pembayaran'     => $pembayaran,
            'ttdBendahara'   => $this->loadTtd('bendahara.jpeg'),
            'ttdKepsek'      => $this->loadTtd('kepalasekolah.jpeg'),
            'isSpp'          => $isSpp,
            'historiCicilan' => $historiCicilan,
            'totalTerbayar'  => $totalTerbayar,
            'nominalAsli'    => $nominalAsli,
            'sisaTagihan'    => $sisaTagihan,
            'bulanLabels'    => $bulanLabels,
            'isLunas'        => $pembayaran->status === 'lunas',
            'isCicilan'      => $pembayaran->status === 'cicilan',
            'cetakTanggal'   => now()->format('d M Y H:i'),
            'nomorKuitansi'  => $nomorKuitansi,
            'urlKuitansi'    => $urlKuitansi,
            'terbilang'      => $terbilang
        ];
    }

    private function terbilang(int $angka): string
    {
        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima',
                'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
                'sebelas'];

        if ($angka < 12) return $satuan[$angka];
        if ($angka < 20) return $this->terbilang($angka - 10) . ' belas';
        if ($angka < 100) return $this->terbilang((int)($angka / 10)) . ' puluh' . ($angka % 10 ? ' ' . $this->terbilang($angka % 10) : '');
        if ($angka < 200) return 'seratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        if ($angka < 1000) return $this->terbilang((int)($angka / 100)) . ' ratus' . ($angka % 100 ? ' ' . $this->terbilang($angka % 100) : '');
        if ($angka < 2000) return 'seribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        if ($angka < 1000000) return $this->terbilang((int)($angka / 1000)) . ' ribu' . ($angka % 1000 ? ' ' . $this->terbilang($angka % 1000) : '');
        if ($angka < 1000000000) return $this->terbilang((int)($angka / 1000000)) . ' juta' . ($angka % 1000000 ? ' ' . $this->terbilang($angka % 1000000) : '');
        return $this->terbilang((int)($angka / 1000000000)) . ' miliar' . ($angka % 1000000000 ? ' ' . $this->terbilang($angka % 1000000000) : '');
    }
}
