@extends('layouts.template')
 
 @section('content')
     <div class="card card-outline card-primary">
         <div class="card-header">
             <h3 class="card-title">{{ $page->title }}</h3>
             <div class="card-tools">
                <button onclick="modalAction('{{ url('/supplier/import') }}')" class="btn btn-info btn-sm btn-primary mt-1">Import supplier</button>
                <a href="{{ url('/supplier/export_excel') }}" class="btn btn-primary btn-sm mt-1"><i class="fa fa-fileexcel"></i> Export supplier</a>
                <a href="{{ url('/supplier/export_pdf') }}" class="btn btn-warning btn-sm btn-primary mt-1"><i class="fa fa-filepdf"></i> Export supplier Pdf</a>
                <a href="{{ url('/supplier/create') }}" class="btn btn-primary btn-sm mt-1">Tambah Data</a>
                <button onclick="modalAction('{{ url('/supplier/create_ajax') }}')" class="btn btn-success btn-sm btn-primary mt-1">Tambah Data (Ajax)</button>
             </div>
         </div>
         <div class="card-body">
             @if (session('success'))
                 <div class="alert alert-success">{{ session('success') }}</div>
             @endif
             @if (session('error'))
                 <div class="alert alert-danger">{{ session('error') }}</div>
             @endif
             <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
                 <thead>
                     <tr>
                         <th>ID</th>
                         <th>Supplier Kode</th>
                         <th>Supplier Nama</th>
                         <th>Supplier Alamat</th>
                         <th>Aksi</th>
                     </tr>
                 </thead>
             </table>
         </div>
     </div>
     <div id="myModal" class="modal fade animate shake" tabindex="-1" 
     role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
     </div>
 @endsection
 
 @push('css')
 @endpush
 
 @push('js')
     <script>
        function modalAction(url='') {
            $('#myModal').load(url, function() {
                 $('#myModal').modal('show');
             }) 
        }
         $(document).ready(function() {
                 dataSupplier = $('#table_supplier').DataTable({
                 serverSide: true,
                 ajax: {
                     "url": "{{ url('supplier/list') }}",
                     "dataType": "json",
                     "type": "POST"
                 },
                 columns: [
                 {
                     data: "DT_RowIndex",
                     className: "text-center",
                     orderable: false,
                     searchable: false
                 },
                 {
                     data: "supplier_kode",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "supplier_nama",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "supplier_alamat",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "aksi",
                     className: "",
                     orderable: false,
                     searchable: false
                 }]
             });
             $('#table-supplier_filter input').unbind().bind().on('keyup', function(e) {
                 if (e.keyCode == 13) { // enter key
                     tableSupplier.search(this.value).draw();
                 }
             });
             $('.filter_level').change(function() {
                 tableSupplier.draw();
             });
         });
     </script>
 @endpush