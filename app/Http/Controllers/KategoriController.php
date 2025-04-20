<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class KategoriController extends Controller
{
    public function index() {        
        $kategori = KategoriModel::all();
 
         $breadcrumb = (object) [
             'title' => 'Daftar Kategori',
             'list'  => ['Home', 'Kategori']
         ];
 
         $page = (object) [
             'title' => 'Daftar Kategori yang terdaftar dalam sistem'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }
 
     public function list(Request $request)
     {
         $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');
 
         if ($request->kategori_id) {
             $kategori->where('kategori_id', $request->kategori_id);
         }
 
         return DataTables::of($kategori)
             ->addIndexColumn()
             ->addColumn('aksi', function ($kategori) {
                //  $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                //  $btn .= '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                //  $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
                //      . csrf_field() . method_field('DELETE')
                //      . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';
                $btn = '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                    return $btn;
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
 
     public function create()
     {
         $breadcrumb = (object) [
             'title' => 'Tambah kategori',
             'list'  => ['Home', 'kategori', 'Tambah']
         ];
 
         $page = (object) [
             'title' => 'Tambah kategori baru'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
     }
 
     public function store(Request $request)
     {
         $request->validate([
             'kategori_kode'  => 'required|string',
             'kategori_nama'  => 'required|string',
         ]);
 
         KategoriModel::create([
             'kategori_kode'  => $request->kategori_kode,
             'kategori_nama'  => $request->kategori_nama,
         ]);
 
         return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
     }
 
     public function create_ajax() {
        $kategori = KategoriModel::all();

        return view('kategori.create_ajax', ['kategori' => $kategori]);
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:6|regex:/^[A-Z0-9]+$/',
                'kategori_nama' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false, 
                    'message'  =>'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            KategoriModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data Kategori berhasil disimpan'
            ]);
        }
        redirect('/');
    }
     public function show(string $id)
     {
         $kategori = KategoriModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Detail kategori',
             'list'  => ['Home', 'kategori', 'Detail']
         ];
 
         $page = (object) [
             'title' => 'Detail kategori'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function show_ajax(string $id) {
        $kategori = KategoriModel::find($id);

        return view('kategori.show_ajax', ['kategori' => $kategori]);
     }
 
     public function edit(string $id)
     {
         $kategori = KategoriModel::find($id);
 
         $breadcrumb = (object) [
             'title' => 'Edit kategori',
             'list'  => ['Home', 'kategori', 'Edit']
         ];
 
         $page = (object) [
             'title' => 'Edit kategori'
         ];
 
         $activeMenu = 'kategori';
 
         return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
     }

     public function edit_ajax(string $id) {
        $kategori = KategoriModel::find($id);

        return view('kategori.edit_ajax', ['kategori' => $kategori]); 
    }

    public function update_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'kategori_kode' => 'required|string|max:6|regex:/^[A-Z0-9]+$/',
                'kategori_nama' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = kategoriModel::find($id);
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

     public function update(Request $request, string $id)
     {
 
         $request->validate([
             'kategori_kode'    => 'required|string|max:3|unique:m_kategori,kategori_kode',
             'kategori_nama'    => 'required|string',
         ]);
 
         KategoriModel::find($id)->update([
             'kategori_kode'    => $request->kategori_kode,
             'kategori_nama'    => $request->kategori_nama
         ]);
 
         return redirect('/kategori')->with('succes', 'Data kategori berhasil diubah');
     }
 
     public function destroy(string $id)
     {
         $check = KategoriModel::find($id);
         if (!$check) {
             return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
         }
 
         try {
             KategoriModel::destroy($id);
 
             return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
         } catch (\Illuminate\Database\QueryException $e) {
             return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
         }
     }

     public function confirm_ajax(string $id) {
        $kategori = KategoriModel::find($id);

        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = KategoriModel::find($id);
            if (!$kategori) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
    
            try {
                $kategori->delete();
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
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_kategori');
    
            Log::info('Debug Upload', [
                'isUploaded' => $request->hasFile('file_kategori'),
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
                                'kategori_kode'  => $value['A'],
                                'kategori_nama'  => $value['B'],
                                'created_at'   => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        $insertResult = KategoriModel::insertOrIgnore($insert);
    
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
         // ambil data kkategori yang akan di export
         $kategori = KategoriModel::select('kategori_id', 'kategori_nama',)
             ->get();
 
         // load library excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Kategori ID');
         $sheet->setCellValue('C1', 'Nama Kategori');
 
 
         $sheet->getStyle('A1:C1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
 
         foreach ($kategori as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->kategori_id);
             $sheet->setCellValue('C' . $baris, $value->kategori_nama);
 
             $baris++;
             $no++;
         }
 
         foreach (range('A', 'F') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Kategori'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Kategori ' . date('Y-m-d H:i:s') . '.xlsx';
 
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
 }