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
     <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') }}" method="POST" id="form-delete">
         @csrf
         @method('DELETE')
         <div id="modal-master" class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">Hapus Data Penjualan</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="alert alert-warning">
                         <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                         Apakah Anda ingin menghapus data seperti di bawah ini?
                     </div>
                     <table class="table table-sm table-bordered table-striped">
                         <tr>
                             <th class="text-right col-3">Kode Penjualan :</th>
                             <td class="col-9">{{ $penjualan->penjualan_kode }}</td>
                         </tr>
                         <tr>
                             <th class="text-right col-3">Pembeli :</th>
                             <td class="col-9">{{ $penjualan->pembeli }}</td>
                         </tr>
                         <tr>
                             <th class="text-right col-3">Petugas :</th>
                             <td class="col-9">{{ $penjualan->user->nama }}</td>
                         </tr>
                         <tr>
                             <th class="text-right col-3">Tanggal Penjualan :</th>
                             <td class="col-9">{{ date('d-m-Y', strtotime($penjualan->penjualan_tanggal)) }}</td>
                         </tr>
                         <tr>
                             <th class="text-right col-3">Total Penjualan :</th>
                             <td class="col-9">Rp {{ number_format($penjualan->getTotalAmount(), 0, ',', '.') }}</td>
                         </tr>
                     </table>
 
                     <h5 class="mt-4">Detail Barang</h5>
                     <table class="table table-sm table-bordered table-striped">
                         <thead>
                             <tr>
                                 <th>No</th>
                                 <th>Barang</th>
                                 <th>Jumlah</th>
                                 <th>Harga</th>
                                 <th>Subtotal</th>
                             </tr>
                         </thead>
                         <tbody>
                             @foreach($penjualan->details as $index => $detail)
                             <tr>
                                 <td>{{ $index + 1 }}</td>
                                 <td>{{ $detail->barang->barang_kode }} - {{ $detail->barang->barang_nama }}</td>
                                 <td>{{ $detail->jumlah }}</td>
                                 <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                 <td>Rp {{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}</td>
                             </tr>
                             @endforeach
                         </tbody>
                     </table>
                 </div>
                 <div class="modal-footer">
                     <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                     <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                 </div>
             </div>
         </div>
     </form>
     <script>
         $(document).ready(function() {
             $("#form-delete").validate({
                 rules: {},
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
                                     $('#error-' + prefix).text(val[0]);
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