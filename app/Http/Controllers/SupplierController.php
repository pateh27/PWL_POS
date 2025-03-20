<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\SupplierModel;
 use Illuminate\Http\Request;
 use Yajra\DataTables\Facades\DataTables;
 class SupplierController extends Controller
 {
     public function index() {
         $supplier = SupplierModel::all();
        
         $breadcrumb = (Object) [    
             'title' => 'Daftar supplier',
             'list'  => ['Home', 'supplier']
         ];
 
         $page = (object) [
             'title' => 'Daftar supplier yang terdaftar dalam sistem'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request) {
         $supplier = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
 
         if ($request->supplier_id) {
             $supplier->where('supplier_id', $request->supplier_id);
         }
     
         return DataTables::of($supplier)
             ->addIndexColumn()
             ->addColumn('aksi', function ($supplier) {
                 $btn = '<a href="'.url('/supplier/' .$supplier->supplier_id).'" class="btn btn-info btn-sm">Detail</a> ';
                 $btn .= '<a href="'.url('/supplier/' .$supplier->supplier_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                 $btn .= '<form class="d-inline-block" method="POST" action="'.url('/supplier/'.$supplier->supplier_id).'">'
                      . csrf_field() . method_field('DELETE')
                      . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';
                 return $btn; 
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
     
     public function create() {
         $breadcrumb = (object) [
             'title' => 'Tambah supplier',
             'list'  => ['Home', 'supplier', 'Tambah'] 
         ];
 
         $page = (object) [
             'title' => 'Tambah supplier baru'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
     }
 
     public function store(Request $request) {
         $request->validate([
             'supplier_kode'  => 'required|string',
             'supplier_nama'  => 'required|string',
             'supplier_alamat'=> 'required|string',
         ]);
 
         SupplierModel::create([
             'supplier_kode'  => $request->supplier_kode,
             'supplier_nama'  => $request->supplier_nama,
             'supplier_alamat'=> $request->supplier_alamat,
         ]);
 
         return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
     }
 
     public function show(string $id) {
         $supplier = SupplierModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail supplier',
             'list'  => ['Home', 'supplier', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail supplier'
         ];
         
         $activeMenu = 'supplier';
 
         return view('supplier.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function edit(string $id) {
         $supplier = SupplierModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Edit supplier',
             'list'  => ['Home', 'supplier', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit supplier'
         ];
 
         $activeMenu = 'supplier';
 
         return view('supplier.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function update(Request $request, string $id) {
 
         $request->validate([
             'supplier_kode'    => 'required|unique:m_supplier,supplier_kode',
             'supplier_nama'    => 'required|string',
             'supplier_alamat'  => 'required|string',
         ]);
 
         SupplierModel::find($id)->update([
             'supplier_kode'    => $request->supplier_kode,
             'supplier_nama'    => $request->supplier_nama,
             'supplier_alamat'  => $request->supplier_alamat
         ]);
 
         return redirect('/supplier')->with('succes', 'Data supplier berhasil diubah');
     }
 
     public function destroy(string $id) {
         $check = SupplierModel::find($id);
         if (!$check) {
             return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
         }
 
         try {
             SupplierModel::destroy($id);
 
             return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }
 }