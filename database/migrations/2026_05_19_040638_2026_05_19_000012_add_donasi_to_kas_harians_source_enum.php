<?php
// File: database/migrations/2026_05_19_000012_add_donasi_to_kas_harians_source_enum.php
// Perlu dijalankan agar bisa insert ke kas_harians dengan source='donasi'

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE kas_harians
            MODIFY COLUMN source ENUM('manual','pembayaran','donasi') NOT NULL DEFAULT 'manual'
        ");
    }

    public function down(): void
    {
        // Hapus baris donasi dulu agar tidak error saat rollback
        DB::statement("UPDATE kas_harians SET source = 'manual' WHERE source = 'donasi'");

        DB::statement("
            ALTER TABLE kas_harians
            MODIFY COLUMN source ENUM('manual','pembayaran') NOT NULL DEFAULT 'manual'
        ");
    }
};
