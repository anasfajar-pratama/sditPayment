<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRekeningTujuan extends Model
{
    protected $table = 'master_rekening_tujuan';

    protected $fillable = [
        'label', 'bank', 'no_rekening', 'atas_nama', 'urutan', 'is_cash',
    ];

    protected $casts = [
        'is_cash' => 'boolean',
    ];
}