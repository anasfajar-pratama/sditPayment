<?php
// File: app/Models/Karyawan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $fillable = [
        'nama', 'nik', 'no_ktp', 'jenis_kelamin', 'tanggal_lahir',
        'tempat_lahir', 'alamat', 'no_hp', 'email', 'foto',
        'job', 'jabatan', 'mata_pelajaran', 'kelas_ajar',
        'status_kepegawaian', 'status',
        'tanggal_masuk', 'tanggal_keluar',
        'gaji_pokok',
        'nama_bank', 'no_rekening', 'atas_nama_rekening',
        'created_by',
    ];

    protected $casts = [
        'tanggal_lahir'  => 'date',
        'tanggal_masuk'  => 'date',
        'tanggal_keluar' => 'date',
        'gaji_pokok'     => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function absenHarians(): HasMany
    {
        return $this->hasMany(AbsenHarian::class);
    }

    public function gajiBulanan(): HasMany
    {
        return $this->hasMany(GajiBulanan::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeGuru($query)
    {
        return $query->whereIn('job', ['guru','admin']);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Hitung jumlah hari HADIR di bulan tertentu.
     */
    public function hariMasuk(string $bulan, string $tahun): int
    {
        return $this->absenHarians()
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->whereIn('status', ['hadir', 'dinas'])
                    ->count();
    }

    public function getJobLabelAttribute(): string
    {
        return [
            'guru'     => 'Guru',
            'admin'    => 'Admin',
            'operator' => 'Operator',
            'penjaga'  => 'Penjaga',
            'kantin'   => 'Kantin',
        ][$this->job] ?? $this->job;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'aktif'       => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'cuti'        => 'Cuti',
            'resign'      => 'Resign',
        ][$this->status] ?? $this->status;
    }
}
