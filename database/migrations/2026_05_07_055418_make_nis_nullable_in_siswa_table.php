<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis')->nullable()->change();
            $table->string('kelas')->nullable()->change();
            $table->string('tingkat')->nullable()->change();
            $table->string('tahun_ajaran')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis')->nullable(false)->change();
            $table->string('kelas')->nullable(false)->change();
            $table->string('tingkat')->nullable(false)->change();
            $table->string('tahun_ajaran')->nullable(false)->change();

        });
    }
};