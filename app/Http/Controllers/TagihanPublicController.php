<?php

// ════════════════════════════════════════════════════════════
// File: app/Http/Controllers/TagihanPublicController.php
// ════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use App\Models\Tagihan;
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
     * Redirect ke halaman PDF tagihan yang sudah ada.
     *
     * PENTING: Pastikan route 'tagihan.pdf' (biasanya /tagihan/{id}/pdf)
     * diletakkan di LUAR middleware auth agar dapat diakses tanpa login.
     * Lihat routes_snippet.php untuk contoh struktur route yang benar.
     */
    public function show(string $token): \Illuminate\Http\RedirectResponse
    {
        try {
            $id = $this->decryptToken($token);
        } catch (\Throwable $e) {
            abort(404, 'Link tidak valid atau sudah tidak berlaku.');
        }

        // Pastikan ID valid sebelum redirect
        Tagihan::findOrFail($id);

        // Redirect ke route PDF yang sudah ada tanpa perlu login
        return redirect(url("/tagihan/{$id}/pdf"));
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
                'Link PDF Tagihan / Kuitansi',   // URL PDF yang butuh login
                'Link Share Wali Murid',          // URL publik terenkripsi
            ]);

            foreach ($tagihans as $t) {
                // Link PDF internal (butuh login)
                if ($t->status === 'lunas' && $t->pembayaran) {
                    $linkPdf = url("/kuitansi/{$t->pembayaran->id}");
                } else {
                    $linkPdf = url("/tagihan/{$t->id}/pdf");
                }

                // Link share publik (enkripsi ID, tanpa login)
                $token    = self::encryptId($t->id);
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

    // ─── Helper enkripsi ID (sama dengan di TagihanResource) ──────────────────

    public static function encryptId(int $id): string
    {
        // base64url: ganti +/ → -_ dan strip padding =
        return rtrim(strtr(encrypt($id), '+/', '-_'), '=');
    }
}
