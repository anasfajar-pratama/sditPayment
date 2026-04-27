<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nis' => '2025001', 'nama' => 'Ahmad Fauzi', 'kelas' => '1A', 'tingkat' => 1, 'tahun_ajaran' => '2025-2026'],
            ['nis' => '2025002', 'nama' => 'Siti Aisyah', 'kelas' => '1B', 'tingkat' => 1, 'tahun_ajaran' => '2025-2026'],
            ['nis' => '2024005', 'nama' => 'Muhammad Rizki', 'kelas' => '2A', 'tingkat' => 2, 'tahun_ajaran' => '2025-2026'],
            ['nis' => '2023010', 'nama' => 'Nurul Hidayah', 'kelas' => '3B', 'tingkat' => 3, 'tahun_ajaran' => '2025-2026'],
            // Tambahkan sendiri siswa lain sesuai kebutuhan
        ];

        foreach ($data as $item) {
            Siswa::create(array_merge($item, [
                'nama_orang_tua' => 'Orang Tua ' . $item['nama'],
                'no_hp_orang_tua' => '081234567' . rand(100,999),
                'status_aktif' => true,
            ]));
        }
    }
}