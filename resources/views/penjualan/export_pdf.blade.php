<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

    <h2 class="text-center">LAPORAN PENJUALAN</h2>
    
    <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>

    @php
    $totalKeseluruhan = 0;
@endphp

<div class="flex-container">
    <table class="border-all main-table">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Penjualan</th>
                <th>Tanggal Penjualan</th>
                <th>Pembeli</th>
                <th class="text-right">Total Pembayaran</th>
                <th class="text-center">Detail Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan as $p)
                @php
                    $totalTransaksi = $p->getTotalAmount();
                    $totalKeseluruhan += $totalTransaksi;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $p->penjualan_kode }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->penjualan_tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $p->pembeli }}</td>
                    <td class="text-right">Rp {{ number_format($totalTransaksi , 0, ',', '.') }}</td>
                    <td class="text-center">
                        <table class="border-all detail-table">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Jumlah</th>
                                    <th class="text-right">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($p->details as $detail)
                                <tr>
                                    <td>{{ $detail->barang->barang_kode }}</td>
                                    <td>{{ $detail->barang->barang_nama }}</td>
                                    <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td class="text-right">{{ $detail->jumlah }}</td>
                                    <td class="text-right">{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>