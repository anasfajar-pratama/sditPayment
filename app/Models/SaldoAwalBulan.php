<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoAwalBulan extends Model
{
    protected $table = 'saldo_awal_bulans';
    protected $fillable = [
        'bulan', 'tahun', 'saldo_awal', 'keterangan', 'created_by',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getSaldo(string $bulan, string $tahun): float
    {
        return (float) static::where('bulan', $bulan)
                             ->where('tahun', $tahun)
                             ->value('saldo_awal') ?? 0;
    }
}
