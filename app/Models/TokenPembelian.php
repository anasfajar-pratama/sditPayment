<?php
// File: app/Models/TokenPembelian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenPembelian extends Model
{
    protected $table = 'token_pembelians';

    protected $fillable = [
        'token_listrik_id',
        'tanggal',
        'nominal',
        'nomor_token',
        'kwh',
        'note',
        'bulan',
        'tahun',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'kwh'     => 'decimal:2',
    ];

    public function tokenListrik(): BelongsTo
    {
        return $this->belongsTo(TokenListrik::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Posting otomatis ke kas_harian setelah pembelian disimpan ────────────

    protected static function booted(): void
    {
        static::created(function (TokenPembelian $pembelian) {
            KasHarian::postingDariToken($pembelian);
        });

        static::deleted(function (TokenPembelian $pembelian) {
            KasHarian::hapusPostingToken($pembelian->id);
        });
    }
}
