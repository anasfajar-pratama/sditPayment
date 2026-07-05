<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE siswa MODIFY COLUMN calon_jenis ENUM('SD', 'SMP', 'DTA', 'PAUD', 'TK') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE siswa MODIFY COLUMN calon_jenis ENUM('SD', 'SMP', 'DTA', 'PAUD') NULL");
    }
};
