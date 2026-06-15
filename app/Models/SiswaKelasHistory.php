<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaKelasHistory extends Model
{
    protected $table = 'siswa_kelas_histories';

    protected $fillable = [
        'siswa_id',
        'kelas',
        'jenis_sekolah',
        'tahun_ajaran',
        'tahun_mulai',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tahun_mulai' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Simpan history kelas siswa untuk tahun ajaran berjalan.
     * Tidak akan duplikat (updateOrCreate).
     */
    public static function simpan(Siswa $siswa, string $tahunAjaran, int $tahunMulai): self
    {
        return static::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran' => $tahunAjaran],
            [
                'kelas'        => $siswa->kelas,
                'jenis_sekolah'=> $siswa->jenis_sekolah,
                'tahun_mulai'  => $tahunMulai,
                'created_by'   => auth()->id(),
            ]
        );
    }
}
