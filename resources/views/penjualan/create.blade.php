@extends('layouts.template')
@section('content')

<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah-penjualan">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Tanggal Penjualan</label>
                    <input type="date" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                    <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                </div>
                
                {{-- Barang yang dijual --}}
                <div id="barang-container">
                    <div class="form-group" id="barang-item-1">
                        <label for="barang-1">Pilih Barang</label>
                        <select class="form-control" id="barang-1" name="barang[0][id]" required>
                            @foreach ($barang as $item)
                            <option value="{{ $item['barang_id'] }}">
                                {{ $item['barang_nama'] }} - Rp{{ number_format($item['harga_jual'], 0, ',', '.') }} (Stok: {{ $item['stock_available'] ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>
                        <label for="jumlah-1">Jumlah</label>
                        <input type="number" class="form-control" id="jumlah-1" name="barang[0][jumlah]" min="1" required>
                    </div>
                </div>
                
                {{-- Tambah Barang --}}
                <button type="button" id="add-barang" class="btn btn-secondary">Tambah Barang</button>
                
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection

<script>
    $(document).ready(function() {
        // Validasi form
        $("#form-tambah-penjualan").validate({
            rules: {
                pembeli: {required: true},
                penjualan_tanggal: {required: true},
                'barang[0][id]': {required: true},
                'barang[0][jumlah]': {required: true, min: 1}
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataPenjualan.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-'+prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

        // Menambahkan barang secara dinamis
        let barangCount = 1;

        $("#add-barang").on("click", function() {
            barangCount++;
            const barangContainer = $("#barang-container");
            const newBarangItem = 
                <div class="form-group" id="barang-item-${barangCount}">
                    <label for="barang-${barangCount}">Pilih Barang</label>
                    <select class="form-control" id="barang-${barangCount}" name="barang[${barangCount - 1}][id]" required>
                        @foreach ($barang as $item)
                            <option value="{{ $item['barang_id'] }}">
                                {{ $item['barang_nama'] }} - Rp{{ number_format($item['harga_jual'], 0, ',', '.') }} (Stok: {{ $item['stock_available'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    <label for="jumlah-${barangCount}">Jumlah</label>
                    <input type="number" class="form-control" id="jumlah-${barangCount}" name="barang[${barangCount - 1}][jumlah]" min="1" required>
                </div>
            ;
            barangContainer.append(newBarangItem);
        });
    });
</script>
