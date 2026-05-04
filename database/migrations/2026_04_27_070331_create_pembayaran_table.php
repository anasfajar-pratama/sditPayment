<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('jenis_pembayaran_id')->constrained('jenis_pembayaran');
            
            $table->string('bulan')->nullable();        // Januari, Februari, ... (hanya SPP)
            $table->string('tahun');                    // 2026
            
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_bayar');
            $table->enum('status', ['pending', 'lunas','cicilan'])->default('pending');
            
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Index untuk performa
            $table->index(['siswa_id', 'status']);
            $table->index(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};