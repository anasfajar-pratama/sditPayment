<?php

// File: app/Http/Controllers/KasHarianPrintController.php

namespace App\Http\Controllers;

use App\Exports\KasHarianExport;
use App\Models\KasHarian;
use App\Models\SaldoAwalBulan;
use App\Traits\ExportsCsv;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class KasHarianPrintController extends Controller
{
    use ExportsCsv;

    public function __invoke(Request $request)
    {
        $report = $this->buildReport($request);

        $today = now()->toDateString();
        $todayDebit = (float) KasHarian::whereDate('tanggal', $today)->sum('debit');
        $todayKredit = (float) KasHarian::whereDate('tanggal', $today)->sum('kredit');
        $kasHariIni = $todayDebit - $todayKredit;

        return view('pdf.kas-harian', [
            'entries' => $report['entries'],
            'saldoAwal' => $report['saldoAwal'],
            'totalDebit' => $report['totalDebit'],
            'totalKredit' => $report['totalKredit'],
            'saldoAkhir' => $report['saldoAkhir'],
            'judulPeriode' => $report['judulPeriode'],
            'mode' => $report['mode'],
            'todayDebit' => $todayDebit,
            'todayKredit' => $todayKredit,
            'kasHariIni' => $kasHariIni,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $report = $this->buildReport($request);

        $out = [];
        $no = 1;

        foreach ($report['entries'] as $entry) {
            $out[] = [
                $no++,
                $entry['tanggal'],
                $entry['uraian'],
                $entry['akun'] ?? '',
                $entry['rekening_tujuan'] ?: '',
                $entry['sub_kategori'] ?: '',
                (float) ($entry['debit'] ?? 0),
                (float) ($entry['kredit'] ?? 0),
                (float) $entry['saldo'],
            ];
        }

        $headers = ['No', 'Tanggal', 'Uraian', 'Akun', 'Rekening Tujuan', 'Sub Kategori', 'Debit', 'Kredit', 'Saldo'];

        return $this->streamCsv(
            'kas-gabungan-'.$report['dari'].'-to-'.$report['sampai'].'.csv',
            $headers,
            $out,
        );
    }

    public function exportExcel(Request $request)
    {
        $report = $this->buildReport($request);

        return Excel::download(
            new KasHarianExport(
                entries: $report['entries'],
                judulPeriode: $report['judulPeriode'],
                totalDebit: $report['totalDebit'],
                totalKredit: $report['totalKredit'],
                saldoAkhir: $report['saldoAkhir'],
            ),
            'kas-gabungan-'.Str::slug($report['judulPeriode']).'.xlsx',
        );
    }

    private function buildReport(Request $request): array
    {
        $mode = $request->input('mode', 'bulanan');
        $bulan = $request->input('bulan', now()->format('m'));
        $tahun = $request->input('tahun', now()->format('Y'));
        $tanggal = $request->input('tanggal', now()->toDateString());
        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $rows = $this->periodQuery($mode, $bulan, $tahun, $tanggal, $dari, $sampai)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $saldoAwal = $this->saldoAwal($mode, $bulan, $tahun, $tanggal, $dari);

        $saldo = $saldoAwal;
        $entries = [];
        $totalDebit = 0.0;
        $totalKredit = 0.0;

        foreach ($rows as $row) {
            $debit = (float) ($row->debit ?? 0);
            $kredit = (float) ($row->kredit ?? 0);

            $saldo += $debit;
            $saldo -= $kredit;
            $totalDebit += $debit;
            $totalKredit += $kredit;

            $entries[] = [
                'tanggal' => $row->tanggal->format('d M Y'),
                'uraian' => $row->uraian,
                'akun' => $row->akun?->nama_akun ?? '—',
                'rekening_tujuan' => $row->rekening_tujuan,
                'sub_kategori' => $row->sub_kategori,
                'debit' => $row->debit,
                'kredit' => $row->kredit,
                'saldo' => $saldo,
                'source' => $row->source,
            ];
        }

        return [
            'mode' => $mode,
            'entries' => $entries,
            'saldoAwal' => $saldoAwal,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'saldoAkhir' => $saldoAwal + $totalDebit - $totalKredit,
            'judulPeriode' => $this->periodeLabel($mode, $bulan, $tahun, $tanggal, $dari, $sampai),
            'dari' => $dari,
            'sampai' => $sampai,
        ];
    }

    private function saldoAwal(string $mode, string $bulan, string $tahun, string $tanggal, string $dari): float
    {
        if ($mode === 'bulanan') {
            return SaldoAwalBulan::getSaldo($bulan, $tahun);
        }

        $startDate = match ($mode) {
            'harian' => $tanggal,
            '7hari' => $tanggal,
            'range' => $dari,
            default => $tanggal,
        };

        $start = Carbon::parse($startDate);
        $saldo = SaldoAwalBulan::getSaldo($start->format('m'), $start->format('Y'));

        $before = KasHarian::whereYear('tanggal', $start->format('Y'))
            ->whereMonth('tanggal', $start->format('m'))
            ->whereDate('tanggal', '<', $startDate);

        $saldo += (float) (clone $before)->sum('debit');
        $saldo -= (float) (clone $before)->sum('kredit');

        return $saldo;
    }

    private function periodeLabel(string $mode, string $bulan, string $tahun, string $tanggal, string $dari, string $sampai): string
    {
        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        return match ($mode) {
            'harian' => 'Tanggal '.Carbon::parse($tanggal)->translatedFormat('d F Y'),
            '7hari' => Carbon::parse($tanggal)->format('d M Y')
                        .' – '
                        .Carbon::parse($tanggal)->addDays(6)->format('d M Y'),
            'range' => Carbon::parse($dari)->format('d M Y')
                        .' – '
                        .Carbon::parse($sampai)->format('d M Y'),
            default => ($bulanLabels[$bulan] ?? $bulan).' '.$tahun,
        };
    }

    private function periodQuery(string $mode, string $bulan, string $tahun, string $tanggal, string $dari, string $sampai): Builder
    {
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
                $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', (int) $bulan);
                break;
        }

        return $query;
    }
}
