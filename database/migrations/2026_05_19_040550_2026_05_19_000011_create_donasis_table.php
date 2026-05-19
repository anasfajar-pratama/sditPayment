<?php
// File: database/migrations/2026_05_19_000011_create_donasis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donatur_id')->constrained('donatur')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->text('note')->nullable();
            $table->string('bulan', 2)->nullable();
            $table->string('tahun', 4)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['donatur_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasis');
    }
};
