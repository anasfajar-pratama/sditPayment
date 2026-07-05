<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'tingkat', 'tahun_ajaran', 'jenis_sekolah']);
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('kelas')->nullable()->after('nama');
            $table->string('tingkat')->nullable()->after('kelas');
            $table->string('tahun_ajaran')->nullable()->after('tingkat');
            $table->string('jenis_sekolah')->nullable()->after('tahun_ajaran');
        });
    }
};
