<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreignId('jenis_pembayaran_id')->nullable()->change();
            $table->string('bulan', 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->foreignId('jenis_pembayaran_id')->nullable(false)->change();
            $table->string('bulan', 2)->nullable(false)->change();
        });
    }
};