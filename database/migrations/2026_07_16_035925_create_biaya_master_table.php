<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_master', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->decimal('nominal_spp', 12)->default(0);
            $table->decimal('nominal_daftar_ulang', 12)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_master');
    }
};
