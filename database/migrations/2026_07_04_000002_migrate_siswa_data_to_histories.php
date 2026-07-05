<?php

use App\Models\Siswa;
use App\Models\SiswaKelasHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Siswa::whereNotNull('kelas')
            ->chunk(100, function ($siswaList) use ($now) {
                foreach ($siswaList as $siswa) {
                    $parts = explode('/', $siswa->tahun_ajaran ?? '');
                    $tahunMulai = (int) ($parts[0] ?? date('Y'));

                    SiswaKelasHistory::updateOrCreate(
                        ['siswa_id' => $siswa->id, 'tahun_ajaran' => $siswa->tahun_ajaran],
                        [
                            'kelas'         => $siswa->kelas,
                            'tingkat'       => $siswa->tingkat,
                            'jenis_sekolah' => $siswa->jenis_sekolah,
                            'tahun_mulai'   => $tahunMulai,
                            'mutasi'        => 'naik',
                            'is_current'    => true,
                            'created_by'    => 1,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]
                    );
                }
            });

        // Set is_current = false for all, then set only the latest per siswa
        SiswaKelasHistory::query()->update(['is_current' => false]);

        $latestIds = SiswaKelasHistory::selectRaw('MAX(id) as id')
            ->groupBy('siswa_id')
            ->pluck('id');

        SiswaKelasHistory::whereIn('id', $latestIds)->update(['is_current' => true]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
    }
};
