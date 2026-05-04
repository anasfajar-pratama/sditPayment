<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique();           // contoh: 4101, 6501
            $table->string('nama_akun');
            $table->string('kelompok');                      // Aset, Pendapatan, Beban
            $table->string('sub_kelompok')->nullable();
            $table->enum('tipe', ['DEBIT', 'KREDIT']);       // Normal balance
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('akun');
    }
};