<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'admin',
        //     'email' => 'admin@bungacempakarw.sch.id',
        //     'password' => Hash::make('admin1234')
        // ]);

        $this->call([
            AkunSeeder::class,
            AkunPengeluaranSeeder::class,
            JenisPembayaranSeeder::class,
            Siswa2026Seeder::class
        ]);
    }
}
