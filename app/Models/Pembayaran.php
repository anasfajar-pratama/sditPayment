<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'siswa_id', 'jenis_pembayaran_id', 'tagihan_id', 'bulan', 'tahun',
        'nominal', 'tanggal_bayar', 'status', 'created_by'
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',     // ← Ini yang penting
        'nominal'       => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}