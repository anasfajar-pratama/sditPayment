<?php
// File: app/Models/Donatur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donatur extends Model
{
    protected $table = 'donatur';

    protected $fillable = [
        'nama',
        'no_hp',
        'email',
        'alamat',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function donasis(): HasMany
    {
        return $this->hasMany(Donasi::class);
    }

    public function totalDonasi(): int|float
    {
        return $this->donasis()->sum('nominal');
    }
}
