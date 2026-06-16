<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji_bulanan', function (Blueprint $table) {
            $table->decimal('gaji_pokok', 15, 2)->default(0)->after('hari_masuk');
            $table->decimal('tunjangan',  15, 2)->default(0)->after('gaji_pokok');
            $table->decimal('transport',  15, 2)->default(0)->after('tunjangan');
            $table->decimal('thr',        15, 2)->default(0)->after('transport');
            // kolom nominal_gaji yang sudah ada akan dipakai sebagai total akumulasi
        });
    }

    public function down(): void
    {
        Schema::table('gaji_bulanan', function (Blueprint $table) {
            $table->dropColumn(['gaji_pokok', 'tunjangan', 'transport', 'thr']);
        });
    }
};