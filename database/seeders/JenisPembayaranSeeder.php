<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use Illuminate\Database\Seeder;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        JenisPembayaran::create([
            'nama' => 'Daftar Masuk',
            'is_periodik' => false,
            'keterangan' => 'Pembayaran masuk kelas 1'
        ]);

        JenisPembayaran::create([
            'nama' => 'Daftar Ulang',
            'is_periodik' => false,
            'keterangan' => 'Pembayaran naik kelas'
        ]);

        JenisPembayaran::create([
            'nama' => 'SPP',
            'is_periodik' => true,
            'keterangan' => 'Sumbangan Pembinaan Pendidikan bulanan'
        ]);
    }
}