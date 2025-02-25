<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'barang_id' =>1,
                'kategori_id'=>1,
                'barang_kode' => 'YMH',
                'barang_nama' => 'Yamaha R6',
                'harga_beli' => 25000000,
                'harga_jual' => 30000000,
            ],
            [
                'barang_id' =>2,
                'kategori_id'=>2,
                'barang_kode' => 'TMG',
                'barang_nama' => 'Termignoni',
                'harga_beli' => 2500000,
                'harga_jual' => 2700000,
            ],
            [
                'barang_id' =>3,
                'kategori_id'=>3,
                'barang_kode' => 'AKR',
                'barang_nama' => 'Akrapovic',
                'harga_beli' => 2700000,
                'harga_jual' => 3000000,
            ],
            [
                'barang_id' =>4,
                'kategori_id'=>1,
                'barang_kode' => 'BRM',
                'barang_nama' => 'Brembo Kit',
                'harga_beli' => 5000000,
                'harga_jual' => 7500000,
            ],
            [
                'barang_id' =>5,
                'kategori_id'=>1,
                'barang_kode' => 'BP',
                'barang_nama' => 'Brembo Spare Part',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>6,
                'kategori_id'=>2,
                'barang_kode' => 'BH',
                'barang_nama' => 'Body Halus',
                'harga_beli' => 12000000,
                'harga_jual' => 12800000,
            ],
            [
                'barang_id' =>7,
                'kategori_id'=>2,
                'barang_kode' => 'BFB',
                'barang_nama' => 'Body Full Body',
                'harga_beli' => 15000000,
                'harga_jual' => 16000000,
            ],
            [
                'barang_id' =>8,
                'kategori_id'=>2,
                'barang_kode' => 'BSP',
                'barang_nama' => 'Body Spare Part',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>9,
                'kategori_id'=>2,
                'barang_kode' => 'SR',
                'barang_nama' => 'Hell',
                'harga_beli' => 500000,
                'harga_jual' => 900000,
            ],
            [
                'barang_id' =>10,
                'kategori_id'=>3,
                'barang_kode' => 'SP',
                'barang_nama' => 'Kampas Rem',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>11,
                'kategori_id'=>3,
                'barang_kode' => 'MR',
                'barang_nama' => 'Minyak Rem',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>12,
                'kategori_id'=>3,
                'barang_kode' => 'PR',
                'barang_nama' => 'Pompa Rem',
                'harga_beli' => 500000,
                'harga_jual' => 600000,
            ],
            [
                'barang_id' =>13,
                'kategori_id'=>3,
                'barang_kode' => 'BA',
                'barang_nama' => 'Block Aftermarket',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>14,
                'kategori_id'=>3,
                'barang_kode' => 'HR',
                'barang_nama' => 'Handle Rem',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,
            ],
            [
                'barang_id' =>15,
                'kategori_id'=>3,    
                'barang_kode' => 'TRR',
                'barang_nama' => 'Tabung Rem RPD',
                'harga_beli' => 500000,
                'harga_jual' => 1000000,  
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
