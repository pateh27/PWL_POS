@extends('layouts.template')
 
 @section('content')
     <div class="card card-outline card-primary">
         <div class="card-header">
             <h3 class="card-title">{{ $page->title }}</h3>
             <div class="card-tools">
                <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-info btn-sm btn-primary mt-1">Import level</button>
                <a href="{{ url('/level/export_excel') }}" class="btn btn-primary btn-sm mt-1"><i class="fa fa-fileexcel"></i> Export level</a>
                <a href="{{ url('/level/export_pdf') }}" class="btn btn-warning btn-sm btn-primary mt-1"><i class="fa fa-filepdf"></i> Export level Pdf</a>
                <a href="{{ url('/level/create') }}" class="btn btn-primary btn-sm mt-1">Tambah Data</a>
                <button onclick="modalAction('{{ url('/level/create_ajax') }}')" class="btn btn-success btn-sm btn-primary mt-1">Tambah Data (Ajax)</button>
             </div>
         </div>
         <div class="card-body">
             @if (session('success'))
                 <div class="alert alert-success">{{ session('success') }}</div>
             @endif
             @if (session('error'))
                 <div class="alert alert-danger">{{ session('error') }}</div>
             @endif
             <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
                 <thead>
                     <tr>
                         <th>ID</th>
                         <th>Level Kode</th>
                         <th>Level Nama</th>
                         <th>Aksi</th>
                     </tr>
                 </thead>
             </table>
         </div>
         <div id="myModal" class="modal fade animate shake" tabindex="-1" 
         role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
         </div>
     </div>
 @endsection
 
 @push('css')
 @endpush
 
 @push('js')
     <script>
         function modalAction(url='') {
             $('#myModal').load(url, function(){
                $('#myModal').modal('show');
             });
         }
         
         var dataLevel;
         $(document).ready(function() {
                 dataLevel = $('#table_level').DataTable({
                 serverSide: true,
                 ajax: {
                     "url": "{{ url('level/list') }}",
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
                     data: "level_kode",
                     className: "",
                     orderable: true,
                     searchable: true
                 },
                 {
                     data: "level_nama",
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
             $('#table-level_filter input').unbind().bind().on('keyup', function(e) {
                 if (e.keyCode == 13) { // enter key
                     tableLevel.search(this.value).draw();
                 }
             });
             $('.filter_level').change(function() {
                 tableKategori.draw();
             });
         });
     </script>
 @endpush