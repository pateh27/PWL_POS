@extends('layouts.template')
 
 @section('content')
     <div class="card card-outline card-primary">
         <div class="card-header">
             <h3 class="card-title">{{ $page->title }}</h3>
             <div class="card-tools">
                 <a class="btn btn-sm btn-warning mt-1" href="{{ url('/penjualan/export_pdf') }}"><i class="fa fa-file-pdf"></i> Export Penjualan</a>
                 <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Transaksi</button>
             </div>
         </div>
         <div class="card-body">
             @if (session('success'))
                 <div class="alert alert-success">{{ session('success') }}</div>
             @endif
             @if (session('error'))
                 <div class="alert alert-danger">{{ session('error') }}</div>
             @endif
             <div class="row">
                 <div class="col-md-12">
                     <div class="form-group-row">
                         <label class="col-1 control-label col-form-label">Filter:</label>
                         <div class="col-3">
                             <select class="form-control" id="user_id" name="user_id">
                                 <option value="">- Semua User -</option>
                                 @foreach($user as $item)
                                     <option value="{{ $item->user_id }}">{{ $item->nama }}</option>
                                 @endforeach
                             </select>
                             <small class="form-text text-muted">User</small>
                         </div>
                     </div>
                 </div>
             </div>
             <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                 <thead>
                     <tr>
                         <th>ID</th>
                         <th>Kode Transaksi</th>
                         <th>Pembeli</th>
                         <th>Kasir</th>
                         <th>Tanggal</th>
                         <th>Jumlah Item</th>
                         <th>Total</th>
                         <th>Aksi</th>
                     </tr>
                 </thead>
             </table>
         </div>
     </div>
     <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
 @endsection
 
 @push('css')
 @endpush
 
 @push('js')
     <script>
         function modalAction(url = '') {
             $('#myModal').load(url, function() {
                 $('#myModal').modal('show');
             });
         }
 
         var dataPenjualan;
         $(document).ready(function() {
             dataPenjualan = $('#table_penjualan').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax: {
                     "url": "{{ url('penjualan/list') }}",
                     "dataType": "json",
                     "type": "POST",
                     "data": function (d) {
                         d.user_id = $('#user_id').val();
                     }
                 },
                 columns: [
                 {
                     data: "DT_RowIndex",
                     className: "text-center",
                     orderable: false,
                     searchable: false
                 },
                 {
                     data: "penjualan_kode",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "pembeli",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "user.nama",
                     className: "",
                     orderable: false,
                     searchable: false
                 },
                 {
                     data: "penjualan_tanggal",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "total_items",
                     className: "text-right",
                     orderable: false,
                     searchable: false
                 },
                 {
                     data: "total_amount",
                     className: "text-right",
                     orderable: false,
                     searchable: false,
                     render: function(data, type, row) {
                         return formatRupiah(data);
                     }
                 },
                 {
                     data: "aksi",
                     className: "",
                     orderable: false,
                     searchable: false
                 }]
             });
 
             $('#user_id').on('change', function() {
                 dataPenjualan.ajax.reload();
             });
 
             // untuk Format Rupiah
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
         });
     </script>
 @endpush