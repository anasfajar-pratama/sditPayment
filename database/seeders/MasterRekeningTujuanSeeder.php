<?php

namespace Database\Seeders;

use App\Models\MasterRekeningTujuan;
use Illuminate\Database\Seeder;

class MasterRekeningTujuanSeeder extends Seeder
{
    public function run(): void
    {
        MasterRekeningTujuan::insert([
            [
                'label'      => 'Cash',
                'bank'       => 'Cash',
                'no_rekening'=> '-',
                'atas_nama'  => '-',
                'urutan'     => 0,
                'is_cash'    => true,
            ],
            [
                'label'      => 'Mandiri 0060006387561 a.n. Sri Utami',
                'bank'       => 'Mandiri',
                'no_rekening'=> '0060006387561',
                'atas_nama'  => 'Sri Utami',
                'urutan'     => 1,
                'is_cash'    => false,
            ],
            [
                'label'      => 'BNI 0172794880 a.n. Sri Utami',
                'bank'       => 'BNI',
                'no_rekening'=> '0172794880',
                'atas_nama'  => 'Sri Utami',
                'urutan'     => 2,
                'is_cash'    => false,
            ],
            [
                'label'      => 'BCA 2301797747 a.n. Sri Utami',
                'bank'       => 'BCA',
                'no_rekening'=> '2301797747',
                'atas_nama'  => 'Sri Utami',
                'urutan'     => 3,
                'is_cash'    => false,
            ],
            [
                'label'      => 'BSI 7236336764 a.n. Sri Utami',
                'bank'       => 'BSI',
                'no_rekening'=> '7236336764',
                'atas_nama'  => 'Sri Utami',
                'urutan'     => 4,
                'is_cash'    => false,
            ],
        ]);
    }
}
