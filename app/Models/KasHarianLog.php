<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarianLog extends Model
{
    protected $table = 'kas_harian_logs';

    protected $fillable = [
        'kas_harian_id',
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

    public function kasHarian(): BelongsTo
    {
        return $this->belongsTo(KasHarian::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    public static function catat(
        string $aksi,
        ?int $kasHarianId,
        ?array $sebelum,
        ?array $sesudah,
        ?string $keterangan = null
    ): void {
        static::create([
            'kas_harian_id'  => $kasHarianId,
            'aksi'           => $aksi,
            'data_sebelum'   => $sebelum,
            'data_sesudah'   => $sesudah,
            'keterangan'     => $keterangan,
            'dilakukan_oleh' => auth()->id(),
        ]);
    }
}
