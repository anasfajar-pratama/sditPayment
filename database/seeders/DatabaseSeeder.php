<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'admin',
                'email' => 'admin@bungacempakarw.sch.id',
                'password' => Hash::make('admin1234'),
            ],
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin1234'),
            ],
            [
                'name' => 'Ketua Yayasan',
                'email' => 'yayasan@bungacempakarw.sch.id',
                'password' => Hash::make('bungacempakarw2026'),
            ],
        ]);

        $this->call([
            AkunSeeder::class,
            AkunPengeluaranSeeder::class,
            JenisPembayaranSeeder::class,
            Siswa20262027Seeder::class
        ]);
    }
}
