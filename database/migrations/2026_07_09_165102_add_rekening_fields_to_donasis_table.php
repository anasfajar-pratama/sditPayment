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
        Schema::table('donasis', function (Blueprint $table) {
            $table->string('rekening_tujuan', 50)->nullable()->after('nominal');
            $table->string('nama_rekening_pengirim', 100)->nullable()->after('rekening_tujuan');
            $table->string('no_ref', 100)->nullable()->after('nama_rekening_pengirim');
        });
    }

    public function down(): void
    {
        Schema::table('donasis', function (Blueprint $table) {
            $table->dropColumn(['rekening_tujuan', 'nama_rekening_pengirim', 'no_ref']);
        });
    }
};
