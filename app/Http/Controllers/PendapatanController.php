<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\KasHarian;
use App\Traits\ExportsCsv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PendapatanController extends Controller
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

        $akunList = $this->akunList($rows);

        $grouped = [];
        foreach ($akunList as $akun) {
            $grouped[$akun['id']] = $this->buildRows(
                $rows->filter(fn ($r) => (float) ($r->debit ?? 0) > 0 && (int) $r->akun_id === (int) $akun['id'])
            );
        }

        $ringkasan = [];
        $grandTotal = 0.0;
        foreach ($akunList as $akun) {
            $total = (float) collect($grouped[$akun['id']])->sum('jumlah');
            $ringkasan[$akun['id']] = $total;
            $grandTotal += $total;
        }

        $data = compact('start', 'end', 'akunList', 'grouped', 'ringkasan', 'grandTotal');

        $pdf = Pdf::loadView('pdf.pendapatan', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('pendapatan-' . $start . '-to-' . $end . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $start = $request->query('start', now()->toDateString());
        $end   = $request->query('end', now()->toDateString());
        $tab   = $request->query('tab', '');

        $query = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->where('debit', '>', 0);

        if ($tab !== '' && ctype_digit((string) $tab)) {
            $query->where('akun_id', (int) $tab);
        }

        $rows = $query->orderBy('tanggal')->orderBy('id')->get();

        $out = [];
        $no  = 1;
        foreach ($rows as $r) {
            $out[] = [
                $no++,
                $r->tanggal->format('d M Y'),
                $r->uraian,
                $r->akun?->nama_akun ?? '—',
                $r->rekening_tujuan ?? '',
                $r->nama_rekening_pengirim ?? '',
                (float) $r->debit,
            ];
        }

        $headers = ['No', 'Tanggal', 'Keterangan', 'Akun', 'Rekening Tujuan', 'Pengirim', 'Jumlah'];

        return $this->streamCsv('pendapatan-' . $start . '-to-' . $end . '.csv', $headers, $out);
    }

    private function akunList(Collection $rows): array
    {
        $akuns = Akun::where('kelompok', 'Pendapatan')
            ->orderBy('kode_akun')
            ->get()
            ->keyBy('id');

        $used = $rows
            ->filter(fn ($r) => (float) ($r->debit ?? 0) > 0)
            ->map(fn ($r) => $r->akun)
            ->filter(fn ($a) => $a !== null && $a->kelompok === 'Pendapatan');

        foreach ($used as $a) {
            if (! $akuns->has($a->id)) {
                $akuns->put($a->id, $a);
            }
        }

        return $akuns
            ->sortBy('kode_akun')
            ->values()
            ->map(fn ($a) => [
                'id'   => $a->id,
                'kode' => $a->kode_akun,
                'nama' => $a->nama_akun,
            ])
            ->all();
    }

    private function buildRows(Collection $rows): array
    {
        $no = 1;
        return $rows->map(function ($r) use (&$no) {
            return [
                'no'       => $no++,
                'tanggal'  => $r->tanggal->format('d M Y'),
                'uraian'   => $r->uraian,
                'rekening' => $r->rekening_tujuan,
                'pengirim' => $r->nama_rekening_pengirim,
                'jumlah'   => (float) $r->debit,
            ];
        })->values()->all();
    }
}