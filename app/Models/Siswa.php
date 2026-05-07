<?php

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
        'tingkat', 
        'tahun_ajaran',
        'nama_orang_tua', 
        'no_hp_orang_tua', 
        'email_orang_tua', 
        'is_calon',
        'calon_jenis',
        'status_aktif'
    ];

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }

    // Di dalam class Siswa
    protected $casts = [
        'is_calon' => 'boolean',
        // ... cast lain yang sudah ada
    ];

    // Scope untuk mempermudah
    public function scopeCalon($query)
    {
        return $query->where('is_calon', true);
    }

    public function scopeSiswa($query)
    {
        return $query->where('is_calon', false);
    }
}