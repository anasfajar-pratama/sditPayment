<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    protected $table = 'jenis_pembayaran';

    protected $fillable = ['nama', 'is_periodik', 'keterangan'];

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}