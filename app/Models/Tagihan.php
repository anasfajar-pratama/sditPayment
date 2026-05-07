<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;
    protected $table = 'tagihan';


    protected $fillable = [
        'siswa_id',
        'jenis_pembayaran_id',
        'bulan',
        'tahun',
        'nominal_tagihan',
        'status'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    // Scope untuk memudahkan query
    public function scopeBelumBayar($query)
    {
        return $query->where('status', 'belum_bayar');
    }

    public function scopeSPP($query)
    {
        return $query->whereHas('jenisPembayaran', function($q) {
            $q->where('nama', 'SPP');
        });
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);        // atau hasMany
        // atau kalau ada kolom status di pembayaran:
        // return $this->hasOne(Pembayaran::class)->where('status', 'lunas');
    }
}