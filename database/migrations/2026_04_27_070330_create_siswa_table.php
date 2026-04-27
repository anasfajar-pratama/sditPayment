<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nama');
            $table->string('kelas');           // contoh: 1A, 2B, dst
            $table->integer('tingkat');        // 1 sampai 6
            $table->string('tahun_ajaran');    // contoh: 2025-2026
            $table->string('nama_orang_tua')->nullable();
            $table->string('no_hp_orang_tua')->nullable();
            $table->string('email_orang_tua')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};