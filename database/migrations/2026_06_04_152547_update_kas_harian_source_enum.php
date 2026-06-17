<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah nilai 'token' ke enum source di kas_harians
        DB::statement("ALTER TABLE `kas_harians` MODIFY COLUMN `source`
            ENUM('manual','pembayaran','donasi','token') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `kas_harians` MODIFY COLUMN `source`
            ENUM('manual','pembayaran','donasi') NULL");
    }
};
