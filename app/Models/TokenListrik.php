<?php
// File: app/Models/TokenListrik.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TokenListrik extends Model
{
    protected $table = 'token_listrik';

    protected $fillable = [
        'nama_ruangan',
        'nomor_meter',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'kwh'       => 'decimal:2',
    ];

    public function pembelians(): HasMany
    {
        return $this->hasMany(TokenPembelian::class);
    }

    public function totalPembelian(): int|float
    {
        return $this->pembelians()->sum('nominal');
    }

    public function pembelianTerakhir(): ?TokenPembelian
    {
        return $this->pembelians()->latest('tanggal')->first();
    }
}
