<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_harian_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kas_harian_id')->nullable();
            $table->enum('aksi', ['buat', 'edit', 'hapus']);
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('dilakukan_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_harian_logs');
    }
};
