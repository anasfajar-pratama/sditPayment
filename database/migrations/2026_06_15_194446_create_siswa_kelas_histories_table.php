<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_kelas_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->string('kelas');
            $table->string('jenis_sekolah')->nullable();
            $table->string('tahun_ajaran');          // e.g. "2024/2025"
            $table->unsignedSmallInteger('tahun_mulai'); // e.g. 2024
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran'], 'uniq_siswa_tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas_histories');
    }
};
