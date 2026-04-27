<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KuitansiController extends Controller
{
    public function pdf(Pembayaran $pembayaran)
    {
        // Load relasi
        $pembayaran->load(['siswa', 'jenisPembayaran']);

        $pdf = Pdf::loadView('kuitansi.kuitansi', compact('pembayaran'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Kuitansi-' . $pembayaran->siswa->nis . '.pdf');
    }
}