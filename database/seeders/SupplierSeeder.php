<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => 1,
                'supplier_kode' => 'YMHR',
                'supplier_nama' => 'Yamaha R6',
                'supplier_alamat' => 'Senang Senang 87, 2124 Tokyo, Jepang',
            ],
            [
                'supplier_id' => 2,
                'supplier_kode' => 'HND',
                'supplier_nama' => 'Honda CBR 1000RR',
                'supplier_alamat' => 'Osaka 8, 0970 Osaka, Jepang',
            ],
            [
                'supplier_id' => 3,
                'supplier_kode' => 'YZR',
                'supplier_nama' => 'Yamaha YZ 125x',
                'supplier_alamat' => 'Jl. Raya Pancoran No.12, Pancoran, Jakarta',
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
