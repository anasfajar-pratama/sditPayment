<?php
// File: app/Models/Donasi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donasi extends Model
{
    protected $table = 'donasis';

    protected $fillable = [
        'donatur_id',
        'tanggal',
        'nominal',
        'note',
        'bulan',
        'tahun',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(Donatur::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Posting otomatis ke kas_harian setelah donasi disimpan ──────────────

    protected static function booted(): void
    {
        // static::created(function (Donasi $donasi) {
        //     KasHarian::postingDariDonasi($donasi);
        // });

        static::deleted(function (Donasi $donasi) {
            KasHarian::hapusPostingDonasi($donasi->id);
        });
    }
}
