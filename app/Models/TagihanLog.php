<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanLog extends Model
{
    protected $table = 'tagihan_logs';

    protected $fillable = [
        'tagihan_id',
        'aksi',
        'data_sebelum',
        'data_sesudah',
        'keterangan',
        'dilakukan_oleh',
    ];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    public static function catat(
        string $aksi,
        ?int $tagihanId,
        ?array $sebelum,
        ?array $sesudah,
        ?string $keterangan = null
    ): void {
        static::create([
            'tagihan_id'     => $tagihanId,
            'aksi'           => $aksi,
            'data_sebelum'   => $sebelum,
            'data_sesudah'   => $sesudah,
            'keterangan'     => $keterangan,
            'dilakukan_oleh' => auth()->id(),
        ]);
    }
}