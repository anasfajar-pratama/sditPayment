<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('token_pembelians', function (Blueprint $table) {
            $table->string('bukti')->nullable()->after('nominal');
        });
    }

    public function down(): void
    {
        Schema::table('token_pembelians', function (Blueprint $table) {
            $table->dropColumn('bukti');
        });
    }
};
