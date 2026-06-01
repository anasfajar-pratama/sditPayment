<?php
// File: database/migrations/xxxx_create_gaji_bulanan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->string('bulan', 2)->comment('01-12');
            $table->string('tahun', 4);
            $table->unsignedSmallInteger('hari_masuk')->default(0)->comment('Dihitung dari absen_harians');
            $table->decimal('nominal_gaji', 12, 2)->default(0);
            $table->enum('status_bayar', ['belum', 'sudah'])->default('belum');
            $table->date('tanggal_bayar')->nullable();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Satu record per karyawan per bulan/tahun
            $table->unique(['karyawan_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji_bulanan');
    }
};
