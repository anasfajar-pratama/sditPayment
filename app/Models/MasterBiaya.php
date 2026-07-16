<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterBiaya extends Model
{
    protected $table = 'biaya_master';

    protected $fillable = [
        'tahun', 'nominal_spp', 'nominal_daftar_ulang',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nominal_spp' => 'float',
        'nominal_daftar_ulang' => 'float',
    ];
}
