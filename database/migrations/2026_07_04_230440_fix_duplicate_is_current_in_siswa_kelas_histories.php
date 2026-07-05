<?php

use App\Models\SiswaKelasHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix duplicate is_current=true records per siswa_id.
        // Some siswa have is_current=true on both their old (2025/2026) and
        // current (2026/2027) records due to migration + seeder overlapping.
        // We keep only the latest record per siswa as current.

        $duplicates = DB::table('siswa_kelas_histories')
            ->select('siswa_id')
            ->where('is_current', true)
            ->groupBy('siswa_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('siswa_id');

        foreach ($duplicates->chunk(100) as $ids) {
            $latestIds = DB::table('siswa_kelas_histories')
                ->selectRaw('MAX(id) as id')
                ->whereIn('siswa_id', $ids)
                ->where('is_current', true)
                ->groupBy('siswa_id')
                ->pluck('id');

            SiswaKelasHistory::whereIn('siswa_id', $ids)
                ->where('is_current', true)
                ->whereNotIn('id', $latestIds)
                ->update(['is_current' => false]);
        }
    }

    public function down(): void
    {
    }
};
