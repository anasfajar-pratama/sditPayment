<?php
// File: database/migrations/xxxx_add_sub_kategori_to_kas_harians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            // Kategori pengeluaran detail (TOKEN&PULSA, PERLENGKAPAN, SITI DKK, dll)
            $table->string('sub_kategori', 100)->nullable()->after('uraian');
        });
    }

    public function down(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            $table->dropColumn('sub_kategori');
        });
    }
};
