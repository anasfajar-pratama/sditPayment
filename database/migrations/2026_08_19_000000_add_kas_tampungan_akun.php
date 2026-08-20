<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('akun')->updateOrInsert(
            ['kode_akun' => '4110'],
            [
                'kode_akun' => '4110',
                'nama_akun' => 'Pendapatan Kas Tampungan',
                'kelompok' => 'Pendapatan',
                'tipe' => 'KREDIT',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('akun')->where('kode_akun', '4110')->delete();
    }
};