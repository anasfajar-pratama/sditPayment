<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('angkatan')->nullable()->after('nama');
        });

        Schema::table('siswa_kelas_histories', function (Blueprint $table) {
            $table->string('tingkat')->nullable()->after('kelas');
            $table->string('mutasi')->default('naik')->after('tahun_mulai');
            $table->boolean('is_current')->default(true)->after('mutasi');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('angkatan');
        });

        Schema::table('siswa_kelas_histories', function (Blueprint $table) {
            $table->dropColumn(['tingkat', 'mutasi', 'is_current']);
        });
    }
};
