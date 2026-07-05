<?php

namespace App\Http\Controllers;

use App\Models\AbsenHarian;
use App\Models\GajiBulanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    private function buildData(string $bulan, string $tahun, ?int $karyawanId = null): array
    {
        $bulanLabels = [
            '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',    '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',     '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober',  '11' => 'November',  '12' => 'Desember',
        ];

        $query = GajiBulanan::with('karyawan')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        $gajiList = $query->get()->map(function ($gaji) use ($bulan, $tahun) {
            $gaji->detail_absen = AbsenHarian::where('karyawan_id', $gaji->karyawan_id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', (int) $bulan)
                ->orderBy('tanggal')
                ->get();
            return $gaji;
        });

        return [
            'gajiList'    => $gajiList,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'bulanLabel'  => $bulanLabels[$bulan] ?? $bulan,
            'namaSekolah' => config('app.nama_sekolah', 'Nama Sekolah / Yayasan'),
            'alamat'      => config('app.alamat_sekolah', 'Alamat Sekolah'),
            'ttdKepsek'   => $this->loadTtd('kepalasekolah'),
        ];
    }
    private function loadTtd(string $name): string
    {
        $png = storage_path("app/private/ttd/{$name}.png");
        $jpeg = storage_path("app/private/ttd/{$name}.jpeg");
        $path = $png;
        if (!file_exists($png)) {
            $path = $jpeg;
        }
        if (!file_exists($path)) return '';
        $mime = str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    public function cetak(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $data = $this->buildData($bulan, $tahun);

        $pdf = Pdf::loadView('pdf.slip-gaji', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream("slip-gaji-{$bulan}-{$tahun}.pdf");
    }

    public function share(Request $request, string $karyawanId, string $bulan, string $tahun)
    {
        if (! $request->hasValidSignature()) {
            abort(404);
        }

        $data = $this->buildData($bulan, $tahun, (int) $karyawanId);

        if ($data['gajiList']->isEmpty()) {
            abort(404);
        }

        $pdf = Pdf::loadView('pdf.slip-gaji', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream("slip-gaji-{$bulan}-{$tahun}-{$karyawanId}.pdf");
    }
}
