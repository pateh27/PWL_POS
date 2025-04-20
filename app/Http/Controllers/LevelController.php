<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LevelModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yajra\DataTables\Facades\DataTables;


class LevelController extends Controller
{
    public function index() {
        $level = LevelModel::all();
       
        $breadcrumb = (Object) [    
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object) [
            'title' => 'Daftar Level user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';
        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request) {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama');

        if ($request->level_id) {
            $level->where('level_id', $request->level_id);
        }
    
        return DataTables::of($level)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                // $btn = '<a href="'.url('/level/' .$level->level_id).'" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="'.url('/level/' .$level->level_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/level/'.$level->level_id).'">'
                //      . csrf_field() . method_field('DELETE')
                //      . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';
                
                $btn = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    
    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list'  => ['Home', 'Level', 'Tambah'] 
        ];

        $page = (object) [
            'title' => 'Tambah Level baru'
        ];

        $activeMenu = 'level';

        return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request) {
        $request->validate([
            'level_kode'  => 'required|string|max:3',
            'level_nama'  => 'required|string',
        ]);

        LevelModel::create([
            'level_kode'  => $request->level_kode,
            'level_nama'  => $request->level_nama,
        ]);

        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }

    public function create_ajax() {
        $level = LevelModel::all();

        return view('level.create_ajax', ['level' => $level]);
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|max:3|regex:/^[A-Z]+$/',
                'level_nama' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false, 
                    'message'  =>'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            LevelModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data Level berhasil disimpan'
            ]);
        }
        redirect('/');
    }
    public function show(string $id) {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail Level',
            'list'  => ['Home', 'Level', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail level'
        ];
        
        $activeMenu = 'level';

        return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function show_ajax(string $id) {
        $level = LevelModel::find($id);

        return view('level.show_ajax', ['level' => $level]);
    }

    public function edit_ajax(string $id) {
        $level = LevelModel::find($id);

        return view('level.edit_ajax', ['level' => $level]); 
    }

    public function edit(string $id) {
        $level = LevelModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Edit Level',
            'list'  => ['Home', 'Level', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit level'
        ];

        $activeMenu = 'level';

        return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id) {

        $request->validate([
            'level_kode'    => 'required|max:3|unique:m_level,level_kode',
            'level_nama'    => 'required|string'
        ]);

        LevelModel::find($id)->update([
            'level_kode'    => $request->level_kode,
            'level_nama'    => $request->level_nama
        ]);

        return redirect('/level')->with('succes', 'Data level berhasil diubah');
    }

    public function update_ajax(Request $request, string $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|max:3|regex:/^[A-Z]+$/',
                'level_nama' => 'required|string|min:3|max:50|regex:/^[a-zA-Z\s]+$/'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = LevelModel::find($id);
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
        $check = LevelModel::find($id);
        if (!$check) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            LevelModel::destroy($id);

            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function confirm_ajax(string $id) {
        $level = LevelModel::find($id);

        return view('level.confirm_ajax', ['level' => $level]);
    }

    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);
            if (!$level) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
    
            try {
                $level->delete();
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
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_level');
    
            Log::info('Debug Upload', [
                'isUploaded' => $request->hasFile('file_level'),
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
                                'level_kode'  => $value['A'],
                                'level_nama'  => $value['B'],
                                'created_at'   => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        $insertResult = LevelModel::insertOrIgnore($insert);
    
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
         // ambil data level yang akan di export
         $level = LevelModel::select('level_kode', 'level_nama',)
             ->get();
 
         // load library excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif
 
         $sheet->setCellValue('A1', 'No');
         $sheet->setCellValue('B1', 'Kode Level');
         $sheet->setCellValue('C1', 'Nama Level');
 
 
         $sheet->getStyle('A1:C1')->getFont()->setBold(true); // bold header
 
         $no = 1; // nomor data dimulai dari 1
         $baris = 2; // baris data dimulai dari baris ke 2
 
         foreach ($level as $key => $value) {
             $sheet->setCellValue('A' . $baris, $no);
             $sheet->setCellValue('B' . $baris, $value->level_kode);
             $sheet->setCellValue('C' . $baris, $value->level_nama);
 
             $baris++;
             $no++;
         }
 
         foreach (range('A', 'F') as $columnID) {
             $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
         }
 
         $sheet->setTitle('Data Level'); // set title sheet
 
         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
         $filename = 'Data Level ' . date('Y-m-d H:i:s') . '.xlsx';
 
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
        $level = levelModel::select('level_kode', 'level_nama')
            ->get();
 
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
 
        return $pdf->stream('Data Level ' .date('Y-m-d H:i:s').'.pdf');
     }
}