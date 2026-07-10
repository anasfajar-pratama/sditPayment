<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            UPDATE kas_harians kh
            JOIN pembayaran p ON p.id = kh.source_id AND kh.source = 'pembayaran'
            SET
                kh.no_ref = p.no_ref,
                kh.rekening_tujuan = p.rekening_tujuan,
                kh.nama_rekening_pengirim = p.nama_rekening_pengirim
            WHERE kh.source = 'pembayaran'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE kas_harians kh
            SET
                kh.no_ref = NULL,
                kh.rekening_tujuan = NULL,
                kh.nama_rekening_pengirim = NULL
            WHERE kh.source = 'pembayaran'
        ");
    }
};