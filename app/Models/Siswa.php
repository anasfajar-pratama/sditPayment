<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'tgl_lahir',
        'angkatan',
        'nama_orang_tua',
        'no_hp_orang_tua',
        'email_orang_tua',
        'is_calon',
        'calon_jenis',
        'calon_tingkat',
        'status_aktif',
    ];

    protected $casts = [
        'tgl_lahir'    => 'date',
        'is_calon'     => 'boolean',
        'calon_tingkat'=> 'integer',
        'status_aktif' => 'boolean',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function kelasHistories(): HasMany
    {
        return $this->hasMany(SiswaKelasHistory::class);
    }

    public function kelasSaatIni(): HasOne
    {
        return $this->hasOne(SiswaKelasHistory::class)->where('is_current', true);
    }

    public function tagihanPendaftaran(): HasOne
    {
        return $this->hasOne(Tagihan::class)->where('jenis_pembayaran_id', 1);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeCalon($query)
    {
        return $query->where('is_calon', true);
    }

    public function scopeSiswa($query)
    {
        return $query->where('is_calon', false);
    }

    public function scopeJenjang($query, string $jenjang)
    {
        return $query->whereHas('kelasSaatIni', fn($q) => $q->where('jenis_sekolah', strtoupper($jenjang)));
    }

    public function scopePerKelas($query, string $kelas, ?string $tahunAjaran = null)
    {
        $query->whereHas('kelasSaatIni', function ($q) use ($kelas, $tahunAjaran) {
            $q->where('kelas', $kelas);
            if ($tahunAjaran) $q->where('tahun_ajaran', $tahunAjaran);
        });
    }
}
