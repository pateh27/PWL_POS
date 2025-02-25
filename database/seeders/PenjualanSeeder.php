<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'penjualan_id' => 1,
                'user_id' => 1,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS1',
                'penjualan_tanggal' => '2025-02-02',
            ],
            [
                'penjualan_id' => 2,
                'user_id' => 1,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS2',
                'penjualan_tanggal' => '2025-02-03',
            ],
            [
                'penjualan_id' => 3,
                'user_id' => 1,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS3',
                'penjualan_tanggal' => '2025-02-04',
            ],
            [
                'penjualan_id' => 4,
                'user_id' => 2,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS4',
                'penjualan_tanggal' => '2025-02-05',
            ],
            [
                'penjualan_id' => 5,
                'user_id' => 2,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS5',
                'penjualan_tanggal' => '2025-02-06',
            ],
            [
                'penjualan_id' => 6,
                'user_id' => 2,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS6',
                'penjualan_tanggal' => '2025-02-07',
            ],
            [
                'penjualan_id' => 7,
                'user_id' => 3,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS7',
                'penjualan_tanggal' => '2025-02-08',
            ],
            [
                'penjualan_id' => 8,
                'user_id' => 3,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS8',
                'penjualan_tanggal' => '2025-02-09',
            ],
            [
                'penjualan_id' => 9,
                'user_id' => 3,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS9',
                'penjualan_tanggal' => '2025-02-10',
            ],
            [
                'penjualan_id' => 10,
                'user_id' => 3,
                'pembeli' => 'Fatih',
                'penjualan_kode' => 'HRS10',
                'penjualan_tanggal' => '2025-02-11',
            ],
        ];

        DB::table('t_penjualan')->insert($data);
    }
}
