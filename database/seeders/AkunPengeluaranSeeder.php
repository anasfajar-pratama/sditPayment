<?php
// ════════════════════════════════════════════════════════════
// Jalankan SQL ini di database kamu untuk menambah/update akun
// Bisa lewat TablePlus / phpMyAdmin / tinker
// ════════════════════════════════════════════════════════════

// ─── SQL ─────────────────────────────────────────────────────────────────────
/*

-- Update sub_kelompok akun yang sudah ada agar terorganisir
UPDATE akun SET sub_kelompok = 'Operasional' WHERE kode_akun IN ('6201','6301','6401','6501','6701');
UPDATE akun SET sub_kelompok = 'Operasional', nama_akun = 'Beban Token & Pulsa' WHERE kode_akun = '6201';
UPDATE akun SET sub_kelompok = 'Operasional', nama_akun = 'Beban Makan & Minum' WHERE kode_akun = '6202';
UPDATE akun SET sub_kelompok = 'Operasional', nama_akun = 'Beban Pemeliharaan & Perbaikan' WHERE kode_akun = '6301';
UPDATE akun SET sub_kelompok = 'Operasional', nama_akun = 'Beban Transport & Dinas' WHERE kode_akun = '6401';
UPDATE akun SET sub_kelompok = 'Operasional', nama_akun = 'Beban Pembangunan & Renovasi' WHERE kode_akun = '6501';
UPDATE akun SET sub_kelompok = 'Upah', nama_akun = 'Beban Gaji & Upah' WHERE kode_akun = '6101';

-- Tambah akun baru (Operasional)
INSERT INTO akun (kode_akun, nama_akun, kelompok, sub_kelompok, tipe, is_active, created_at, updated_at) VALUES
('6203', 'Beban Perlengkapan',      'Beban', 'Operasional', 'DEBIT', 1, NOW(), NOW()),
('6601', 'Beban Buku & Paket',      'Beban', 'Operasional', 'DEBIT', 1, NOW(), NOW()),
('6602', 'Beban Bangku & Seragam',  'Beban', 'Operasional', 'DEBIT', 1, NOW(), NOW());

-- Tambah akun baru (Sosial)
INSERT INTO akun (kode_akun, nama_akun, kelompok, sub_kelompok, tipe, is_active, created_at, updated_at) VALUES
('6103', 'Beban Sosial & Obat',         'Beban', 'Sosial', 'DEBIT', 1, NOW(), NOW()),
('6104', 'Beban Jamuan & Konsumsi',     'Beban', 'Sosial', 'DEBIT', 1, NOW(), NOW()),
('6105', 'Beban Santunan Keluarga',     'Beban', 'Sosial', 'DEBIT', 1, NOW(), NOW()),
('6106', 'Beban Token AC Sosial',       'Beban', 'Sosial', 'DEBIT', 1, NOW(), NOW()),
('6107', 'Beban Koperasi & Atribut',    'Beban', 'Sosial', 'DEBIT', 1, NOW(), NOW());

*/

// ─── Alternatif: pakai Laravel Seeder ────────────────────────────────────────
// Buat file: database/seeders/AkunPengeluaranSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunPengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        // Update akun yang sudah ada
        $updates = [
            ['kode' => '6101', 'sub' => 'Upah',        'nama' => 'Beban Gaji & Upah'],
            ['kode' => '6201', 'sub' => 'Operasional',  'nama' => 'Beban Operasional Sekolah'],
        ];

        foreach ($updates as $u) {
            DB::table('akun')
                ->where('kode_akun', $u['kode'])
                ->update(['sub_kelompok' => $u['sub'], 'nama_akun' => $u['nama']]);
        }

        // Akun baru
        $newAkun = [
          // Sosial
            ['kode_akun' => '6103', 'nama_akun' => 'Beban Sosial',      'kelompok' => 'Beban', 'sub_kelompok' => 'Sosial', 'tipe' => 'DEBIT', 'is_active' => 1],
            ['kode_akun' => '4106', 'nama_akun' => 'Pendapatan Token AC Sosial',    'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT', 'is_active' => 1],
            ['kode_akun' => '4105', 'nama_akun' => 'Pendapatan Koperasi & Atribut', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT', 'is_active' => 1],
            ['kode_akun' => '4107', 'nama_akun' => 'Pendapatan CATERING', 'kelompok' => 'Pendapatan', 'tipe' => 'KREDIT', 'is_active' => 1],
        ];

        foreach ($newAkun as $a) {
            DB::table('akun')->insertOrIgnore(array_merge($a, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
