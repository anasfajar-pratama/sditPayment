<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);

        $admin1 = User::updateOrCreate(
            ['email' => 'admin@bungacempakarw.sch.id'],
            ['name' => 'Admin', 'password' => Hash::make('admin1234')]
        );
        $admin1->assignRole('admin');

        $admin2 = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin', 'password' => Hash::make('admin1234')]
        );
        $admin2->assignRole('admin');

        $yayasan = User::updateOrCreate(
            ['email' => 'yayasan@bungacempakarw.sch.id'],
            ['name' => 'Ketua Yayasan', 'password' => Hash::make('bungacempakarw2026')]
        );
        $yayasan->assignRole('admin');

        $kasir = User::updateOrCreate(
            ['email' => 'kasir@bungacempakarw.sch.id'],
            ['name' => 'Kasir', 'password' => Hash::make('kasir1234')]
        );
        $kasir->assignRole('kasir');

        $this->call([
            AkunSeeder::class,
            AkunPengeluaranSeeder::class,
            JenisPembayaranSeeder::class,
            Siswa20262027Seeder::class
        ]);
    }
}
