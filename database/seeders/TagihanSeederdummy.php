<?php

namespace Database\Seeders;

use App\Models\Siswa;
use App\Models\JenisPembayaran;
use App\Models\Tagihan;
use Illuminate\Database\Seeder;

class TagihanSeederdummy extends Seeder
{
    public function run(): void
    {
        // Ambil jenis SPP
        $spp = JenisPembayaran::where('nama', 'SPP')->first();

        if (!$spp) {
            $this->command->warn('Jenis Pembayaran SPP tidak ditemukan!');
            return;
        }

        // Ambil 5 siswa pertama
        $siswas = Siswa::limit(5)->get();

        if ($siswas->count() < 5) {
            $this->command->warn('Hanya ada ' . $siswas->count() . ' siswa. Dummy akan dibuat sesuai jumlah siswa yang ada.');
        }

        $bulan = 'April';
        $tahun = '2026';
        $nominal = 350000; // Rp 350.000

        foreach ($siswas as $siswa) {
            Tagihan::create([
                'siswa_id'           => $siswa->id,
                'jenis_pembayaran_id'=> $spp->id,
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'nominal_tagihan'    => $nominal,
                'status'             => 'belum_bayar',
            ]);

            $this->command->info("✅ Tagihan dibuat untuk: {$siswa->nama} ({$siswa->nis})");
        }

        $this->command->info('🎉 Dummy Tagihan selesai dibuat!');
    }
}