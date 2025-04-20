<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    public function index(){
        $breadcrumb =(object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];

        $page = (object) [
            'title' => 'Daftar User yang terdaftar dalam sistem',  
        ];

        $activeMenu = 'user';
        
        $level = LevelModel::all();
 
        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function list(Request $request) {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
            ->with('level');
    
            if ($request->level_id) {
                $users->where('level_id', $request->level_id);
            }
    

            return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('aksi', function ($user) {
                $btn = '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    //Menyimpan data user baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer'
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id
        ]);

        return redirect('/user') ->with('succes', 'Data user berhasil disimpan');
    }

    public function show(string $id)
    {
        $user = UserModel::with('level')->find($id);
        $breadcrumb = (object) [
            'title' => 'Detail User',
            'list' => ['Home', 'User', 'Detail User']
        ];

        $page = (object) [
            'title' => 'Detail User',  
        ];

        $activeMenu = 'user';
        return view('user.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object) [
            'title' => 'Ubah User',
            'list' => ['Home', 'User', 'Edit']
        ];
        $user->save();
        $page = (object) [
            'title' => 'Edit User',  
        ];

        $activeMenu = 'user';

        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
           //username harus diisi, berupa string, dan minimal 3 karakter
           //dan bernilai unik di table m_user
           'username' => 'required|string|min:3|unique:m_user,username,'.$id. 'user_id',
           'nama' => 'required|string|max:100',
           'password' => 'nullable|min:5',
           'level_id' => 'required|integer' 
        ]);

        UserModel::find($id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id' => $request->level_id
        ]);

        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    public function destroy(string $id)
    {
        $check = UserModel::find($id);
        if (!$check) {
            return redirect('/user')->with('error', 'Data user berhasil dihapus');
        }

        try{
            UserModel::destroy($id);
            return redirect('/user')->with('succes', 'Data user berhasil dihapus');
        }catch (\Illuminate\Database\QueryException $e){
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $level  = levelModel::select('level_id', 'level_nama')->get();
        return view('user.create_ajax') ->with('level', $level);
    }

    public function show_ajax(string $id) {
        $user = UserModel::find($id);

        return view('user.show_ajax', ['user' => $user]);
    }

    public function store_ajax(Request $request) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:6'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false, 
                    'message'  =>'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            UserModel::create($request->all()); 
            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil disimpan'
            ]);
        }
        redirect('/');
    }
    
    public function edit_ajax(string $id) {
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.edit_ajax', ['user' => $user, 'level' => $level]); 
    }

    public function update_ajax(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|max:20|unique:m_user,username,'.$id.',user_id',
                'nama'     => 'required|max:100',
                'password' => 'nullable|min:6|max:20'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = UserModel::find($id);
            if ($check) {
                if ($request->filled('password')) {
                    $request->request->remove('password');
                }

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
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id) {
        //cek apakah request dari ajax

        if ($request->ajax() || $request->wantsJson()) {
            $user = UserModel::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
    
            try {
                $user->delete();
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
        return view('user.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_user' => ['required', 'mimes:xlsx', 'max:1024']
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $file = $request->file('file_user');
    
            Log::info('Debug Upload', [
                'isUploaded' => $request->hasFile('file_user'),
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
                                'level_id'  => $value['A'],
                                'username'  => $value['B'],
                                'nama'  => $value['C'],
                                'password' => bcrypt($value['D']), 
                                'created_at'   => now(),
                            ];
                        }
                    }
    
                    if (count($insert) > 0) {
                        $insertResult = UserModel::insertOrIgnore($insert);
    
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
}