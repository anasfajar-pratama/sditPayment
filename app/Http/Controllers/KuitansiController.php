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

    private function buildData(Pembayaran $pembayaran): array
    {
        $ringkasan = $this->ringkasanPembayaran($pembayaran);
        $isSpp = strtolower($pembayaran->jenisPembayaran?->nama ?? '') === 'spp';

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
            'ttdBendahara'   => $this->loadTtd('ttd_bendahara'),
            'ttdKepsek'      => '',
            'isSpp'          => $isSpp,
            'historiCicilan' => $ringkasan['historiCicilan'],
            'totalTerbayar'  => $ringkasan['totalTerbayar'],
            'nominalAsli'    => $ringkasan['nominalAsli'],
            'sisaTagihan'    => $ringkasan['sisaTagihan'],
            'bulanLabels'    => $bulanLabels,
            'isLunas'        => $pembayaran->status === 'lunas',
            'isCicilan'      => $pembayaran->status === 'cicilan',
            'cicilanKe'      => $ringkasan['cicilanKe'],
            'cetakTanggal'   => now()->format('d M Y H:i'),
            'nomorKuitansi'  => $nomorKuitansi,
            'urlKuitansi'    => $urlKuitansi,
            'terbilang'      => $terbilang
        ];
    }

    /**
     * Ringkasan tagihan & cicilan untuk 1 pembayaran (dipakai PDF & share WA).
     */
    private function ringkasanPembayaran(Pembayaran $pembayaran): array
    {
        $pembayaran->load(['siswa', 'jenisPembayaran']);

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

        $cicilanKe = $historiCicilan->search(fn ($c) => $c->id === $pembayaran->id) + 1;

        return compact('historiCicilan', 'totalTerbayar', 'nominalAsli', 'sisaTagihan', 'cicilanKe');
    }

    /**
     * Share via WA: segarkan token publik (berlaku 10 hari sejak klik ini),
     * lalu buka WhatsApp dengan pesan berisi tautan kuitansi.
     */
    public function shareWa(Pembayaran $pembayaran)
    {
        $link = \DB::table('pdf_links')
            ->where('pdf_id', $pembayaran->id)
            ->where('jenis', 'kuitansi')
            ->first();

        if ($link) {
            \DB::table('pdf_links')->where('id', $link->id)->update([
                'expired_at' => now()->addDays(10),
                'updated_at' => now(),
            ]);
            $token = $link->token;
        } else {
            $token = \Str::random(16);
            \DB::table('pdf_links')->insert([
                'token'        => $token,
                'pdf_id'       => $pembayaran->id,
                'original_url' => "/kuitansi/{$pembayaran->id}/pdf",
                'jenis'        => 'kuitansi',
                'jumlah_view'  => 0,
                'expired_at'   => now()->addDays(10),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $ringkasan = $this->ringkasanPembayaran($pembayaran);
        $historiCicilan = $ringkasan['historiCicilan'];
        $isLunas = $pembayaran->status === 'lunas';
        $shareUrl = url('/k/' . $token);

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $namaSiswa  = $pembayaran->siswa->nama ?? '-';
        $nisSiswa   = $pembayaran->siswa->nis ?? '-';
        $jenis      = $pembayaran->jenisPembayaran?->nama ?? '-';
        $bulanLabel = $pembayaran->bulan ? ($bulanLabels[$pembayaran->bulan] ?? $pembayaran->bulan) : '-';
        $tahun      = $pembayaran->tahun ?? '-';

        $barisCicilan = $historiCicilan->map(function ($c, $i) {
            $tgl = \Carbon\Carbon::parse($c->tanggal_bayar)->translatedFormat('d F Y');
            $nom = 'Rp ' . number_format($c->nominal, 0, ',', '.');
            return "  Cicilan " . ($i + 1) . "  : {$nom} ({$tgl})";
        })->implode("\n");

        $totalTagihan  = 'Rp ' . number_format($ringkasan['nominalAsli'], 0, ',', '.');
        $totalTerbayar = 'Rp ' . number_format($ringkasan['totalTerbayar'], 0, ',', '.');
        $sisa          = 'Rp ' . number_format($ringkasan['sisaTagihan'], 0, ',', '.');

        $pesan = implode("\n", [
            'Assalamualaikum,', '',
            'Berikut kami sampaikan ' . ($isLunas ? 'kuitansi' : 'bukti cicilan') . ' pembayaran:', '',
            "Nama          : {$namaSiswa}",
            "NIS           : {$nisSiswa}",
            "Jenis         : {$jenis}",
            "Periode       : {$bulanLabel} {$tahun}", '',
            'Rincian Pembayaran:',
            $barisCicilan, '',
            "Total Tagihan : {$totalTagihan}",
            "Total Terbayar: {$totalTerbayar}",
            "Sisa Tagihan  : " . ($isLunas ? 'Rp 0' : $sisa),
            "Status        : " . ($isLunas ? '*Lunas*' : '*Cicilan*'), '',
            'Silakan lihat ' . ($isLunas ? 'kuitansi' : 'bukti cicilan') . ' di tautan berikut:',
            $shareUrl, '', 'Terima kasih.',
        ]);

        $noHp = preg_replace('/\D/', '', $pembayaran->siswa->no_hp_orang_tua ?? '');
        if (str_starts_with($noHp, '0'))     $noHp = '62' . substr($noHp, 1);
        elseif (str_starts_with($noHp, '8')) $noHp = '62' . $noHp;

        $teks = rawurlencode($pesan);
        $waUrl = $noHp ? "https://wa.me/{$noHp}?text={$teks}" : "https://wa.me/?text={$teks}";

        return redirect()->away($waUrl);
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
