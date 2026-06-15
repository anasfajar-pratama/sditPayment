<?php
// File: app/Models/AbsenHarian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenHarian extends Model
{
    protected $table = 'absen_harians';

    protected $fillable = [
        'karyawan_id', 'tanggal', 'status', 'keterangan',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Kolom virtual bulan/tahun untuk query mudah
    public function getBulanAttribute(): string
    {
        return $this->tanggal->format('m');
    }

    public function getTahunAttribute(): string
    {
        return $this->tanggal->format('Y');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    // ─── Status helpers ───────────────────────────────────────────────────────

    public static function statusLabel(): array
    {
        return [
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            'libur' => 'Libur',
            'dinas' => 'Dinas',
        ];
    }

    public static function statusColor(): array
    {
        return [
            'hadir' => ['bg' => '#dcfce7', 'text' => '#15803d', 'border' => '#bbf7d0'],
            'izin'  => ['bg' => '#fefce8', 'text' => '#a16207', 'border' => '#fde68a'],
            'sakit' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
            'alpha' => ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#fecaca'],
            'libur' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#bfdbfe'],
            'dinas' => ['bg' => '#f5f3ff', 'text' => '#7c3aed', 'border' => '#ddd6fe'],
        ];
    }
}
