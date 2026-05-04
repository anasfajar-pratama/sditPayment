<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $table = 'akun';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kelompok',
        'sub_kelompok',
        'tipe',
        'is_active',
        'keterangan',
    ];

    public function jurnalDetails()
    {
        return $this->hasMany(JurnalDetail::class);
    }
}