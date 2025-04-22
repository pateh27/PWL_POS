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

    @php $no = 1; $totalSemua = 0; @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Penjualan</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>User</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                @php
                    $totalPerTransaksi = $d->penjualan_detail->sum(fn($item) => $item->harga);
                    $totalSemua += $totalPerTransaksi;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->penjualan_kode }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->penjualan_tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $d->pembeli }}</td>
                    <td>{{ $d->user->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($totalPerTransaksi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="6">
                        <strong>Detail Barang:</strong>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($d->penjualan_detail as $item)
                                    <tr>
                                        <td>{{ $item->barang->barang_nama ?? '-' }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>Total Keseluruhan</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalSemua, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
