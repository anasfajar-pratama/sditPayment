<?php
// File: database/migrations/xxxx_create_absen_harians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absen_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->date('tanggal');

            /**
             * hadir  = masuk kerja normal
             * izin   = tidak hadir dengan izin resmi
             * sakit  = tidak hadir karena sakit
             * alpha  = tidak hadir tanpa keterangan
             * libur  = hari libur / tidak dijadwalkan
             * dinas  = dinas luar / tugas luar
             */
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha', 'libur', 'dinas'])
                  ->default('hadir');

            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Satu baris per karyawan per tanggal
            $table->unique(['karyawan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absen_harians');
    }
};
