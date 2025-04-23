<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Transaksi Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Transaksi</label>
                            <input value="{{ $kode }}" type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" readonly>
                            <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Transaksi</label>
                            <input value="{{ date('Y-m-d\TH:i') }}" type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required>
                            <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Pembeli</label>
                            <input type="text" name="pembeli" id="pembeli" class="form-control" required maxlength="50">
                            <small id="error-pembeli" class="error-text form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Kasir</label>
                            <input type="text" value="{{ Auth::user()->nama }}" class="form-control" readonly>
                            <input type="hidden" name="user_id" id="user_id" value="{{ Auth::id() }}">
                            <small id="error-user_id" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <h5>Detail Barang</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pilih Barang</label>
                                    <select id="add_barang_id" class="form-control">
                                        <option value="" disabled selected>- Pilih Barang -</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->barang_id }}" 
                                                data-nama="{{ $b->barang_nama }}" 
                                                data-kode="{{ $b->barang_kode }}" 
                                                data-harga="{{ $b->harga_jual }}">
                                                {{ $b->barang_nama }} - {{ $b->barang_kode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga</label>
                                    <input type="number" id="add_harga" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <input type="number" id="add_jumlah" class="form-control" min="1" value="1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="btn-add-item" class="btn btn-success btn-block">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <table class="table table-bordered table-sm" id="table-items">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Nama Barang</th>
                                    <th width="15%">Harga</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total:</th>
                                    <th id="total-amount">Rp 0</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        <small id="error-items" class="error-text form-text text-danger"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        var items = [];
        var totalAmount = 0;

        function formatRupiah(angka) {
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp ' + rupiah;
        }

        // Populate price when barang is selected
        $('#add_barang_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var harga = selectedOption.data('harga');
            $('#add_harga').val(harga);
        });

        // Add item to list
        $('#btn-add-item').click(function() {
            var barangId = $('#add_barang_id').val();
            var harga = parseInt($('#add_harga').val());
            var jumlah = parseInt($('#add_jumlah').val());
            
            if (!barangId || isNaN(harga) || isNaN(jumlah) || jumlah <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Pilih barang dan masukkan jumlah yang valid'
                });
                return;
            }

            var selectedOption = $('#add_barang_id').find('option:selected');
            var barangNama = selectedOption.data('nama');
            var subtotal = harga * jumlah;
            
            // Check if item already exists
            var existingItem = items.find(item => item.barang_id == barangId);
            if (existingItem) {
                // Update existing item
                existingItem.jumlah += jumlah;
                existingItem.subtotal = existingItem.harga * existingItem.jumlah;
            } else {
                // Add new item
                items.push({
                    barang_id: barangId,
                    nama: barangNama,
                    harga: harga,
                    jumlah: jumlah,
                    subtotal: subtotal
                });
            }
            
            // Reset inputs
            $('#add_barang_id').val('');
            $('#add_harga').val('');
            $('#add_jumlah').val(1);
            
            // Refresh table
            renderTable();
        });

        // Remove item from list
        $(document).on('click', '.btn-remove-item', function() {
            var index = $(this).data('index');
            items.splice(index, 1);
            renderTable();
        });

        // Render table with items
        function renderTable() {
            var tbody = $('#table-items tbody');
            tbody.empty();
            totalAmount = 0;
            
            if (items.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center">Tidak ada barang</td></tr>');
            } else {
                items.forEach(function(item, index) {
                    totalAmount += item.subtotal;
                    
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.nama}</td>
                            <td>${formatRupiah(item.harga)}</td>
                            <td>${item.jumlah}</td>
                            <td>${formatRupiah(item.subtotal)}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-remove-item" data-index="${index}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }
            
            $('#total-amount').text(formatRupiah(totalAmount));
        }

        // Initialize table
        renderTable();

        // Form validation and submission
        $("#form-tambah").validate({
            rules: {
                penjualan_kode: {required: true},
                pembeli: {required: true},
                user_id: {required: true, number: true},
                penjualan_tanggal: {required: true}
            },
            submitHandler: function(form) {
                // Check if there are items
                if (items.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tambahkan minimal satu barang ke transaksi'
                    });
                    return false;
                }
                
                // Prepare data for submission
                var formData = $(form).serializeArray();
                
                // Add items array
                items.forEach(function(item, index) {
                    formData.push({name: `items[${index}][barang_id]`, value: item.barang_id});
                    formData.push({name: `items[${index}][harga]`, value: item.harga});
                    formData.push({name: `items[${index}][jumlah]`, value: item.jumlah});
                });
                
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
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
                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-'+prefix).text(val[0]);
                                });
                            }
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
    });
</script>