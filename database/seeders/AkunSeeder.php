<?php

namespace Database\Seeders;

use App\Models\Akun;
use Illuminate\Database\Seeder;

class AkunSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // ASET
            ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'kelompok' => 'Aset', 'tipe' => 'DEBIT'],
            ['kode_akun' => '1201', 'nama_akun' => 'Bank Mandiri', 'kelompok' => 'Aset', 'tipe' => 'DEBIT'],
            ['kode_akun' => '1202', 'nama_akun' => 'Bank BCA', 'kelompok' => 'Aset', 'tipe' => 'DEBIT'],

            // PENDAPATAN
            ['kode_akun' => '4101', 'nama_akun' => 'Pendapatan SPP Bulanan', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT'],
            ['kode_akun' => '4102', 'nama_akun' => 'Pendapatan Daftar Ulang', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT'],
            ['kode_akun' => '4103', 'nama_akun' => 'Pendapatan Pendaftaran Siswa Baru', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT'],
            ['kode_akun' => '4104', 'nama_akun' => 'Pendapatan Donasi', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT'],

            // BEBAN / PENGELUARAN
            ['kode_akun' => '6101', 'nama_akun' => 'Beban Gaji & Upah', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6201', 'nama_akun' => 'Beban Operasional Sekolah', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6202', 'nama_akun' => 'Beban Makan & Minum', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6301', 'nama_akun' => 'Beban Pemeliharaan & Perbaikan', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6401', 'nama_akun' => 'Beban Transport & Dinas', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6501', 'nama_akun' => 'Beban Pembangunan & Renovasi', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
            ['kode_akun' => '6701', 'nama_akun' => 'Beban Lain-lain', 'kelompok' => 'Beban', 'tipe' => 'DEBIT'],
        ];

        foreach ($data as $item) {
            Akun::updateOrCreate(
                ['kode_akun' => $item['kode_akun']],
                $item
            );
        }
    }
}