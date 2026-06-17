<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GajiBulanan extends Model
{
    protected $table = 'gaji_bulanan';

    protected $fillable = [
        'karyawan_id', 'bulan', 'tahun',
        'hari_masuk',
        'gaji_pokok', 'tunjangan', 'transport', 'thr',
        'nominal_gaji', 'potongan',
        'status_bayar', 'tanggal_bayar', 'keterangan',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'gaji_pokok'   => 'decimal:2',
        'tunjangan'    => 'decimal:2',
        'transport'    => 'decimal:2',
        'thr'          => 'decimal:2',
        'nominal_gaji' => 'decimal:2',
        'potongan'     => 'decimal:2',
        'tanggal_bayar'=> 'date',
        'hari_masuk'   => 'integer',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

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
