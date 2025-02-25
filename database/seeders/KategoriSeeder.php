<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'kategori_kode' => 'MOTOR',
                'kategori_nama' => 'Motor Bekas',
            ],
            [
                'kategori_id' => 2,
                'kategori_kode' => 'FB',
                'kategori_nama' => 'Full Body',
            ],
            [
                'kategori_id' => 3,
                'kategori_kode' => 'SP',
                'kategori_nama' => 'SparePart',
            ],
            [
                'kategori_id' => 4,
                'kategori_kode' => 'AV',
                'kategori_nama' => 'Aftermarket Velg',
            ],
            [
                'kategori_id' => 5,
                'kategori_kode' => 'AP',
                'kategori_nama' => 'Aftermarket Parts',
            ],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
