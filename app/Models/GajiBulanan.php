<?php
// File: app/Models/GajiBulanan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GajiBulanan extends Model
{
    protected $table = 'gaji_bulanan';

    protected $fillable = [
        'karyawan_id', 'bulan', 'tahun',
        'hari_masuk', 'nominal_gaji', 'potongan',
        'status_bayar', 'tanggal_bayar', 'keterangan',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'nominal_gaji' => 'decimal:2',
        'potongan' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'hari_masuk' => 'integer',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Hitung ulang hari masuk dari absen_harians dan simpan.
     */
    public function syncHariMasuk(): void
    {
        $this->hari_masuk = AbsenHarian::where('karyawan_id', $this->karyawan_id)
            ->whereYear('tanggal', $this->tahun)
            ->whereMonth('tanggal', $this->bulan)
            ->whereIn('status', ['hadir', 'dinas'])
            ->count();
        $this->save();
    }
}
