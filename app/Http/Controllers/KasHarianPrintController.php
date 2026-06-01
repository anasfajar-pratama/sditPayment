<?php
// File: app/Http/Controllers/KasHarianPrintController.php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Models\SaldoAwalBulan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KasHarianPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $mode    = $request->input('mode', 'bulanan');
        $bulan   = $request->input('bulan',   now()->format('m'));
        $tahun   = $request->input('tahun',   now()->format('Y'));
        $tanggal = $request->input('tanggal', now()->toDateString());
        $dari    = $request->input('dari',    now()->startOfMonth()->toDateString());
        $sampai  = $request->input('sampai',  now()->toDateString());

        // ─── Build query ──────────────────────────────────────────────────────

        $query = KasHarian::with('akun');

        switch ($mode) {
            case 'harian':
                $query->whereDate('tanggal', $tanggal);
                break;

            case '7hari':
                $start = Carbon::parse($tanggal);
                $query->whereBetween('tanggal', [
                    $start->toDateString(),
                    $start->copy()->addDays(6)->toDateString(),
                ]);
                break;

            case 'range':
                $query->whereBetween('tanggal', [$dari, $sampai]);
                break;

            default: // bulanan
                $query->where('tahun', $tahun)->where('bulan', $bulan);
                break;
        }

        $rows = $query->orderBy('tanggal')->orderBy('id')->get();

        // ─── Saldo awal periode ───────────────────────────────────────────────

        if ($mode === 'bulanan') {
            $saldoAwal = SaldoAwalBulan::getSaldo($bulan, $tahun);
        } else {
            $startDate = match($mode) {
                'harian' => $tanggal,
                '7hari'  => $tanggal,
                'range'  => $dari,
                default  => $tanggal,
            };

            $start     = Carbon::parse($startDate);
            $saldoAwal = SaldoAwalBulan::getSaldo($start->format('m'), $start->format('Y'));

            // Akumulasi transaksi sebelum start date di bulan yang sama
            $before    = KasHarian::where('tahun', $start->format('Y'))
                                   ->where('bulan', $start->format('m'))
                                   ->whereDate('tanggal', '<', $startDate);

            $saldoAwal += (float) (clone $before)->sum('debit');
            $saldoAwal -= (float) (clone $before)->sum('kredit');
        }

        // ─── Running saldo & totals ───────────────────────────────────────────

        $saldo      = $saldoAwal;
        $entries    = [];
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($rows as $row) {
            $saldo      += (float) ($row->debit  ?? 0);
            $saldo      -= (float) ($row->kredit ?? 0);
            $totalDebit  += (float) ($row->debit  ?? 0);
            $totalKredit += (float) ($row->kredit ?? 0);

            $entries[] = [
                'tanggal' => $row->tanggal->format('d M Y'),
                'uraian'  => $row->uraian,
                'akun'    => $row->akun?->nama_akun ?? '—',
                'debit'   => $row->debit,
                'kredit'  => $row->kredit,
                'saldo'   => $saldo,
                'source'  => $row->source,
            ];
        }

        $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

        // ─── Judul periode ────────────────────────────────────────────────────

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $judulPeriode = match($mode) {
            'harian' => 'Tanggal ' . Carbon::parse($tanggal)->translatedFormat('d F Y'),
            '7hari'  => Carbon::parse($tanggal)->format('d M Y')
                        . ' – '
                        . Carbon::parse($tanggal)->addDays(6)->format('d M Y'),
            'range'  => Carbon::parse($dari)->format('d M Y')
                        . ' – '
                        . Carbon::parse($sampai)->format('d M Y'),
            default  => ($bulanLabels[$bulan] ?? $bulan) . ' ' . $tahun,
        };

        return view('pdf.kas-harian', compact(
            'entries',
            'saldoAwal',
            'totalDebit',
            'totalKredit',
            'saldoAkhir',
            'judulPeriode',
            'mode'
        ));
    }
}
