<?php

namespace App\Http\Controllers;

use App\Models\AbsenHarian;
use App\Models\GajiBulanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    public function cetak(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $gajiList = GajiBulanan::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->map(function ($gaji) use ($bulan, $tahun) {
                $gaji->detail_absen = AbsenHarian::where('karyawan_id', $gaji->karyawan_id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', (int) $bulan)
                    ->orderBy('tanggal')
                    ->get();

                return $gaji;
            });

        $data = [
            'gajiList'    => $gajiList,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'bulanLabel'  => $bulanLabels[$bulan] ?? $bulan,
            'namaSekolah' => config('app.nama_sekolah', 'Nama Sekolah / Yayasan'),
            'alamat'      => config('app.alamat_sekolah', 'Alamat Sekolah'),
        ];

        $pdf = Pdf::loadView('pdf.slip-gaji', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream("slip-gaji-{$bulan}-{$tahun}.pdf");
    }
}
