<?php
// File: database/migrations/xxxx_create_kas_harians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->text('uraian');
            $table->unsignedBigInteger('akun_id')->nullable();
            $table->foreign('akun_id')->references('id')->on('akun')->nullOnDelete();
            $table->decimal('debit', 15, 2)->nullable()->comment('Uang masuk ke kas');
            $table->decimal('kredit', 15, 2)->nullable()->comment('Uang keluar dari kas');
            $table->enum('source', ['manual', 'pembayaran'])->default('manual');
            $table->unsignedBigInteger('source_id')->nullable()->comment('pembayaran.id jika otomatis');
            $table->string('bulan', 2)->nullable();
            $table->string('tahun', 4);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index(['tanggal']);
            $table->index(['source', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_harians');
    }
};
