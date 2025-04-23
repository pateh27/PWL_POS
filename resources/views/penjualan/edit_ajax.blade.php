@empty($penjualan)
     <div id="modal-master" class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <div class="alert alert-danger">
                     <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                     Data yang anda cari tidak ditemukan
                 </div>
                 <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
             </div>
         </div>
     </div>
 @else
     <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
     @csrf
     @method('PUT')
     <div id="modal-master" class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Edit Data Penjualan {{ $penjualan->penjualan_kode }}</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <div class="form-group">
                     <label>Nama Pembeli</label>
                     <input type="text" name="pembeli" id="pembeli" class="form-control" value="{{ $penjualan->pembeli }}" maxlength="50" required>
                     <small id="error-pembeli" class="error-text form-text text-danger"></small>
                 </div>
                 <div class="form-group">
                     <label>Tanggal Penjualan</label>
                     <input type="date" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" value="{{ date('Y-m-d', strtotime($penjualan->penjualan_tanggal)) }}" required>
                     <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                 </div>
                 <div class="form-group">
                     <label>Petugas</label>
                     <select name="user_id" id="user_id" class="form-control" required>
                         <option value="" disabled>- Pilih Petugas -</option>
                         @foreach ($user as $u)
                             <option {{ ($u->user_id == $penjualan->user_id) ? 'selected' : '' }} value="{{ $u->user_id }}">{{ $u->nama }}</option>
                         @endforeach
                     </select>
                     <small id="error-user_id" class="error-text form-text text-danger"></small>
                 </div>
 
                 <div class="mt-4">
                     <h5>Detail Barang</h5>
                     <button type="button" class="btn btn-sm btn-success mb-2" id="btn-add-item">
                         <i class="fas fa-plus"></i> Tambah Item
                     </button>
                     
                     <div class="table-responsive">
                         <table class="table table-bordered" id="detail-table">
                             <thead>
                                 <tr>
                                     <th>Barang</th>
                                     <th width="120px">Jumlah</th>
                                     <th width="200px">Harga</th>
                                     <th width="200px">Subtotal</th>
                                     <th width="80px">Aksi</th>
                                 </tr>
                             </thead>
                             <tbody id="detail-items">
                                 @foreach($penjualan->details as $index => $detail)
                                 <tr class="item-row">
                                     <td>
                                         <select name="items[{{ $index }}][barang_id]" class="form-control barang-select" required>
                                             <option value="">- Pilih Barang -</option>
                                             @foreach($barang as $b)
                                                 <option value="{{ $b->barang_id }}" 
                                                         data-harga="{{ $b->harga_jual }}"
                                                         {{ $detail->barang_id == $b->barang_id ? 'selected' : '' }}>
                                                     {{ $b->barang_kode }} - {{ $b->barang_nama }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <small class="error-text form-text text-danger error-barang"></small>
                                     </td>
                                     <td>
                                         <input type="number" name="items[{{ $index }}][jumlah]" class="form-control item-qty" min="1" value="{{ $detail->jumlah }}" required>
                                         <small class="error-text form-text text-danger error-jumlah"></small>
                                     </td>
                                     <td>
                                         <input type="number" name="items[{{ $index }}][harga]" class="form-control item-price" min="0" value="{{ $detail->harga }}" required readonly>
                                         <small class="error-text form-text text-danger error-harga"></small>
                                     </td>
                                     <td>
                                         <input type="text" class="form-control item-subtotal" value="{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}" readonly>
                                     </td>
                                     <td>
                                         <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                             <i class="fas fa-trash"></i>
                                         </button>
                                     </td>
                                 </tr>
                                 @endforeach
                             </tbody>
                             <tfoot>
                                 <tr>
                                     <th colspan="3" class="text-right">Total:</th>
                                     <th id="total-amount">{{ number_format($penjualan->getTotalAmount(), 0, ',', '.') }}</th>
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
         // Recalculate subtotal when quantity or price changes
         $(document).on('input', '.item-qty, .item-price', function() {
             calculateSubtotal($(this).closest('tr'));
             calculateTotal();
         });
         
         // Add new item row
         $('#btn-add-item').click(function() {
             let rowCount = $('.item-row').length;
             let newRow = `
                 <tr class="item-row">
                     <td>
                         <select name="items[${rowCount}][barang_id]" class="form-control barang-select" required>
                             <option value="">- Pilih Barang -</option>
                             @foreach($barang as $b)
                                 <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">
                                     {{ $b->barang_kode }} - {{ $b->barang_nama }}
                                 </option>
                             @endforeach
                         </select>
                         <small class="error-text form-text text-danger error-barang"></small>
                     </td>
                     <td>
                         <input type="number" name="items[${rowCount}][jumlah]" class="form-control item-qty" min="1" value="1" required>
                         <small class="error-text form-text text-danger error-jumlah"></small>
                     </td>
                     <td>
                         <input type="number" name="items[${rowCount}][harga]" class="form-control item-price" min="0" value="0" readonly>
                     </td>
                     <td>
                         <input type="text" class="form-control item-subtotal" value="0" readonly>
                     </td>
                     <td>
                         <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                             <i class="fas fa-trash"></i>
                         </button>
                     </td>
                 </tr>
             `;
             $('#detail-items').append(newRow);
         });
         
         // Remove item row
         $(document).on('click', '.btn-remove-item', function() {
             if ($('.item-row').length > 1) {
                 $(this).closest('tr').remove();
                 reindexRows();
                 calculateTotal();
             } else {
                 Swal.fire({
                     icon: 'error',
                     title: 'Tidak Dapat Menghapus',
                     text: 'Minimal harus ada satu item barang!'
                 });
             }
         });
         
         // Set price when barang is selected
         $(document).on('change', '.barang-select', function() {
             let row = $(this).closest('tr');
             let harga = $(this).find('option:selected').data('harga') || 0;
             row.find('.item-price').val(harga);
             calculateSubtotal(row);
             calculateTotal();
         });
         
         // Function to calculate subtotal for a row
         function calculateSubtotal(row) {
             let qty = parseInt(row.find('.item-qty').val()) || 0;
             let price = parseInt(row.find('.item-price').val()) || 0;
             let subtotal = qty * price;
             row.find('.item-subtotal').val(formatNumber(subtotal));
         }
         
         // Function to calculate total
         function calculateTotal() {
             let total = 0;
             $('.item-row').each(function() {
                 let qty = parseInt($(this).find('.item-qty').val()) || 0;
                 let price = parseInt($(this).find('.item-price').val()) || 0;
                 total += qty * price;
             });
             $('#total-amount').text(formatNumber(total));
         }
         
         // Function to reindex rows after removal
         function reindexRows() {
             $('.item-row').each(function(index) {
                 $(this).find('select[name^="items"]').attr('name', `items[${index}][barang_id]`);
                 $(this).find('input.item-qty').attr('name', `items[${index}][jumlah]`);
                 $(this).find('input.item-price').attr('name', `items[${index}][harga]`);
             });
         }
         
         // Format number with thousands separator
         function formatNumber(num) {
             return new Intl.NumberFormat('id-ID').format(num);
         }
         
         // Form validation and submit
         $('#form-edit').validate({
             rules: {
                 pembeli: {required: true, maxlength: 50},
                 penjualan_tanggal: {required: true},
                 user_id: {required: true, number: true}
             },
             submitHandler: function(form) {
                 // Validate at least one item exists
                 if ($('.item-row').length < 1) {
                     Swal.fire({
                         icon: 'error',
                         title: 'Validasi Gagal',
                         text: 'Minimal harus ada satu item barang!'
                     });
                     return false;
                 }
                 
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
                             // Reload DataTable if it exists
                             if (typeof dataPenjualan !== 'undefined') {
                                 dataPenjualan.ajax.reload();
                             }
                         } else {
                             $('.error-text').text('');
                             
                             // Handle validation errors for main fields
                             $.each(response.msgField, function(field, messages) {
                                 if (field.includes('items.')) {
                                     // Handle item array validation errors
                                     let parts = field.split('.');
                                     let index = parseInt(parts[1].replace(/\D/g,''));
                                     let itemField = parts[2];
                                     
                                     // Display error for specific item field
                                     $(`.item-row:eq(${index})`).find(`.error-${itemField}`).text(messages[0]);
                                 } else {
                                     // Display error for main fields
                                     $(`#error-${field}`).text(messages[0]);
                                 }
                             });
                             
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Terjadi Kesalahan',
                                 text: response.message
                             });
                         }
                     },
                     error: function(xhr, status, error) {
                         Swal.fire({
                             icon: 'error',
                             title: 'Terjadi Kesalahan',
                             text: 'Gagal menghubungi server. Silakan coba lagi.'
                         });
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
 @endempty