<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_dana_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_harian_id')->constrained('kas_harians')->onDelete('cascade');
            $table->string('action'); // create, update, unverify
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_dana_masuk');
    }
};