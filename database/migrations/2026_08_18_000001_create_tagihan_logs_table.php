<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_id')->nullable();
            $table->string('aksi'); // buat | edit | hapus
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('dilakukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_logs');
    }
};