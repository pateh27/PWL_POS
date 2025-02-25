<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $data=[
            [
                //Barang 1 (Yamaha R6)
                'detail_id' => 1,
                'penjualan_id' => 1,
                'barang_id' => 1,
                'harga' => 30000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 2,
                'penjualan_id' => 1,
                'barang_id' => 1,
                'harga' => 30000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 3,
                'penjualan_id' => 1,
                'barang_id' => 1,
                'harga' => 30000000,
                'jumlah' =>  1,
            ],
            //Barang 2 
            [
                'detail_id' => 4,
                'penjualan_id' => 2,
                'barang_id' => 2,
                'harga' => 2500000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 5,
                'penjualan_id' => 2,
                'barang_id' => 2,
                'harga' => 2500000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 6,
                'penjualan_id' => 2,
                'barang_id' => 2,
                'harga' => 2500000,
                'jumlah' =>  1,
            ],
            //Barang 3
            [
                'detail_id' => 7,
                'penjualan_id' => 3,
                'barang_id' => 3,
                'harga' => 3000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 8,
                'penjualan_id' => 3,
                'barang_id' => 3,
                'harga' => 3000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 9,
                'penjualan_id' => 3,
                'barang_id' => 3,
                'harga' => 3000000,
                'jumlah' =>  1,
            ],
            //Barang 4
            [
                'detail_id' => 10,
                'penjualan_id' => 4,
                'barang_id' => 4,
                'harga' => 5000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 11,
                'penjualan_id' => 4,
                'barang_id' => 4,
                'harga' => 5000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 12,
                'penjualan_id' => 4,
                'barang_id' => 4,
                'harga' => 5000000,
                'jumlah' =>  1,
            ],
            //Barang 5
            [
                'detail_id' => 13,
                'penjualan_id' => 5,
                'barang_id' => 5,
                'harga' => 500000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 14,
                'penjualan_id' => 5,
                'barang_id' => 5,
                'harga' => 500000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 15,
                'penjualan_id' => 5,
                'barang_id' => 5,
                'harga' => 500000,
                'jumlah' =>  1,
            ],
            //Barang 6
            [
                'detail_id' => 16,
                'penjualan_id' => 6,
                'barang_id' => 6,
                'harga' => 12800000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 17,
                'penjualan_id' => 6,
                'barang_id' => 6,
                'harga' => 12800000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 18,
                'penjualan_id' => 6,
                'barang_id' => 6,
                'harga' => 12800000,
                'jumlah' =>  1,
            ],
            //Barang 7
            [
                'detail_id' => 19,
                'penjualan_id' => 7,
                'barang_id' => 7,
                'harga' => 16000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 20,
                'penjualan_id' => 7,
                'barang_id' => 7,
                'harga' => 16000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 21,
                'penjualan_id' => 7,
                'barang_id' => 7,
                'harga' => 16000000,
                'jumlah' =>  1,
            ],
            //Barang 8
            [
                'detail_id' => 22,
                'penjualan_id' => 8,
                'barang_id' => 8,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 23,
                'penjualan_id' => 8,
                'barang_id' => 8,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 24,
                'penjualan_id' => 8,
                'barang_id' => 8,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
            //Barang 9
            [
                'detail_id' => 25,
                'penjualan_id' => 9,
                'barang_id' => 9,
                'harga' => 900000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 26,
                'penjualan_id' => 9,
                'barang_id' => 9,
                'harga' => 900000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 27,
                'penjualan_id' => 9,
                'barang_id' => 9,
                'harga' => 900000,
                'jumlah' =>  1,
            ],
            //Barang 10
            [
                'detail_id' => 28,
                'penjualan_id' => 10,
                'barang_id' => 10,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 29,
                'penjualan_id' => 10,
                'barang_id' => 10,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
            [
                'detail_id' => 30,
                'penjualan_id' => 10,
                'barang_id' => 10,
                'harga' => 1000000,
                'jumlah' =>  1,
            ],
        ];
        DB::table('t_penjualan_detail')->insert($data);
    }
}
