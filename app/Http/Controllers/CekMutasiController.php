<?php

namespace App\Http\Controllers;

use App\Models\KasHarian;
use App\Traits\ExportsCsv;
use Illuminate\Http\Request;

class CekMutasiController extends Controller
{
    use ExportsCsv;

    public function exportCsv(Request $request)
    {
        $tanggalDari   = $request->query('tanggal_dari', '');
        $tanggalSampai = $request->query('tanggal_sampai', '');
        $q             = $request->query('q', '');
        $rekening      = $request->query('rekening', '');
        $status        = $request->query('status', 'all');

        $query = KasHarian::with(['akun', 'verifiedBy'])
            ->where('debit', '>', 0);

        if ($tanggalDari) {
            $query->whereDate('tanggal', '>=', $tanggalDari);
        }
        if ($tanggalSampai) {
            $query->whereDate('tanggal', '<=', $tanggalSampai);
        }
        if ($q) {
            $query->where(function ($inner) use ($q) {
                $inner->where('no_ref', 'like', "%{$q}%")
                  ->orWhere('nama_rekening_pengirim', 'like', "%{$q}%")
                  ->orWhere('uraian', 'like', "%{$q}%");
            });
        }
        if ($rekening) {
            $query->where('rekening_tujuan', $rekening);
        }

        match ($status) {
            'pending'  => $query->whereNull('verified_at'),
            'verified' => $query->whereNotNull('verified_at'),
            default    => null,
        };

        $rows = $query->orderBy('tanggal')->orderBy('id')->get();

        $out = [];
        $no  = 1;
        foreach ($rows as $r) {
            $out[] = [
                $no++,
                $r->tanggal->format('d M Y'),
                $r->uraian,
                $r->akun?->nama_akun ?? '—',
                $r->no_ref ?? '',
                $r->rekening_tujuan ?? '',
                $r->nama_rekening_pengirim ?? '',
                (float) $r->debit,
                $r->verified_at ? 'Terverifikasi' : 'Belum Verifikasi',
                $r->verified_at ? $r->verified_at->format('d M Y H:i') : '',
                $r->verifiedBy?->name ?? '',
            ];
        }

        $headers = ['No', 'Tanggal', 'Uraian', 'Akun', 'No. Referensi', 'Rekening Tujuan', 'Pengirim', 'Jumlah', 'Status', 'Tgl Verifikasi', 'Verifikator'];

        return $this->streamCsv('cek-mutasi-' . now()->format('Ymd_His') . '.csv', $headers, $out);
    }
}