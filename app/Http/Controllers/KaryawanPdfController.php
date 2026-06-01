<?php
// File: app/Http/Controllers/KaryawanPdfController.php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanPdfController extends Controller
{
    public function print(Request $request)
    {
        $job    = $request->query('job', '');
        $status = $request->query('status', 'aktif');
        $kepeg  = $request->query('kepeg', '');
        $search = $request->query('search', '');

        $query = Karyawan::query()->orderBy('job')->orderBy('nama');

        if ($job)    $query->where('job', $job);
        if ($status) $query->where('status', $status);
        if ($kepeg)  $query->where('status_kepegawaian', $kepeg);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->get();

        // Label filter untuk judul cetak
        $filterLabel = collect([
            $job    ? 'Job: ' . ucfirst($job) : null,
            $status ? 'Status: ' . ucfirst($status) : null,
            $kepeg  ? 'Kepeg: ' . $kepeg : null,
            $search ? 'Cari: "' . $search . '"' : null,
        ])->filter()->implode(' | ');

        // Hitung per job
        $rekapJob = $karyawans->groupBy('job')
            ->map(fn ($g) => $g->count())
            ->toArray();

        return view('pdf.karyawan', compact('karyawans', 'filterLabel', 'rekapJob'));
    }
}
