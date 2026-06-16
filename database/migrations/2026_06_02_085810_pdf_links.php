<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 16)->unique()->index();
            $table->foreignId('pdf_id')->constrained('pembayaran')->cascadeOnDelete();
            $table->string('original_url');
            $table->enum('jenis', ['kuitansi', 'tagihan'])->default('kuitansi');
            $table->unsignedInteger('jumlah_view')->default(0);
            $table->timestamp('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
