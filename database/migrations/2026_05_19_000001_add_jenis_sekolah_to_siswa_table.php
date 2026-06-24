<?php
// ════════════════════════════════════════════════════════════
// File: database/migrations/xxxx_xx_xx_add_jenis_sekolah_to_siswa_table.php
// Beri nama timestamp hari ini, contoh:
// 2026_05_19_000001_add_jenis_sekolah_to_siswa_table.php
// ════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Kolom jenis_sekolah untuk siswa aktif (bukan calon)
            $table->enum('jenis_sekolah', ['SD', 'SMP', 'DTA', 'PAUD'])
                  ->nullable()
                  ->after('kelas');
        });

        // ── Tambahkan 'dta' ke enum calon_jenis yang sudah ada ────────────────
        // MySQL tidak support ALTER COLUMN enum secara langsung via Blueprint,
        // pakai raw SQL:
         DB::statement("
            ALTER TABLE siswa
            MODIFY COLUMN calon_jenis ENUM('SD', 'SMP', 'DTA', 'PAUD') NULL
        ");


        // ── Auto-fill jenis_sekolah dari tingkat untuk siswa aktif ────────────
        // Sesuaikan angka tingkat jika DTA/PAUD punya tingkat berbeda di data Anda
        DB::statement("
            UPDATE siswa
            SET jenis_sekolah = CASE
                WHEN tingkat BETWEEN 1 AND 6 THEN 'SD'
                WHEN tingkat BETWEEN 7 AND 9 THEN 'SMP'
                ELSE NULL
            END
            WHERE is_calon = 0
              AND jenis_sekolah IS NULL
              AND tingkat IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('jenis_sekolah');
        });

        // Kembalikan enum calon_jenis ke semula
        DB::statement("
            ALTER TABLE siswa
            MODIFY COLUMN calon_jenis ENUM('paud','tk','sd','smp') NULL
        ");

    }
};
