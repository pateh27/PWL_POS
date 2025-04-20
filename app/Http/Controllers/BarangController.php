<?php
 
namespace App\Http\Controllers;
 
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;
 
 class BarangController extends Controller
 {
     public function index() {
         $breadcrumb = (object) [
             'title' => 'Daftar Barang',
             'list'  => ['Home', 'Barang']
         ];
 
         $page = (object) [
             'title' => 'Daftar barang yang terdaftar dalam sistem'
         ];
 
         $activeMenu = 'barang';
 
         $kategori = KategoriModel::all();
 
         return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request) {
         $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual', 'kategori_id')
             ->with('kategori');
 
         if ($request->kategori_id) {
             $barang->where('kategori_id', $request->kategori_id);
         }
     
         return DataTables::of($barang)
             ->addIndexColumn()
             ->addColumn('aksi', function ($barang) {
                //  $btn = '<a href="'.url('/barang/' .$barang->barang_id).'" class="btn btn-info btn-sm">Detail</a> ';
                //  $btn .= '<a href="'.url('/barang/' .$barang->barang_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                //  $btn .= '<form class="d-inline-block" method="POST" action="'.url('/barang/'.$barang->barang_id).'">'
                //       . csrf_field() . method_field('DELETE')
                //       . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn = '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                 return $btn; 
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
     
 
     public function create() {
         $breadcrumb = (object) [
             'title' => 'Tambah Barang',
             'list'  => ['Home', 'Barang', 'Tambah'] 
         ];
 
         $page = (object) [
             'title' => 'Tambah Barang baru'
         ];
 
         $kategori = KategoriModel::all();
         $activeMenu = 'barang';
 
         return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function create_ajax() {
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.create_ajax')
                    ->with('kategori', $kategori);
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|max:6',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|integer',
                'harga_jual' => 'required|integer|gte:harga_beli',
                'kategori_id' => 'required|exists:m_kategori,kategori_id'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false, 
                    'message'  =>'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            BarangModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan'
            ]);
        }
        redirect('/');
    }
 
     public function store(Request $request) {
         $request->validate([
             'barang_kode'   => 'required|string|min:3|unique:m_barang,barang_kode',
             'barang_nama'   => 'required|string|max:100',
             'harga_beli'    => 'required|integer',
             'harga_jual'    => 'required|integer',
             'kategori_id'   => 'required|integer'
         ]);
 
         BarangModel::create([
             'barang_kode'   => $request->barang_kode,
             'barang_nama'   => $request->barang_nama,
             'harga_beli'    => $request->harga_beli,
             'harga_jual'    => $request->harga_jual,
             'kategori_id'   => $request->kategori_id
         ]);
 
         return redirect('/barang')->with('success', 'Data barang berhasil disimpan');
     }
 
     public function show(string $id) {
         $barang = BarangModel::with('kategori')->find($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail Barang',
             'list'  => ['Home', 'Barang', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail Barang'
         ];
         
         $activeMenu = 'barang';
 
         return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
     }

     public function show_ajax(string $id) {
        $barang = BarangModel::find($id);

        return view('barang.show_ajax', ['barang' => $barang]);
    }
 
     public function edit(string $id) {
         $barang = BarangModel::find($id);
         $kategori = KategoriModel::all();
 
         $breadcrumb = (object) [
             'title' => 'Edit Barang',
             'list'  => ['Home', 'Barang', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit Barang'
         ];
 
         $activeMenu = 'barang';
 
         return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function edit_ajax(string $id) {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();

        return view('barang.edit_ajax', ['barang' => $barang, 'kategori' => $kategori]); 
    }

     public function update(Request $request, string $id) {
 
         $request->validate([
             'barang_kode'   => 'required|string|min:3|',
             'barang_nama'   => 'required|string|max:100',
             'harga_beli'    => 'required|integer',
             'harga_jual'    => 'required|integer',
             'kategori_id'   => 'required|integer'
         ]);
 
         BarangModel::find($id)->update([
             'barang_kode'   => $request->barang_kode,
             'barang_nama'   => $request->barang_nama,
             'harga_beli'    => $request->harga_beli,
             'harga_jual'    => $request->harga_jual,
             'kategori_id'   => $request->kategori_id
         ]);
 
         return redirect('/barang')->with('succes', 'Data barang berhasil diubah');
     }
 
     public function update_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|max:6',
                'barang_nama' => 'required|string|max:100',
                'harga_beli' => 'required|integer',
                'harga_jual' => 'required|integer|gte:harga_beli',
                'kategori_id' => 'required|exists:m_kategori,kategori_id'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = BarangModel::find($id);
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

     public function destroy(string $id) {
         $check = BarangModel::find($id);
         if (!$check) {
             return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
         }
 
         try {
             BarangModel::destroy($id);
 
             return redirect('/barang')->with('success', 'Data barang berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             return redirect('/barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }

     public function confirm_ajax(string $id) {
        $barang = BarangModel::find($id);

        return view('barang.confirm_ajax', ['barang' => $barang]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if (!$barang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
    
            try {
                $barang->delete();
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

    public function import() {
        return view('barang.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_barang');
    
            Log::info('Debug Upload', [
                'isUploaded' => $request->hasFile('file_barang'),
                'file' => $file,
                'real_path' => $file ? $file->getRealPath() : 'null'
            ]);
    
            try {
                // Gunakan getRealPath() untuk mendapatkan path lengkap ke file sementara
                $fullPath = $file->getRealPath();
                
                // Atau buat direktori custom dan pindahkan file
                $customDir = storage_path('app/temp');
                if (!file_exists($customDir)) {
                    mkdir($customDir, 0755, true);
                }
                
                $customFilePath = $customDir . '/' . $file->getClientOriginalName();
                // Salin file ke direktori custom
                copy($fullPath, $customFilePath);
                
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                // Gunakan path file kustom
                $spreadsheet = $reader->load($customFilePath);
                $sheet = $spreadsheet->getActiveSheet();
    
                $data = $sheet->toArray(null, false, true, true);
    
                // Hapus file temporary custom setelah selesai
                if (file_exists($customFilePath)) {
                    unlink($customFilePath);
                }
    
                $insert = [];
                
                if (count($data) > 1) {
                    foreach ($data as $baris => $value) {
                        if ($baris > 1) {
                            $insert[] = [
                                'kategori_id'  => $value['A'],
                                'barang_kode'  => $value['B'],
                                'barang_nama'  => $value['C'],
                                'harga_beli'   => $value['D'],
                                'harga_jual'   => $value['E'],
                                'created_at'   => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        $insertResult = BarangModel::insertOrIgnore($insert);
    
                        if (!$insertResult) {
                            Log::error('Gagal menyisipkan data ke database.', [
                                'data' => $insert,
                                'error' => 'Data tidak berhasil dimasukkan.'
                            ]);
    
                            return response()->json([
                                'status'  => false,
                                'message' => 'Gagal menyisipkan data ke database.'
                            ]);
                        }
                    }
    
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil diimpor'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tidak ada data yang diimpor'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error saat memproses file Excel.', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'status'  => false,
                    'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
                ]);
            }
        }
    
        return redirect('/');
     }

     public function export_excel()
     {
         // ambil data barang yang akan di export
         $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
             ->orderBy('kategori_id')
             ->with('kategori')
             ->get();
 
         // load library excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Kode Barang');
         $sheet->setCellValue('C1', 'Nama Barang');
         $sheet->setCellValue('D1', 'Harga Beli');
         $sheet->setCellValue('E1', 'Harga Jual');
         $sheet->setCellValue('F1', 'Kategori');
 
         $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
 
         foreach ($barang as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->barang_kode);
             $sheet->setCellValue('C' . $baris, $value->barang_nama);
             $sheet->setCellValue('D' . $baris, $value->harga_beli);
             $sheet->setCellValue('E' . $baris, $value->harga_jual);
             $sheet->setCellValue('F' . $baris, $value->kategori->kategori_nama); // ambil nama kategori
 
             $baris++;
             $no++;
         }
 
         foreach (range('A', 'F') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Barang'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Barang ' . date('Y-m-d H:i:s') . '.xlsx';
 
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
        $barang = BarangModel::select('kategori_id', 'barang_kode', 'barang_nama', 'harga_beli', 'harga_jual')
            ->orderBy('kategori_id')
            ->orderBy('barang_kode')
            ->with('kategori')
            ->get();

        $pdf = Pdf::loadView('barang.export_pdf', ['barang' => $barang]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data Barang ' .date('Y-m-d H:i:s').'.pdf');
    }
}