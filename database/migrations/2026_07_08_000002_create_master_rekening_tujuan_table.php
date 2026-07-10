<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_rekening_tujuan', function (Blueprint $table) {
            $table->id();
            $table->string('label');       // tampilan di select box
            $table->string('bank');
            $table->string('no_rekening');
            $table->string('atas_nama');
            $table->integer('urutan')->default(0);
            $table->boolean('is_cash')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_rekening_tujuan');
    }
};