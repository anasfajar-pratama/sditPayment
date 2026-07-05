<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    public function cetakPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));

        $kategori = [
            'TOKEN & PULSA',
            'PERLENGKAPAN',
            'MAINTENANCE & FC',
            'TRANSPORT & DINAS',
            'PEMBANGUNAN',
            'BUKU PAKET',
            'BANGKU & SERAGAM',
        ];

        $data = $this->buildData($start, $end, $kategori);
        $data['kategori'] = $kategori;

        $pdf = Pdf::loadView('pdf.operasional', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('operasional-' . $start . '-to-' . $end . '.pdf');
    }

    public function sosialPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfMonth()->format('Y-m-d'));
        $end   = $request->query('end', now()->endOfMonth()->format('Y-m-d'));

        $kategori = [
            'SOSIAL & OBAT',
            'JAMUAN',
            'KELUARGA',
            'KASBON',
        ];

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
