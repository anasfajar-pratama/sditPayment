<?php
// File: database/migrations/xxxx_create_saldo_awal_bulans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo_awal_bulans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan', 2);   // '01' - '12'
            $table->string('tahun', 4);
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['bulan', 'tahun']); // 1 record per bulan per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_awal_bulans');
    }
};
