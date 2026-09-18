<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            $table->boolean('is_yayasan')->default(false)->after('sub_kategori');
        });
    }

    public function down(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            $table->dropColumn('is_yayasan');
        });
    }
};
