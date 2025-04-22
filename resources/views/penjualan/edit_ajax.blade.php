<form action="{{ url('/penjualan/ajax' . $penjualan->penjualan_id) }}" method="POST" id="form-penjualan-edit">
    @csrf
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Transaksi Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="pembeli">Nama Pembeli</label>
                    <input type="text" name="pembeli" id="pembeli" class="form-control"
                        value="{{ $penjualan->pembeli }}" required>
                </div>

                <div class="form-group">
                    <label for="penjualan_tanggal">Tanggal Pembelian</label>
                    <input type="date" name="penjualan_tanggal" id="penjualan_tanggal"
                        value="{{ $penjualan->penjualan_tanggal }}" class="form-control" required>
                </div>

                <hr>
                <h5>Detail Barang</h5>
                <table class="table table-bordered" id="table-barang-edit">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>
                                <button type="button" class="btn btn-success btn-sm" id="addRowEdit">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualanWithBarang as $item)
                            <tr>
                                <td>
                                    <select name="barang_id[]" class="form-control barang-select" required>
                                        <option value="">Pilih Barang</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}"
                                                {{ $b->barang_id == $item['barang_id'] ? 'selected' : '' }}>
                                                {{ $b->barang_kode }} - {{ $b->barang_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="harga[]" class="form-control harga" value="{{ $item['harga_jual'] }}" readonly></td>
                                <td><input type="number" name="jumlah[]" class="form-control jumlah" min="1" value="{{ $item['jumlah'] }}"></td>
                                <td><input type="number" name="subtotal[]" class="form-control subtotal" readonly value="{{ $item['harga_jual'] * $item['jumlah'] }}"></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right mt-3">
                    <strong>Total: <span id="total-edit" class="text-primary">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span></strong>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</form>

<script>
    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function hitungSubtotal(row) {
        let harga = parseFloat(row.find('.harga').val()) || 0;
        let jumlah = parseInt(row.find('.jumlah').val()) || 0;
        let subtotal = harga * jumlah;
        row.find('.subtotal').val(subtotal);
        return subtotal;
    }

    function hitungTotal() {
        let total = 0;
        $('#table-barang-edit tbody tr').each(function () {
            total += hitungSubtotal($(this));
        });
        $('#total-edit').text(formatRupiah(total));
    }

    $('#table-barang-edit').on('change', '.barang-select', function () {
        let harga = parseFloat($(this).find(':selected').data('harga')) || 0;
        let row = $(this).closest('tr');
        row.find('.harga').val(harga);
        hitungSubtotal(row);
        hitungTotal();
    });

    $('#table-barang-edit').on('input', '.jumlah', function () {
        let row = $(this).closest('tr');
        hitungSubtotal(row);
        hitungTotal();
    });

    $('#addRowEdit').click(function () {
        let newRow = $('#table-barang-edit tbody tr:first').clone();
        newRow.find('select').val('');
        newRow.find('input').val('');
        newRow.find('.jumlah').val(1);
        $('#table-barang-edit tbody').append(newRow);
    });

    $('#table-barang-edit').on('click', '.removeRow', function () {
        if ($('#table-barang-edit tbody tr').length > 1) {
            $(this).closest('tr').remove();
            hitungTotal();
        }
    });

    $('#form-penjualan-edit').on('submit', function (e) {
        e.preventDefault();

        // serialize data barang ke format laravel array dalam JS
        let data = {
            _token: '{{ csrf_token() }}',
            pembeli: $('#pembeli').val(),
            penjualan_tanggal: $('#penjualan_tanggal').val(),
            barang: []
        };

        $('#table-barang-edit tbody tr').each(function () {
            let row = $(this);
            let barangId = row.find('select').val();
            let jumlah = row.find('.jumlah').val();
            let subtotal = row.find('.subtotal').val();

            if (barangId && jumlah) {
                data.barang.push({
                    id: barangId,
                    jumlah: jumlah,
                    total: subtotal
                });
            }
        });

        $.ajax({
            url: $('#form-penjualan-edit').attr('action'),
            method: 'POST',
            data: JSON.stringify(data), // ubah ke JSON string
            contentType: 'application/json', // tambahkan ini
            success: function (res) {
            if (res.status) {
                Swal.fire('Berhasil', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Peringatan', res.message, 'warning');
            }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
</script>
