<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Traits\ExportsCsv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KasRingkasanController extends Controller
{
    use ExportsCsv;
    public function cetakPdf(Request $request)
    {
        $start = $request->query('start', now()->toDateString());
        $end   = $request->query('end', now()->toDateString());

        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $transfer  = $this->buildList($rows->filter(fn ($r) =>
            (float) ($r->debit ?? 0) > 0
            && $r->rekening_tujuan !== null
            && $r->rekening_tujuan !== 'Cash'));
        $cash      = $this->buildList($rows->filter(fn ($r) =>
            (float) ($r->debit ?? 0) > 0
            && ($r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash')));
        $kredit    = $this->buildList($rows->filter(fn ($r) => (float) ($r->kredit ?? 0) > 0), true);

        $saldo     = 0.0;
        $gabungan  = [];
        foreach ($rows as $r) {
            $debitVal  = (float) ($r->debit  ?? 0);
            $kreditVal = (float) ($r->kredit ?? 0);
            $isCash    = $r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash';

            if ($debitVal > 0 && $isCash) {
                $saldo += $debitVal;
                $gabungan[] = $this->mapRow($r, 'Masuk', $debitVal, $saldo, count($gabungan) + 1);
            } elseif ($kreditVal > 0) {
                $saldo -= $kreditVal;
                $gabungan[] = $this->mapRow($r, 'Keluar', $kreditVal, $saldo, count($gabungan) + 1);
            }
        }

        $totalTransfer = (float) $transfer->sum('jumlah');
        $totalCash     = (float) $cash->sum('jumlah');
        $totalKredit   = (float) $kredit->sum('jumlah');
        $kasHariIni    = $totalCash - $totalKredit;

        $data = compact(
            'start', 'end',
            'transfer', 'cash', 'kredit', 'gabungan',
            'totalTransfer', 'totalCash', 'totalKredit', 'kasHariIni',
        );

        $pdf = Pdf::loadView('pdf.kas-ringkasan', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('kas-ringkasan-' . $start . '-to-' . $end . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $start = $request->query('start', now()->toDateString());
        $end   = $request->query('end', now()->toDateString());
        $tab   = $request->query('tab', 'gabungan');

        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $rowsSekolah = $rows->filter(fn ($r) => ! $r->is_yayasan)->values();

        $kashariini = [];
        $gabungan   = [];
        $semua      = [];
        $saldoK     = 0.0;
        $saldoG     = 0.0;
        $saldoS     = 0.0;

        foreach ($rowsSekolah as $r) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);
            $isCash = $r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash';

            if ($debit > 0) {
                $saldoG += $debit;
                if ($isCash) {
                    $saldoK += $debit;
                    $kashariini[] = $this->mapRow($r, 'Masuk', $debit, $saldoK, count($kashariini) + 1);
                }
                $gabungan[] = $this->mapRow($r, 'Masuk', $debit, $saldoG, count($gabungan) + 1);
            } elseif ($kredit > 0) {
                $saldoK -= $kredit;
                $saldoG -= $kredit;
                $kashariini[] = $this->mapRow($r, 'Keluar', $kredit, $saldoK, count($kashariini) + 1);
                $gabungan[]   = $this->mapRow($r, 'Keluar', $kredit, $saldoG, count($gabungan) + 1);
            }
        }

        foreach ($rows as $r) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);

            if ($debit > 0) {
                $saldoS += $debit;
                $semua[] = $this->mapRow($r, 'Masuk', $debit, $saldoS, count($semua) + 1);
            } elseif ($kredit > 0) {
                $saldoS -= $kredit;
                $semua[] = $this->mapRow($r, 'Keluar', $kredit, $saldoS, count($semua) + 1);
            }
        }

        $transfer = $this->buildList($rowsSekolah->filter(fn ($r) =>
            (float) ($r->debit ?? 0) > 0
            && $r->rekening_tujuan !== null
            && $r->rekening_tujuan !== 'Cash'));
        $cash     = $this->buildList($rowsSekolah->filter(fn ($r) =>
            (float) ($r->debit ?? 0) > 0
            && ($r->rekening_tujuan === null || $r->rekening_tujuan === 'Cash')));
        $kredit   = $this->buildList($rowsSekolah->filter(fn ($r) => (float) ($r->kredit ?? 0) > 0), true);

        $pendapatanYayasan   = $this->buildList($rows->filter(fn ($r) => $r->is_yayasan && (float) ($r->debit ?? 0) > 0));
        $pengeluaranYayasan  = $this->buildList($rows->filter(fn ($r) => $r->is_yayasan && (float) ($r->kredit ?? 0) > 0), true);

        $yayasan    = [];
        $nonYayasan = [];
        $saldoY     = 0.0;
        $saldoN     = 0.0;

        foreach ($rows as $r) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);

            if ($r->is_yayasan) {
                if ($debit > 0) {
                    $saldoY += $debit;
                    $yayasan[] = $this->mapRow($r, 'Masuk', $debit, $saldoY, count($yayasan) + 1);
                } elseif ($kredit > 0) {
                    $saldoY -= $kredit;
                    $yayasan[] = $this->mapRow($r, 'Keluar', $kredit, $saldoY, count($yayasan) + 1);
                }
            } else {
                if ($debit > 0) {
                    $saldoN += $debit;
                    $nonYayasan[] = $this->mapRow($r, 'Masuk', $debit, $saldoN, count($nonYayasan) + 1);
                } elseif ($kredit > 0) {
                    $saldoN -= $kredit;
                    $nonYayasan[] = $this->mapRow($r, 'Keluar', $kredit, $saldoN, count($nonYayasan) + 1);
                }
            }
        }

        [$headers, $keys, $list] = match ($tab) {
            'transfer'             => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Pengirim', 'Jumlah'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'rekening', 'pengirim', 'jumlah'], $transfer],
            'cash'                 => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Jumlah'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'jumlah'], $cash],
            'pengeluaran'          => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Jumlah'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'jumlah'], $kredit],
            'kashariini'           => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Tipe', 'Jumlah', 'Saldo'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'tipe', 'jumlah', 'saldo'], $kashariini],
            'pendapatan_yayasan'   => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Jumlah'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'jumlah'], $pendapatanYayasan],
            'pengeluaran_yayasan'  => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Jumlah'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'jumlah'], $pengeluaranYayasan],
            'yayasan'              => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Tipe', 'Jumlah', 'Saldo'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'rekening', 'tipe', 'jumlah', 'saldo'], $yayasan],
            'non_yayasan'          => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Tipe', 'Jumlah', 'Saldo'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'rekening', 'tipe', 'jumlah', 'saldo'], $nonYayasan],
            'semua'                => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Tipe', 'Jumlah', 'Saldo'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'rekening', 'tipe', 'jumlah', 'saldo'], $semua],
            default                => [['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Tipe', 'Jumlah', 'Saldo'],
                                       ['no', 'tanggal', 'uraian', 'akun', 'rekening', 'tipe', 'jumlah', 'saldo'], $gabungan],
        };

        $out = collect($list)
            ->map(fn ($r) => array_map(fn ($k) => $r[$k] ?? '', $keys))
            ->values()
            ->all();

        return $this->streamCsv('rekap-kas-harian-' . $start . '-to-' . $end . '.csv', $headers, $out);
    }

    private function buildList(Collection $rows, bool $kreditMode = false): Collection
    {
        $no = 1;
        return $rows->map(function ($r) use (&$no, $kreditMode) {
            $debit  = (float) ($r->debit  ?? 0);
            $kredit = (float) ($r->kredit ?? 0);
            $jumlah = $kreditMode ? $kredit : $debit;
            return $this->mapRow($r, $kreditMode ? 'Keluar' : 'Masuk', $jumlah, null, $no++);
        })->values();
    }

    private function mapRow($r, string $tipe, float $jumlah, ?float $saldo, int $no): array
    {
        return [
            'no'           => $no,
            'tanggal'      => $r->tanggal->format('d M Y'),
            'uraian'       => $r->uraian,
            'akun'         => $r->akun?->nama_akun ?? '—',
            'sub_kategori' => $r->sub_kategori,
            'rekening'     => $r->rekening_tujuan,
            'pengirim'     => $r->nama_rekening_pengirim,
            'tipe'         => $tipe,
            'jumlah'       => $jumlah,
            'saldo'        => $saldo,
        ];
    }
}