<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDanaMasuk extends Model
{
    protected $table = 'log_dana_masuk';

    protected $fillable = [
        'kas_harian_id',
        'action',
        'uraian',
        'data_lama',
        'data_baru',
        'created_by',
    ];

    protected $casts = [
        'data_lama'  => 'json',
        'data_baru'  => 'json',
    ];

    public function kasHarian()
    {
        return $this->belongsTo(KasHarian::class, 'kas_harian_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}