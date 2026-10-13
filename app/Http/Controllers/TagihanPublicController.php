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

    public static array $namaBulan = [
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
        $query = Tagihan::with(['siswa', 'siswa.kelasSaatIni', 'jenisPembayaran'])
            ->withSum('pembayarans', 'nominal')
            ->withSum('pembayarans', 'potongan')
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

        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        $tagihans = $query->get();

        $bagianFilter = collect([
            $request->filled('bulan')  ? (self::$namaBulan[$request->bulan] ?? $request->bulan) : null,
            $request->filled('tahun')  ? $request->tahun  : null,
            $request->filled('status') ? $request->status : null,
            $request->filled('jenis_pembayaran_id') ? 'Jenis-' . $request->jenis_pembayaran_id : null,
        ])->filter()->implode('_');

        $filename = 'tagihan' . ($bagianFilter ? "_{$bagianFilter}" : '') . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($tagihans) {
            $file = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'NIS',
                'Nama Siswa',
                'Jenjang',
                'Kelas',
                'Jenis Pembayaran',
                'Bulan',
                'Tahun',
                'Nominal',
                'Status',
                // 'Link PDF Tagihan / Kuitansi',
                // 'Link Share Wali Murid',
            ]);

            foreach ($tagihans as $t) {
                if ($t->status === 'lunas' && $t->pembayaran) {
                    $linkPdf = url("/kuitansi/{$t->pembayaran->id}");
                } else {
                    $linkPdf = url("/tagihan/{$t->id}/pdf");
                }

                $token     = self::encryptId($t->id);
                $linkShare = url("/tagihan/share/{$token}");

                $nominalTampil = $t->status === 'lunas'
                    ? (float) ($t->pembayarans_sum_nominal ?? 0) + (float) ($t->pembayarans_sum_potongan ?? 0)
                    : (float) $t->nominal_tagihan;

                fputcsv($file, [
                    $t->siswa->nis    ?? '-',
                    $t->siswa->nama   ?? '-',
                    $t->siswa->kelasSaatIni?->jenis_sekolah ?? '-',
                    $t->siswa->kelasSaatIni?->kelas ?? '-',
                    $t->jenisPembayaran?->nama ?? '-',
                    self::$namaBulan[$t->bulan] ?? $t->bulan,
                    $t->tahun,
                    $nominalTampil,
                    $t->status === 'lunas' ? 'Lunas' : 'Belum Bayar',
                    // $linkPdf,
                    // $linkShare,
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
        $query = Tagihan::with(['siswa', 'siswa.kelasSaatIni', 'jenisPembayaran'])
            ->withSum('pembayarans', 'nominal')
            ->withSum('pembayarans', 'potongan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->orderBy('created_at');

        $bulan  = is_string($request->bulan)  && $request->bulan  !== '' ? $request->bulan  : null;
        $tahun  = is_string($request->tahun)  && $request->tahun  !== '' ? $request->tahun  : null;
        $status = is_string($request->status) && $request->status !== '' ? $request->status : null;
        $jenis  = is_string($request->jenis_pembayaran_id) && $request->jenis_pembayaran_id !== '' ? $request->jenis_pembayaran_id : null;

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($jenis) {
            $query->where('jenis_pembayaran_id', $jenis);
        }

        $tagihans = $query->get();

        $bagianFilter = collect([
            $bulan  ? (self::$namaBulan[$bulan] ?? $bulan) : null,
            $tahun,
            $status,
            $jenis ? 'Jenis-' . $jenis : null,
        ])->filter()->implode('_');

        $filename = 'tagihan' . ($bagianFilter ? "_{$bagianFilter}" : '') . '_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView('pdf.tagihan-report', compact('tagihans', 'bagianFilter'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // ─── Export matrix PDF per kelas per bulan ─────────────────────────────

    public function exportMatrixPdf(Request $request): \Illuminate\Http\Response
    {
        $data = $this->buildTunggakanData(
            (string) $request->jenis_sekolah,
            (string) $request->kelas,
            (string) $request->bulan,
            (string) $request->tahun,
        );

        $pdf = Pdf::loadView('pdf.tagihan-tunggakan', [
            'rows'         => $data['rows'],
            'grandTotal'   => $data['grandTotal'],
            'jenisSekolah' => $data['jenisSekolah'],
            'kelas'        => $data['kelas'],
            'judul'        => $data['judul'],
            'periodeLabel' => $data['periodeLabel'],
        ])->setPaper('a4', 'portrait');

        $filename = 'tunggakan_' . $data['bagianFilter'] . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Susun daftar tunggakan KUMULATIF (dipakai bersama oleh PDF & CSV).
     * Hanya siswa yang masih menunggak s/d bulan target; yang sudah lunas dilewati.
     */
    private function buildTunggakanData(string $jenisSekolah, string $kelas, string $bulan, string $tahun): array
    {
        $isDta      = strtoupper($jenisSekolah) === 'DTA';
        $isDuColumn = $bulan === '';

        $siswaList = \App\Models\Siswa::where('status_aktif', true)
            ->whereHas('kelasSaatIni', fn ($q) => $q->where('jenis_sekolah', $jenisSekolah)->where('kelas', $kelas))
            ->orderBy('nama')
            ->get(['id', 'nis', 'nama']);

        // Periode bulan yang ditagih secara KUMULATIF dari awal tahun ajaran s/d bulan target.
        $targetMonths = $this->buildTargetMonths($bulan, $tahun, $isDta);

        // Seluruh tagihan yang masih belum bayar untuk siswa kelas ini.
        $tagihans = Tagihan::with('jenisPembayaran')
            ->whereIn('siswa_id', $siswaList->pluck('id'))
            ->where('status', 'belum_bayar')
            ->get();

        // Susun tunggakan per siswa (hanya item dengan periode <= bulan target), anti dobel per periode.
        $outstanding = [];
        foreach ($tagihans as $t) {
            foreach ($this->tagihanOutstandingItems($t, $targetMonths, $isDuColumn, $tahun) as $item) {
                $key = ($item['bulan'] ?? 'du') . '_' . $item['tahun'];
                if (! isset($outstanding[$t->siswa_id][$key])) {
                    $outstanding[$t->siswa_id][$key] = $item;
                }
            }
        }

        // Siswa yang sudah lunas (tidak menunggak) TIDAK ikut.
        $rows       = [];
        $grandTotal = 0;
        foreach ($siswaList as $siswa) {
            $items = array_values($outstanding[$siswa->id] ?? []);
            if (empty($items)) {
                continue;
            }

            $total       = array_sum(array_column($items, 'nominal'));
            $grandTotal += $total;

            $rows[] = [
                'nis'   => $siswa->nis,
                'nama'  => $siswa->nama,
                'items' => $items,
                'total' => $total,
            ];
        }

        $periodeLabel = $isDuColumn
            ? ($isDta ? 'Awal Tahun' : 'Daftar Ulang') . ' ' . $tahun
            : ((self::$namaBulan[$bulan] ?? $bulan) . ' ' . $tahun);

        $bagianFilter = $isDuColumn
            ? "{$jenisSekolah}_{$kelas}_du_{$tahun}"
            : "{$jenisSekolah}_{$kelas}_{$bulan}_{$tahun}";

        return [
            'rows'         => $rows,
            'grandTotal'   => $grandTotal,
            'jenisSekolah' => $jenisSekolah,
            'kelas'        => $kelas,
            'periodeLabel' => $periodeLabel,
            'bagianFilter' => $bagianFilter,
            'judul'        => 'Daftar Tunggakan s/d ' . $periodeLabel,
        ];
    }

    /**
     * Daftar periode (bulan) kumulatif dari awal tahun ajaran s/d bulan target.
     * Kosong bila kolom DU/BP (bulan '').
     */
    private function buildTargetMonths(string $bulan, string $tahun, bool $isDta): array
    {
        if ($bulan === '') {
            return [];
        }

        $bulanInt   = (int) $bulan;
        $tahunMulai = $bulanInt >= 7 ? (int) $tahun : (int) $tahun - 1;

        $allMonths = $isDta
            ? ['07', '08', '09', '10', '11', '12', '01', '02', '03', '04', '05', '06']
            : ['08', '09', '10', '11', '12', '01', '02', '03', '04', '05', '06'];

        $months = [];
        foreach ($allMonths as $m) {
            $mt       = (int) $m <= 6 ? $tahunMulai + 1 : $tahunMulai;
            $months[] = ['bulan' => $m, 'tahun' => (string) $mt];

            if ($m === $bulan) {
                break;
            }
        }

        return $months;
    }

    /**
     * Pecah satu tagihan menjadi item tunggakan per bulan.
     * Untuk kolom DU/BP mengembalikan satu item non-SPP.
     */
    private function tagihanOutstandingItems(Tagihan $tagihan, array $targetMonths, bool $isDuColumn, string $tahun): array
    {
        $details = $tagihan->detail ?? [];
        $items   = [];

        if (count($details) > 0) {
            // Sisa tagihan (nominal_tagihan sudah dikurangi cicilan) dialokasikan
            // proporsional ke tiap item detail, agar sisa tidak memakai nominal asli.
            $origTotal = 0.0;
            foreach ($details as $it) {
                $origTotal += (float) ($it['nominal'] ?? 0);
            }
            $remaining = (float) $tagihan->nominal_tagihan;
            $factor    = $origTotal > 0 ? ($remaining / $origTotal) : 1;

            foreach ($details as $item) {
                $jenis        = strtoupper($item['jenis'] ?? '');
                $itemRemaining = (float) ($item['nominal'] ?? 0) * $factor;

                if ($jenis === 'SPP') {
                    if ($isDuColumn) {
                        continue;
                    }

                    $b = $item['bulan'] ?? null;
                    $t = (string) ($item['tahun'] ?? '');
                    if ($b && $this->periodInList($targetMonths, $b, $t)) {
                        $items[] = ['bulan' => $b, 'tahun' => $t, 'nominal' => $itemRemaining];
                    }
                } elseif ($isDuColumn && (string) $tagihan->tahun === $tahun) {
                    $items[] = ['bulan' => null, 'tahun' => (string) $tagihan->tahun, 'nominal' => $itemRemaining];
                }
            }

            return $items;
        }

        // Tagihan sederhana (tanpa detail multi-item).
        $isSpp = strtolower($tagihan->jenisPembayaran?->nama ?? '') === 'spp';

        if ($isSpp) {
            if (! $isDuColumn && $tagihan->bulan
                && $this->periodInList($targetMonths, $tagihan->bulan, (string) $tagihan->tahun)) {
                $items[] = [
                    'bulan'   => $tagihan->bulan,
                    'tahun'   => (string) $tagihan->tahun,
                    'nominal' => (float) $tagihan->nominal_tagihan,
                ];
            }
        } elseif ($isDuColumn && (string) $tagihan->tahun === $tahun) {
            $items[] = [
                'bulan'   => null,
                'tahun'   => (string) $tagihan->tahun,
                'nominal' => (float) $tagihan->nominal_tagihan,
            ];
        }

        return $items;
    }

    private function periodInList(array $months, string $bulan, string $tahun): bool
    {
        foreach ($months as $m) {
            if ($m['bulan'] === $bulan && $m['tahun'] === $tahun) {
                return true;
            }
        }

        return false;
    }

    // ─── Export matrix CSV per kelas per bulan ─────────────────────────────

    public function exportMatrixCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = $this->buildTunggakanData(
            (string) $request->jenis_sekolah,
            (string) $request->kelas,
            (string) $request->bulan,
            (string) $request->tahun,
        );

        $filename = 'tunggakan_' . $data['bagianFilter'] . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No', 'NIS', 'Nama Siswa', 'Kelas',
                'Bulan Menunggak', 'Total Tunggakan (Rp)',
            ]);

            foreach ($data['rows'] as $i => $r) {
                $bulanList = collect($r['items'])->map(function ($it) {
                    $label = $it['bulan']
                        ? ((self::$namaBulan[$it['bulan']] ?? $it['bulan']) . ' ' . $it['tahun'])
                        : 'Daftar Ulang';

                    return $label . ': ' . number_format($it['nominal'], 0, ',', '.');
                })->implode('; ');

                fputcsv($file, [
                    $i + 1,
                    $r['nis'] ?? '-',
                    $r['nama'] ?? '-',
                    $data['kelas'],
                    $bulanList,
                    $r['total'],
                ]);
            }

            fputcsv($file, ['', '', '', '', 'TOTAL', $data['grandTotal']]);

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─── Helper enkripsi ID (dipanggil juga dari TagihanResource) ─────────────

    public static function encryptId(int $id): string
    {
        // base64url: ganti +/ → -_ dan strip padding =
        return rtrim(strtr(encrypt($id), '+/', '-_'), '=');
    }
}
