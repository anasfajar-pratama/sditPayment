<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'siswa_id',
        'jenis_pembayaran_id',
        'tagihan_id',
        'bulan',
        'tahun',
        'nominal',
        'tanggal_bayar',
        'status',
        'no_ref',
        'tgl_bayar_struk',
        'potongan',
        'bukti_bayar',
        'rekening_tujuan',
        'nama_rekening_pengirim',
        'created_by',
    ];

    protected $appends = [
        'bukti_url',
    ];

    protected $casts = [
        'tanggal_bayar'   => 'date',
        'tgl_bayar_struk' => 'date',
        'nominal'         => 'decimal:2',
        'potongan'        => 'decimal:2',
    ];

    public function getBuktiUrlAttribute(): ?string
    {
        if (!$this->bukti_bayar) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::url($this->bukti_bayar);
    }

    public function siswa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function tagihan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }
}
