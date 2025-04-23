<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\BarangModel;
 use App\Models\PenjualanModel;
 use App\Models\PenjualanDetailModel;
 use App\Models\UserModel;
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Validator;
 use PhpOffice\PhpSpreadsheet\IOFactory;
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Shared\Date;
 use Barryvdh\DomPDF\Facade\Pdf;
 use Yajra\DataTables\Facades\DataTables;
 
 class PenjualanController extends Controller
 {
     public function index()
     {
         $breadcrumb = (object) [
             'title' => 'Daftar Penjualan',
             'list'  => ['Home', 'Penjualan']
         ];
 
         $page = (object) [
             'title' => 'Daftar Transaksi Penjualan yang sudah terdaftar dalam sistem'
         ];
 
         $activeMenu = 'penjualan';
         
         $user = UserModel::all();
 
         return view('penjualan.index', [
             'breadcrumb' => $breadcrumb, 
             'page' => $page, 
             'user' => $user, 
             'activeMenu' => $activeMenu
         ]);
     }
 
     public function list(Request $request)
     {
         $penjualan = PenjualanModel::select(
                 'penjualan_id', 
                 'user_id', 
                 'pembeli', 
                 'penjualan_kode', 
                 'penjualan_tanggal'
             )
             ->with('user')
             ->with('details');
 
         if ($request->user_id) {
             $penjualan->where('user_id', $request->user_id);
         }
 
         if ($request->start_date && $request->end_date) {
             $penjualan->whereBetween('penjualan_tanggal', [$request->start_date, $request->end_date]);
         }
 
         return DataTables::of($penjualan)
             ->addIndexColumn()
             ->addColumn('total_items', function ($penjualan) {
                 return $penjualan->details->sum('jumlah');
             })
             ->addColumn('total_amount', function ($penjualan) {
                 return $penjualan->getTotalAmount();
             })
             ->addColumn('aksi', function ($penjualan) {
                 $btn = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                 $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                 $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                 return $btn;
             })
             ->rawColumns(['aksi'])
             ->make(true);
     }
 
     public function create_ajax()
     {
         $barang = BarangModel::select('barang_id', 'barang_nama', 'barang_kode', 'harga_jual', 'harga_beli')
             ->get();
         $user = UserModel::select('user_id', 'nama')->get();
         $kode = PenjualanModel::generateKode();
 
         return view('penjualan.create_ajax')
                     ->with('barang', $barang)
                     ->with('user', $user)
                     ->with('kode', $kode);
     }
 
     public function store_ajax(Request $request)
     {
         if ($request->ajax() || $request->wantsJson()) {
             $rules = [
                 'user_id' => 'required|integer',
                 'pembeli' => 'required|string|max:50',
                 'penjualan_kode' => 'required|string|max:20|unique:t_penjualan,penjualan_kode',
                 'penjualan_tanggal' => 'required|date',
                 'items' => 'required|array|min:1',
                 'items.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
                 'items.*.jumlah' => 'required|integer|min:1',
                 'items.*.harga' => 'required|integer|min:0',
             ];
     
             $validator = Validator::make($request->all(), $rules);
     
             if ($validator->fails()) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Validasi Gagal',
                     'msgField' => $validator->errors(),
                 ]);
             }
     
             DB::beginTransaction();
             try {
                 $penjualan = PenjualanModel::create([
                     'user_id' => $request->user_id,
                     'pembeli' => $request->pembeli,
                     'penjualan_kode' => $request->penjualan_kode,
                     'penjualan_tanggal' => $request->penjualan_tanggal,
                     'created_at' => now(),
                     'updated_at' => now()
                 ]);
                 foreach ($request->items as $item) {
                     PenjualanDetailModel::create([
                         'penjualan_id' => $penjualan->penjualan_id,
                         'barang_id' => $item['barang_id'],
                         'jumlah' => $item['jumlah'],
                         'harga' => $item['harga'],
                         'created_at' => now(),
                         'updated_at' => now()
                     ]);
                 }
     
                 DB::commit();
                 return response()->json([
                     'status' => true,
                     'message' => 'Transaksi penjualan berhasil disimpan'
                 ]);
             } catch (\Exception $e) {
                 DB::rollBack();
                 return response()->json([
                     'status' => false,
                     'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                 ]);
             }
         }
         return redirect('/');
     }
     
 
     public function show_ajax(string $id)
     {
         $penjualan = PenjualanModel::with(['user', 'details.barang'])->find($id);
         
         if (!$penjualan) {
             return response()->json([
                 'status' => false,
                 'message' => 'Data tidak ditemukan'
             ]);
         }
 
         $total = $penjualan->getTotalAmount();
 
         return view('penjualan.show_ajax', [
             'penjualan' => $penjualan,
             'total' => $total
         ]);
     }
 
     public function edit_ajax(string $id)
 {
     $penjualan = PenjualanModel::with(['details.barang'])->find($id);
     
     if (!$penjualan) {
         return response()->json([
             'status' => false,
             'message' => 'Data tidak ditemukan'
         ]);
     }
 
     $barang = BarangModel::select('barang_id', 'barang_nama', 'barang_kode', 'harga_jual', 'harga_beli')
         ->get();
     $user = UserModel::select('user_id', 'nama')->get();
 
     return view('penjualan.edit_ajax', [
         'penjualan' => $penjualan,
         'barang' => $barang,
         'user' => $user
     ]);
 }
 
 public function update_ajax(Request $request, $id)
 {
     if ($request->ajax() || $request->wantsJson()) {
         $rules = [
             'user_id' => 'required|integer',
             'pembeli' => 'required|string|max:50',
             'penjualan_tanggal' => 'required|date',
             'items' => 'required|array|min:1',
             'items.*.barang_id' => 'required|integer|exists:m_barang,barang_id',
             'items.*.jumlah' => 'required|integer|min:1',
             'items.*.harga' => 'required|integer|min:0',
         ];
 
         $validator = Validator::make($request->all(), $rules);
 
         if ($validator->fails()) {
             return response()->json([
                 'status' => false,
                 'message' => 'Validasi Gagal',
                 'msgField' => $validator->errors(),
             ]);
         }
 
         DB::beginTransaction();
         try {
             $penjualan = PenjualanModel::find($id);
 
             if (!$penjualan) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data penjualan tidak ditemukan',
                 ]);
             }
 
             // Step 1: Update penjualan header
             $penjualan->update([
                 'user_id' => $request->user_id,
                 'pembeli' => $request->pembeli,
                 'penjualan_tanggal' => $request->penjualan_tanggal,
                 'updated_at' => now()
             ]);
 
             // Step 2: Hapus detail lama
             PenjualanDetailModel::where('penjualan_id', $id)->delete();
 
             // Step 3: Tambahkan detail baru 
             foreach ($request->items as $item) {
                 PenjualanDetailModel::create([
                     'penjualan_id' => $penjualan->penjualan_id,
                     'barang_id' => $item['barang_id'],
                     'jumlah' => $item['jumlah'],
                     'harga' => $item['harga'],
                     'created_at' => now(),
                     'updated_at' => now()
                 ]);
             }
             
             DB::commit();
             return response()->json([
                 'status' => true,
                 'message' => 'Data penjualan berhasil diperbarui'
             ]);
         } catch (\Exception $e) {
             DB::rollBack();
             return response()->json([
                 'status' => false,
                 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
             ]);
         }
     }
     return redirect('/');
 }
 
 public function confirm_ajax(string $id)
 {
     $penjualan = PenjualanModel::with(['details.barang', 'user'])->find($id);
     
     if (!$penjualan) {
         return response()->json([
             'status' => false,
             'message' => 'Data tidak ditemukan'
         ]);
     }
 
     return view('penjualan.confirm_ajax', [
         'penjualan' => $penjualan
     ]);
 }
 
 public function delete_ajax(Request $request, $id)
 {
     if ($request->ajax() || $request->wantsJson()) {
         DB::beginTransaction();
         try {
             $penjualan = PenjualanModel::find($id);
 
             if (!$penjualan) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Data penjualan tidak ditemukan',
                 ]);
             }
 
             // Step 1: Delete detail records first
             PenjualanDetailModel::where('penjualan_id', $id)->delete();
             
             // Step 2: Delete the main penjualan record
             $penjualan->delete();
             
             DB::commit();
             return response()->json([
                 'status' => true,
                 'message' => 'Data penjualan berhasil dihapus'
             ]);
         } catch (\Exception $e) {
             DB::rollBack();
             return response()->json([
                 'status' => false,
                 'message' => 'Terjadi kesalahan: ' . $e->getMessage()
             ]);
         }
     }
     return redirect('/');
 }
    public function export_pdf() 
    {
        $penjualan = PenjualanModel::with(['user', 'details.barang'])
        ->orderBy('penjualan_tanggal', 'desc')
        ->get();

        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
 
        return $pdf->stream('Data Penjualan ' .date('Y-m-d H:i:s').'.pdf');
     }
}   