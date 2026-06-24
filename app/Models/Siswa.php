<?php
// ════════════════════════════════════════════════════════════
// File: app/Models/Siswa.php
// Perubahan: tambah 'jenis_sekolah' ke $fillable
// ════════════════════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jenis_sekolah',      // ← BARU: SD, SMP, DTA, PAUD
        'tingkat',
        'tahun_ajaran',
        'nama_orang_tua',
        'no_hp_orang_tua',
        'email_orang_tua',
        'is_calon',
        'calon_jenis',
        'status_aktif',
    ];

    protected $casts = [
        'is_calon'    => 'boolean',
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
        return $query->where('jenis_sekolah', strtoupper($jenjang));
    }
}