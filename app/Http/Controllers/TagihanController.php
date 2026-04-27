<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanController extends Controller
{
    public function pdf(Tagihan $tagihan)
    {
        $tagihan->load(['siswa', 'jenisPembayaran']);

        $pdf = Pdf::loadView('tagihan.tagihan', compact('tagihan'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Tagihan-' . $tagihan->siswa->nis . '.pdf');
    }
}