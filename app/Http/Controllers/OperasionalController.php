<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Traits\ExportsCsv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    use ExportsCsv;
    public function cetakPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));

        $kategori = $this->dynamicKategori('Operasional', [
            'TOKEN & PULSA',
            'PERLENGKAPAN',
            'MAINTENANCE & FC',
            'TRANSPORT & DINAS',
            'PEMBANGUNAN',
            'BUKU PAKET',
            'BANGKU & SERAGAM',
        ], $start, $end);

        $data = $this->buildData($start, $end, $kategori);
        $data['kategori'] = $kategori;

        $pdf = Pdf::loadView('pdf.operasional', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('operasional-' . $start . '-to-' . $end . '.pdf');
    }

    public function sosialPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));

        $kategori = $this->dynamicKategori('Sosial', [
            'SOSIAL & OBAT',
            'JAMUAN',
            'KELUARGA',
            'KASBON',
        ], $start, $end);

        $data = $this->buildData($start, $end, $kategori);
        $data['kategori'] = $kategori;

        $pdf = Pdf::loadView('pdf.sosial', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('sosial-' . $start . '-to-' . $end . '.pdf');
    }

    public function upahPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));

        $penerimas = KasHarian::whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereNotNull('sub_kategori')
            ->distinct()->orderBy('sub_kategori')->pluck('sub_kategori')->toArray();

        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereNotNull('sub_kategori')
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($penerimas as $p) {
            $grouped[$p] = [];
            $kumulatif = 0;
            $no = 1;
            foreach ($rows->where('sub_kategori', $p) as $row) {
                $kumulatif += (float) ($row->kredit ?? 0);
                $grouped[$p][] = [
                    'no'      => $no++,
                    'tanggal' => $row->tanggal->format('d-m-Y'),
                    'uraian'  => $row->uraian,
                    'jumlah'  => (float) ($row->kredit ?? 0),
                    'total'   => $kumulatif,
                ];
            }
        }

        $ringkasan = [];
        $grandTotal = 0;
        foreach ($penerimas as $p) {
            $total = (float) $rows->where('sub_kategori', $p)->sum('kredit');
            $ringkasan[$p] = $total;
            $grandTotal += $total;
        }

        $data = compact('start', 'end', 'penerimas', 'grouped', 'ringkasan', 'grandTotal');

        $pdf = Pdf::loadView('pdf.upah', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('upah-' . $start . '-to-' . $end . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        return $this->kreditExportCsv($request, 'Operasional', [
            'TOKEN & PULSA',
            'PERLENGKAPAN',
            'MAINTENANCE & FC',
            'TRANSPORT & DINAS',
            'PEMBANGUNAN',
            'BUKU PAKET',
            'BANGKU & SERAGAM',
        ], 'operasional');
    }

    public function sosialCsv(Request $request)
    {
        return $this->kreditExportCsv($request, 'Sosial', [
            'SOSIAL & OBAT',
            'JAMUAN',
            'KELUARGA',
            'KASBON',
        ], 'sosial');
    }

    public function upahCsv(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));
        $tab   = $request->query('tab', '');

        $penerimas = KasHarian::whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereNotNull('sub_kategori')
            ->distinct()->orderBy('sub_kategori')->pluck('sub_kategori')->toArray();

        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', 'Upah'))
            ->whereNotNull('sub_kategori')
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($penerimas as $p) {
            $grouped[$p] = [];
            $kumulatif = 0;
            $no = 1;
            foreach ($rows->where('sub_kategori', $p) as $row) {
                $kumulatif += (float) ($row->kredit ?? 0);
                $grouped[$p][] = [
                    'no'      => $no++,
                    'tanggal' => $row->tanggal->format('d-m-Y'),
                    'uraian'  => $row->uraian,
                    'jumlah'  => (float) ($row->kredit ?? 0),
                    'total'   => $kumulatif,
                ];
            }
        }

        $hasTab = $tab !== '' && array_key_exists($tab, $grouped);
        $out = [];
        $no  = 1;

        foreach ($grouped as $p => $items) {
            if ($hasTab && $p !== $tab) continue;
            foreach ($items as $item) {
                $out[] = $hasTab
                    ? [$item['no'], $item['tanggal'], $item['uraian'], $item['jumlah'], $item['total']]
                    : [$no++, $item['tanggal'], $item['uraian'], $p, $item['jumlah'], $item['total']];
            }
        }

        $headers = $hasTab
            ? ['No', 'Tanggal', 'Keterangan', 'Jumlah', 'Total']
            : ['No', 'Tanggal', 'Keterangan', 'Penerima', 'Jumlah', 'Total'];

        return $this->streamCsv('upah-' . $start . '-to-' . $end . '.csv', $headers, $out);
    }

    private function kreditExportCsv(Request $request, string $subKelompok, array $fixed, string $prefix)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));
        $tab   = $request->query('tab', '');

        $kategori = $this->dynamicKategori($subKelompok, $fixed, $start, $end);
        $data     = $this->buildData($start, $end, $kategori);
        $grouped  = $data['grouped'];

        $hasTab = $tab !== '' && array_key_exists($tab, $grouped);
        $out = [];
        $no  = 1;

        foreach ($grouped as $kat => $items) {
            if ($hasTab && $kat !== $tab) continue;
            foreach ($items as $item) {
                $out[] = $hasTab
                    ? [$item['no'], $item['tanggal'], $item['uraian'], $item['jumlah'], $item['total']]
                    : [$no++, $item['tanggal'], $item['uraian'], $kat, $item['jumlah'], $item['total']];
            }
        }

        $headers = $hasTab
            ? ['No', 'Tanggal', 'Keterangan', 'Jumlah', 'Total']
            : ['No', 'Tanggal', 'Keterangan', 'Kategori', 'Jumlah', 'Total'];

        return $this->streamCsv($prefix . '-' . $start . '-to-' . $end . '.csv', $headers, $out);
    }

    protected function dynamicKategori(string $subKelompok, array $fixed, string $start, string $end): array
    {
        $used = KasHarian::whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereHas('akun', fn ($q) => $q->where('sub_kelompok', $subKelompok))
            ->whereNotNull('sub_kategori')
            ->distinct()->orderBy('sub_kategori')
            ->pluck('sub_kategori')
            ->toArray();

        return array_values(array_unique(array_merge($fixed, $used)));
    }

    protected function buildData(string $start, string $end, array $kategori): array
    {
        $rows = KasHarian::with('akun')
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end)
            ->whereIn('sub_kategori', $kategori)
            ->orderBy('tanggal')->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($kategori as $kat) {
            $grouped[$kat] = [];
            $kumulatif = 0;
            $no = 1;
            foreach ($rows->where('sub_kategori', $kat) as $row) {
                $kumulatif += (float) ($row->kredit ?? 0);
                $grouped[$kat][] = [
                    'no'      => $no++,
                    'tanggal' => $row->tanggal->format('d-m-Y'),
                    'uraian'  => $row->uraian,
                    'jumlah'  => (float) ($row->kredit ?? 0),
                    'total'   => $kumulatif,
                ];
            }
        }

        $ringkasan = [];
        $grandTotal = 0;
        foreach ($kategori as $kat) {
            $total = (float) $rows->where('sub_kategori', $kat)->sum('kredit');
            $ringkasan[$kat] = $total;
            $grandTotal += $total;
        }

        return compact('start', 'end', 'grouped', 'ringkasan', 'grandTotal');
    }
}
