<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KasRingkasanController extends Controller
{
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