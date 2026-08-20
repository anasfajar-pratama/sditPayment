<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_dana_masuk', function (Blueprint $table) {
            $table->dropForeign(['kas_harian_id']);
            $table->foreignId('kas_harian_id')->nullable()->change();
            $table->foreign('kas_harian_id')->references('id')->on('kas_harians')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('log_dana_masuk', function (Blueprint $table) {
            $table->dropForeign(['kas_harian_id']);
            $table->foreignId('kas_harian_id')->nullable(false)->change();
            $table->foreign('kas_harian_id')->references('id')->on('kas_harians')->onDelete('cascade');
        });
    }
};
