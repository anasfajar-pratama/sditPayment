<?php

// ════════════════════════════════════════════════════════════
// File: app/Http/Controllers/TagihanPublicController.php
// ════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TagihanPublicController extends Controller
{
    // ─── Bulan helper ─────────────────────────────────────────────────────────

    private static array $namaBulan = [
        '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
    ];

    // ─── Decode token yang aman ───────────────────────────────────────────────

    private function decryptToken(string $token): int
    {
        // Kembalikan base64url → base64 standar (ganti -_ ke +/, tambah padding =)
        $restored = strtr($token, '-_', '+/');
        $mod4 = strlen($restored) % 4;
        if ($mod4) {
            $restored .= str_repeat('=', 4 - $mod4);
        }
        return (int) decrypt($restored);
    }

    // ─── Halaman publik tagihan (tanpa login) ─────────────────────────────────

    /**
     * Tampilkan detail tagihan langsung di halaman web.
     * Dapat diakses tanpa login — aman karena ID dienkripsi di URL.
     */
    public function show(string $token): \Illuminate\View\View
    {
        try {
            $id = $this->decryptToken($token);
        } catch (\Throwable $e) {
            abort(404, 'Link tidak valid atau sudah tidak berlaku.');
        }

        $tagihan = Tagihan::with(['siswa', 'jenisPembayaran', 'pembayaran'])
            ->findOrFail($id);

        $namaBulan = self::$namaBulan[$tagihan->bulan] ?? $tagihan->bulan;

        return view('tagihan.public', compact('tagihan', 'namaBulan'));
    }

    // ─── Export CSV (hanya untuk user login) ──────────────────────────────────

    /**
     * Download tagihan sebagai CSV dengan kolom link PDF & link share.
     */
    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Tagihan::with(['siswa', 'jenisPembayaran'])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('created_at');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tagihans = $query->get();

        $bagianFilter = collect([
            $request->filled('bulan')  ? (self::$namaBulan[$request->bulan] ?? $request->bulan) : null,
            $request->filled('tahun')  ? $request->tahun  : null,
            $request->filled('status') ? $request->status : null,
        ])->filter()->implode('_');

        $filename = 'tagihan' . ($bagianFilter ? "_{$bagianFilter}" : '') . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($tagihans) {
            $file = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'NIS',
                'Nama Siswa',
                'Jenis Pembayaran',
                'Bulan',
                'Tahun',
                'Nominal',
                'Status',
                'Link PDF Tagihan / Kuitansi',
                'Link Share Wali Murid',
            ]);

            foreach ($tagihans as $t) {
                if ($t->status === 'lunas' && $t->pembayaran) {
                    $linkPdf = url("/kuitansi/{$t->pembayaran->id}");
                } else {
                    $linkPdf = url("/tagihan/{$t->id}/pdf");
                }

                $token     = self::encryptId($t->id);
                $linkShare = url("/tagihan/share/{$token}");

                fputcsv($file, [
                    $t->siswa->nis    ?? '-',
                    $t->siswa->nama   ?? '-',
                    $t->jenisPembayaran->nama ?? '-',
                    self::$namaBulan[$t->bulan] ?? $t->bulan,
                    $t->tahun,
                    $t->nominal_tagihan,
                    $t->status === 'lunas' ? 'Lunas' : 'Belum Bayar',
                    $linkPdf,
                    $linkShare,
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─── Export PDF ───────────────────────────────────────────────────────────

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        $query = Tagihan::with(['siswa', 'jenisPembayaran'])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('created_at');

        $bulan  = is_string($request->bulan)  && $request->bulan  !== '' ? $request->bulan  : null;
        $tahun  = is_string($request->tahun)  && $request->tahun  !== '' ? $request->tahun  : null;
        $status = is_string($request->status) && $request->status !== '' ? $request->status : null;

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tagihans = $query->get();

        $bagianFilter = collect([
            $bulan  ? (self::$namaBulan[$bulan] ?? $bulan) : null,
            $tahun,
            $status,
        ])->filter()->implode('_');

        $filename = 'tagihan' . ($bagianFilter ? "_{$bagianFilter}" : '') . '_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('pdf.tagihan-report', compact('tagihans', 'bagianFilter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // ─── Helper enkripsi ID (dipanggil juga dari TagihanResource) ─────────────

    public static function encryptId(int $id): string
    {
        // base64url: ganti +/ → -_ dan strip padding =
        return rtrim(strtr(encrypt($id), '+/', '-_'), '=');
    }
}
