@extends('layouts.template')

@section('content')
<div class="container">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumb->list as $item)
                <li class="breadcrumb-item">{{ $item }}</li>
            @endforeach
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        </ol>
    </nav>

    <h3>{{ $page->title }}</h3>

    {{-- Form Edit Penjualan --}}
    <form action="{{ url('/penjualan/edit/' . $penjualan->penjualan_id . '/update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Pembeli --}}
        <div class="form-group">
            <label for="pembeli">Nama Pembeli</label>
            <input type="text" class="form-control" id="pembeli" name="pembeli" value="{{ $penjualan->pembeli }}" required>
        </div>

        {{-- Tanggal Penjualan --}}
        <div class="form-group">
            <label for="penjualan_tanggal">Tanggal</label>
            <input type="date" class="form-control" id="penjualan_tanggal" name="penjualan_tanggal" value="{{ $penjualan->penjualan_tanggal }}" required>
        </div>

        {{-- Detail Barang --}}
        <h5>Barang yang Dijual</h5>
        <div id="barang-container">
            @foreach ($penjualan_detail as $index => $item)
            <div class="form-row align-items-end mb-2">
                <div class="col-md-5">
                    <label>Barang</label>
                    <select name="barang_id[]" class="form-control">
                        @foreach ($barang as $b)
                            <option value="{{ $b['barang_id'] }}" {{ $b['barang_id'] == $item['barang_id'] ? 'selected' : '' }}>
                                {{ $b['barang_nama'] }} - Rp{{ number_format($b['harga_jual'], 0, ',', '.') }} (Stok: {{ $b['stock_available'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah[]" class="form-control" value="{{ $item['jumlah'] }}" min="1" required>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-barang">Hapus</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-info mb-3" id="tambah-barang">+ Tambah Barang</button>

        {{-- Tombol Simpan --}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Penjualan</button>
            <a href="{{ url('/penjualan') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

{{-- Template Row Barang untuk JavaScript --}}
<template id="barang-template">
    <div class="form-row align-items-end mb-2">
        <div class="col-md-5">
            <label>Barang</label>
            <select name="barang_id[]" class="form-control">
                @foreach ($barang as $b)
                    <option value="{{ $b['barang_id'] }}">
                        {{ $b['barang_nama'] }} - Rp{{ number_format($b['harga_jual'], 0, ',', '.') }} (Stok: {{ $b['stock_available'] ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" class="form-control" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-barang">Hapus</button>
        </div>
    </div>
</template>

{{-- JavaScript untuk tambah/hapus barang --}}
@push('scripts')
<script>
    document.getElementById('tambah-barang').addEventListener('click', function () {
        const template = document.getElementById('barang-template').content.cloneNode(true);
        document.getElementById('barang-container').appendChild(template);
    });

    document.getElementById('barang-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-barang')) {
            e.target.closest('.form-row').remove();
        }
    });
</script>
@endpush

@endsection
