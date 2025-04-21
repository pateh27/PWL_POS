<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\UserModel;
 use App\Models\LevelModel;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Hash;
 use Illuminate\Support\Facades\Validator;
 
 class AuthController extends Controller
 {
     public function login()
     {
         if (Auth::check()) {
             return redirect('/');
         }
         return view('auth/login');
     }
 
     public function postLogin(Request $request)
     {
         if ($request->ajax() || $request->wantsJson()) {
             $credentials = $request->only('username', 'password');
 
             if (Auth::attempt($credentials)) {
                 return response()->json([
                     'status' => true,
                     'message' => 'Login Berhasil',
                     'redirect' => url('/')
                 ]);
             } 
             
             return response()->json([
                 'status' => false,
                 'message' => 'Login Gagal'
             ]);
         }
         return redirect('login');
     }
 
     public function logout(Request $request)
     {
         Auth::logout();
 
         $request->session()->invalidate();
         $request->session()->regenerateToken();
         return redirect('login');
     }

     public function register()
     {
         $levels = LevelModel::where('level_nama', '!=', 'Administrator')->get();
         return view('auth.register', compact('levels'));
     }
 
     public function postRegister(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'username' => 'required|unique:m_user,username|min:4|max:20',
             'nama' => 'required|string|max:100',
             'password' => 'required|min:6|confirmed',
             'level_id' => 'required|exists:m_level,level_id|not_in:' . 
             LevelModel::where('level_nama', 'Administrator')->value('level_id')
         ], [
             'level_id.not_in' => 'Pemilihan level admin tidak diperbolehkan.'
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'status' => false,
                 'message' => 'Registrasi Gagal',
                 'errors' => $validator->errors()
             ], 422);
         }
 
         try {
             UserModel::create([
                 'username' => $request->username,
                 'nama' => $request->nama,
                 'password' => Hash::make($request->password),
                 'level_id' => $request->level_id
             ]);
 
             return response()->json([
                 'status' => true,
                 'message' => 'Registrasi Berhasil',
                 'redirect' => url('login')
             ]);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => false,
                 'message' => 'Registrasi Gagal: ' . $e->getMessage()
             ], 500);
         }
     }

     public function profile()
     {
         $user = Auth::user();
         $activeMenu = 'profile';
 
         $breadcrumb = (object) [
             'title' => 'Profil Pengguna',
             'list'  => ['Home', 'Profil']
         ];
 
         return view('profile.index', compact('user', 'activeMenu', 'breadcrumb'))->with([
             'status' => true,
             'data' => $user
         ]);
     }
 
     public function update(Request $request)
     {
         $user = Auth::user();
 
         $rules = [
             'username' => 'required|string|min:3|unique:m_user,username,' . $user->user_id . ',user_id',
             'nama' => 'required|string|max:100',
             'password' => 'nullable|min:6|confirmed',
             'profile_picture' => 'nullable|mimes:jpeg,png,jpg|max:2048',
         ];
 
         $validator = Validator::make($request->all(), $rules);
 
         if ($validator->fails()) {
             return redirect()->back()
                 ->withErrors($validator)
                 ->withInput();
         }
 
         $user->username = $request->username;
         $user->nama = $request->nama;
 
         if ($request->filled('password')) {
             $user->password = bcrypt($request->password);
         }
 
         if ($request->hasFile('profile_picture')) {
             $file = $request->file('profile_picture');
             $filename = time() . '.' . $file->getClientOriginalExtension();
             $file->move(public_path('uploads/profile'), $filename);
             $user->profile_picture = $filename;
         }
 
         /** @var \App\Models\User $user **/
         $user->save();
 
         return redirect()->route('profile')
             ->with('success', 'Profil berhasil diperbarui');
     }
 }