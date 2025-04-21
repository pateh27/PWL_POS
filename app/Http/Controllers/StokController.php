<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\BarangModel;
 use App\Models\StokModel;
 use App\Models\SupplierModel;
 use App\Models\UserModel;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Validator;
 use PhpOffice\PhpSpreadsheet\IOFactory;
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Shared\Date;
 use Barryvdh\DomPDF\Facade\Pdf;
 use Yajra\DataTables\Facades\DataTables;
 
 class StokController extends Controller
 {
     public function index() {
         $breadcrumb = (object) [
             'title' => 'Daftar Stok',
             'list'  => ['Home', 'Stok']
         ];
 
         $page = (object) [
             'title' => 'Daftar Stok Barang yang sudah terdaftar dalam sistem'
         ];
 
         $activeMenu = 'stok';
 
         $supplier = SupplierModel::all();
         // $barang = BarangModel::all();
         // $user = UserModel::all();
 
         return view('stok.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'supplier' => $supplier, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request) {
         $stok = StokModel::select('stok_id', 'stok_tanggal', 'stok_jumlah', 'supplier_id', 'barang_id', 'user_id')
             ->with('supplier')
             ->with('barang')
             ->with('user');
 
         if ($request->supplier_id) {
             $stok->where('supplier_id', $request->supplier_id);
         }
 
         if ($request->barang_id) {
             $stok->where('barang_id', $request->barang_id);
         }
     
         if ($request->user_id) {
             $stok->where('user_id', $request->user_id);
         }
     
     
         return DataTables::of($stok)
             ->addIndexColumn()
             ->addColumn('aksi', function ($stok) {
                 $btn = '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                 $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                 $btn .= '<button onclick="modalAction(\''.url('/stok/' . $stok->stok_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                 return $btn; 
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
 
     public function create_ajax() {
         $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
         $barang = BarangModel::select('barang_id', 'barang_nama')->get();
         $user = UserModel::select('user_id', 'nama')->get();
 
         return view('stok.create_ajax')
                     ->with('supplier', $supplier)
                     ->with('barang', $barang)
                     ->with('user', $user);
 
     }
 
     public function store_ajax(Request $request) {
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'supplier_id' => 'required|integer',
                 'barang_id' => 'required|integer',
                 'user_id' => 'required|integer',
                 'stok_tanggal' => 'required',
                 'stok_jumlah'     => 'required|integer',
             ];
 
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status'   => false, 
                     'message'  =>'Validasi Gagal',
                     'msgField' => $validator->errors(),
                 ]);
             }
 
             StokModel::create($request->all());
             return response()->json([
                 'status' => true,
                 'message' => 'Data Stok berhasil disimpan'
             ]);
         }
         redirect('/');
     }
 
     public function show_ajax(string $id) {
         $stok = StokModel::find($id);
 
         return view('stok.show_ajax', ['stok' => $stok]);
     }
 
     public function edit_ajax(string $id) {
         $stok = StokModel::find($id);
         $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();
         $barang = BarangModel::select('barang_id', 'barang_nama')->get();
         $user = UserModel::select('user_id', 'nama')->get();
 
         return view('stok.edit_ajax', ['stok' => $stok, 'supplier' => $supplier, 'barang' => $barang, 'user' => $user]); 
     }
 
     public function update_ajax(Request $request, $id) {
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'supplier_id' => 'required|integer',
                 'barang_id' => 'required|integer',
                 'user_id' => 'required|integer',
                 'stok_tanggal' => 'required',
                 'stok_jumlah'     => 'required|integer',
             ];
 
             $validator = Validator::make($request->all(), $rules);
 
             if ($validator->fails()) {
                 return response()->json([
                     'status'   => false,
                     'message'  => 'Validasi gagal.',
                     'msgField' => $validator->errors()
                 ]);
             }
 
             $check = StokModel::find($id);
             if ($check) {
                 $check->update($request->all());
                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil diupdate'
                 ]);
             } else {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data tidak ditemukan'
                 ]);
             }
         }
         return redirect('/');
     }
 
     public function confirm_ajax(string $id) {
         $stok = StokModel::find($id);
 
         return view('stok.confirm_ajax', ['stok' => $stok]);
     }
 
     public function delete_ajax(Request $request, $id){
         if ($request->ajax() || $request->wantsJson()) {
             $stok = StokModel::find($id);
             if (!$stok) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data tidak ditemukan'
                 ]);
             }
     
             try {
                 $stok->delete();
                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil dihapus'
                 ]);
             } catch (\Illuminate\Database\QueryException $e) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini'
                 ]);
             }
         }
     
         return redirect('/');
     }
     
     public function import()
     {
         return view('stok.import');
     }
 
     public function import_ajax(Request $request)
     {
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
             ];
             $validator = Validator::make($request->all(), $rules);
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Validasi Gagal',
                     'msgField' => $validator->errors()
                 ]);
             }
             $file = $request->file('file_stok');
             $reader = IOFactory::createReader('Xlsx');
             $reader->setReadDataOnly(true);
             $spreadsheet = $reader->load($file->getRealPath());
             $sheet = $spreadsheet->getActiveSheet();
             $data = $sheet->toArray(null, false, true, true);
             $insert = [];
             if (count($data) > 1) {
                 foreach ($data as $baris => $value) {
                     if ($baris > 1) {
                         // Konversi tanggal
                         $tanggal_excel = $value['D'];
                         $tanggal = null;
                         if (is_numeric($tanggal_excel)) {
                             $tanggal = Date::excelToDateTimeObject($tanggal_excel)->format('Y-m-d H:i:s');
                         } else {
                             // fallback kalau ternyata berupa string
                             $tanggal = date('Y-m-d H:i:s', strtotime($tanggal_excel));
                         }
                 
                         $insert[] = [
                             'supplier_id' => $value['A'],
                             'barang_id' => $value['B'],
                             'user_id' => $value['C'],
                             'stok_tanggal' => $tanggal,
                             'stok_jumlah' => $value['E'],
                             'created_at' => now(),
                         ];
                     }
                 }
                 if (count($insert) > 0) {
                     StokModel::insertOrIgnore($insert);
                 }
                 return response()->json([
                     'status' => true,
                     'message' => 'Data berhasil diimport'
                 ]);
             } else {
                 return response()->json([
                     'status' => false,
                     'message' => 'Tidak ada data yang diimport'
                 ]);
             }
         }
         return redirect('/');
     }
 
     public function export_excel()
     {
         // ambil data user yang akan di export
         $stok = StokModel::select('supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah',)
             ->orderBy('supplier_id')
             ->with('supplier')
             ->with('barang')
             ->with('user')
             ->get();
 
         // load library excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Barang');
         $sheet->setCellValue('C1', 'Jumlah Stok');
         $sheet->setCellValue('D1', 'Supplier');
         $sheet->setCellValue('E1', 'User');
         $sheet->setCellValue('F1', 'Tanggal Stok');
 
         $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
 
         foreach ($stok as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->barang->barang_nama);
             $sheet->setCellValue('C' . $baris, $value->stok_jumlah);
             $sheet->setCellValue('D' . $baris, $value->supplier->supplier_nama);
             $sheet->setCellValue('E' . $baris, $value->user->nama);
         
             // Format datetime ke format Excel + styling datetime
             $excelDateTime = Date::PHPToExcel(new \DateTime($value->stok_tanggal));
             $sheet->setCellValue('F' . $baris, $excelDateTime);
             $sheet->getStyle('F' . $baris)->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm:ss');
         
             $baris++;
             $no++;
         }
         
 
         foreach (range('A', 'F') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Stok Barang'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Stok ' . date('Y-m-d H:i:s') . '.xlsx';
 
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="' . $filename . '"');
         header('Cache-Control: max-age=0');
         header('Cache-Control: max-age=1');
         header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
         header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
         header('Cache-Control: cache, must-revalidate');
         header('Pragma: public');
 
         $writer->save('php://output');
         exit;
     }
 
     public function export_pdf()
     {
         $stok = StokModel::select('supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
             ->orderBy('supplier_id')
             ->with(['supplier'])
             ->with(['barang'])
             ->with(['user'])
             ->get();
 
         // use Barryvdh\DomPDF\Facade\Pdf;
         $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
         $pdf->setPaper('a4', 'portrait'); // set ukuran kertas dan orientasi
         $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
         $pdf->render();
 
         return $pdf->stream('Data Stok ' . date('Y-m-d H:i:s') . '.pdf');
     }
 }