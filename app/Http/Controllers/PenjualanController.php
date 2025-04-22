<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
            'title' => 'Daftar Penjualan yang terdaftar dalam sistem'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
        ->with('user')
        ->orderBy('created_at', 'desc');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp ' . number_format($penjualan->total_harga, 0, ',', '.');
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button>';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button>';
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberi tahu DataTables bahwa kolom 'aksi' adalah kolom HTML
            ->make(true);
    }

    public function show_ajax(string $id) 
    {
        $penjualan = PenjualanModel::with('user', 'penjualan_detail.barang')->find($id);

        if (!$penjualan) 
        {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        return view('penjualan.detail',
        ['penjualan' => $penjualan]);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list'  => ['Home', 'Penjualan', 'Tambah Penjualan']
        ];

        $page = (object) [
            'title' => 'Tambah Penjualan'
        ];

        $activeMenu = 'penjualan';

        $barang = BarangModel::select('barang_nama', 'harga_jual', 'barang_id')
        ->get()
        ->map(function ($barang) {
            return [
                'barang_id' => $barang->barang_id,
                'barang_nama' => $barang->barang_nama,
                'harga_jual' => $barang->harga_jual,
                'stock_available' => $barang->stock_available,
            ];
        });

    return view('penjualan.create', compact('breadcrumb', 'page', 'activeMenu'))->with([
        'barang' => $barang,
    ]);
    }

    public function store(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'pembeli' => 'required|string|max:255',
                'penjualan_tanggal' => 'required|date',
                'barang' => 'required|array|min:1',
                'barang.*.id' => 'required|exists:m_barang,barang_id', // typo: reqired → required
                'barang.*.quantity' => 'required|integer|min:1', // ubah nama field ke quantity sesuai yang digunakan dalam foreach
            ];
            
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }
            
            DB::beginTransaction();
            try {
                $penjualan = PenjualanModel::create([
                    'user_id' => auth()->user()->user_id,
                    'pembeli' => $request->pembeli,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                    'penjualan_kode' => 'P' . time() . strtoupper(str()->random(8)), // Perbaiki penulisan
                ]);
    
                $totalHarga = 0;
    
                foreach ($request->barang as $items) {
                    $barang = BarangModel::find($items['id']);
    
                    // cek stock barang
                    if ($barang->stock_available < $items['quantity']) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Stock barang ' . $barang->barang_nama . ' tidak cukup'
                        ]);
                    }
    
                    $itemHarga = $barang->harga_jual * $items['quantity'];
                    $totalHarga += $itemHarga;
    
                    $penjualan->penjualan_detail()->create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $items['id'],
                        'jumlah' => $items['quantity'],
                        'harga' => $itemHarga,
                    ]);
    
                    // Update stok barang
                    $barang->decrement('stock_available', $items['quantity']);
                }
    
                // Update total harga pada penjualan
                $penjualan->update(['total' => $totalHarga]);
    
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Penjualan berhasil disimpan',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Penjualan gagal disimpan',
                    'error' => $e->getMessage()
                ]);
            }
        }
    
        return redirect('/');
    }

    public function store_ajax(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan tidak valid.'
            ]);
        }
    
        $rules = [
            'pembeli' => 'required|string|max:255',
            'penjualan_tanggal' => 'required|date',
            'barang' => 'required|array|min:1',
            'barang.*.id' => 'required|exists:m_barang,barang_id',
            'barang.*.jumlah' => 'required|integer|min:1',
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ]);
        }
    
        DB::beginTransaction();
        try {
            // Membuat entri penjualan
            $penjualan = PenjualanModel::create([
                'user_id' => auth()->id(), // Menggunakan ID pengguna yang sedang login
                'pembeli' => $request->pembeli,
                'penjualan_tanggal' => $request->penjualan_tanggal,
                'penjualan_kode' => 'P' . time() . strtoupper(Str::random(8)),
            ]);
    
            $totalHarga = 0;
    
            foreach ($request->barang as $item) {
                $barang = BarangModel::find($item['id']);
    
                // Memeriksa ketersediaan stok
                if ($barang->stock_available < $item['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Stok tidak cukup untuk ' . $barang->barang_nama
                    ]);
                }
    
                $hargaItem = $barang->harga_jual * $item['jumlah'];
                $totalHarga += $hargaItem;
    
                // Mengurangi stok barang
                $barang->decrement('stock_available', $item['jumlah']);
    
                // Menyimpan detail penjualan
                $penjualan->penjualan_detail()->create([
                    'barang_id' => $item['id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $hargaItem,
                ]);
            }
    
            // Mengupdate total penjualan
            $penjualan->update(['total' => $totalHarga]);
    
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Penjualan berhasil disimpan',
                'data' => [
                    'penjualan_id' => $penjualan->penjualan_id,
                    'penjualan_kode' => $penjualan->penjualan_kode,
                    'total' => $totalHarga,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Penjualan gagal disimpan',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(string $id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit Penjualan',
            'list' => ['Home', 'Penjualan', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Penjualan',
        ];

        $activeMenu = 'penjualan';

        $penjualan = PenjualanModel::with('user', 'penjualan_detail')->find($id);

        if (!$penjualan) {
            return redirect('/penjualan');
        }

        $penjualanWithBarang = $penjualan->penjualan_detail->map(function ($item) {
            return [
                'barang_id' => $item->barang_id,
                'barang_nama' => $item->barang->barang_nama,
                'harga_jual' => $item->barang->harga_jual,
                'jumlah' => $item->jumlah,
                'stock_available' => $item->barang->stock_available,
                'total_harga' => $item->harga,
            ];
        });

        // dd($penjualanWithBarang);

        $barang = BarangModel::select('barang_nama', 'harga_jual', 'barang_id')
            ->get()
            ->map(function ($barang) {
                return [
                    'barang_id' => $barang->barang_id,
                    'barang_nama' => $barang->barang_nama,
                    'harga_jual' => $barang->harga_jual,
                    'stock_available' => $barang->stock_available,
                ];
            });

        return view('penjualan.edit', compact('breadcrumb', 'page', 'activeMenu'))->with([
            'penjualan' => $penjualan,
            'barang' => $barang,
            'penjualan_detail' => $penjualanWithBarang,
        ]);
    }

    public function edit_ajax(Request $request, $id)
{
    if (!$request->ajax() && !$request->wantsJson()) {
        return response()->json([
            'status' => false,
            'message' => 'Permintaan tidak valid.'
        ]);
    }

    $rules = [
        'pembeli' => 'required|string|max:255',
        'penjualan_tanggal' => 'required|date',
        'barang' => 'required|array|min:1',
        'barang.*.id' => 'required|exists:m_barang,barang_id',
        'barang.*.jumlah' => 'required|integer|min:1',
        'barang.*.total' => 'required|numeric|min:1',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status'   => false,
            'message'  => 'Validasi gagal.',
            'msgField' => $validator->errors()
        ]);
    }

    DB::beginTransaction();

    try {
        $penjualan = PenjualanModel::with('penjualan_detail')->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan.'
            ]);
        }

        // Rollback stok barang sebelumnya
        foreach ($penjualan->penjualan_detail as $detail) {
            $barang = BarangModel::find($detail->barang_id);
            if ($barang) {
                $barang->increment('stock_available', $detail->jumlah);
            }
        }

        // Hapus detail lama
        $penjualan->penjualan_detail()->delete();

        // Update data utama penjualan
        $penjualan->update([
            'pembeli' => $request->pembeli,
            'penjualan_tanggal' => $request->penjualan_tanggal,
        ]);

        $totalHarga = 0;

        // Tambah detail baru
        foreach ($request->barang as $item) {
            $barang = BarangModel::find($item['id']);

            if (!$barang) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Barang tidak ditemukan: ID ' . $item['id']
                ]);
            }

            if ($barang->stock_available < $item['jumlah']) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Stok tidak cukup untuk barang: ' . $barang->barang_nama
                ]);
            }

            $hargaItem = $barang->harga_jual * $item['jumlah'];
            $totalHarga += $hargaItem;

            // Kurangi stok baru
            $barang->decrement('stock_available', $item['jumlah']);

            // Simpan detail baru
            $penjualan->penjualan_detail()->create([
                'barang_id' => $item['id'],
                'jumlah' => $item['jumlah'],
                'harga' => $hargaItem,
            ]);
        }

        // Simpan total
        $penjualan->update(['total' => $totalHarga]);

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Penjualan berhasil diperbarui.',
            'data' => [
                'penjualan_id' => $penjualan->penjualan_id,
                'penjualan_kode' => $penjualan->penjualan_kode,
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Gagal memperbarui penjualan.',
            'error' => $e->getMessage()
        ]);
    }
}

    public function update(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_id' => 'required|exists:m_penjualan,penjualan_id',
                'pembeli' => 'required|string|max:255',
                'penjualan_tanggal' => 'required|date',
                'barang' => 'required|array|min:1',
                'barang.*.id' => 'required|exists:m_barang,barang_id',
                'barang.*.quantity' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => "Validasi gagal",
                    'msgField' => $validator->errors()
                ]);
            }

            DB::beginTransaction();
            try {
                $penjualan = PenjualanModel::find($request->penjualan_id);

                if (!$penjualan) {
                    return response()->json([
                        'status' => false,
                        'message' => "Data tidak ditemukan"
                    ]);
                }

                $penjualan->update([
                    'pembeli' => $request->pembeli,
                    'penjualan_tanggal' => $request->penjualan_tanggal,
                ]);

                // Hapus detail penjualan yang sudah ada
                $penjualan->penjualan_detail()->delete();

                foreach ($request->barang as $item) {
                    $barang = BarangModel::find($item['id']);

                    // cek stock barang
                    if ($barang->stock_available < $item['quantity']) {

                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "Stock " . $barang->barang_nama . " tidak cukup"
                        ]);
                    }

                    $penjualan->penjualan_detail()->create([
                        'penjualan_id' => $penjualan->penjualan_id,
                        'barang_id' => $item['id'],
                        'jumlah' => $item['quantity'],
                        'harga' => $barang->harga_jual * $item['quantity'],
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Penjualan berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                    'error' => $e->getMessage()
                ]);
            }
        }
        redirect('/penjualan');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'penjualan_detail')->find($id);

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
            try {
                $penjualan = PenjualanModel::with('penjualan_detail')->find($id);

                if ($penjualan) {


                    $penjualan->penjualan_detail()->delete();
                    $penjualan->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil dihapus'
                    ]);

                    if ($penjualan->trashed()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Data berhasil dihapus'
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data gagal dihapus'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            } catch (QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data gagal dihapus, karena masih digunakan'
                ]); {
                }
            }
        }

        return redirect('/');
    }
    public function export_pdf() 
    {
        $data = PenjualanModel::with('penjualan_detail', 'user')->withSum('penjualan_detail', 'harga')
            ->orderBy('created_at', 'desc')
            ->get();
 
        $pdf = Pdf::loadView('penjualan.export_pdf', ['data' => $data]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();
 
        return $pdf->stream('Data Level ' .date('Y-m-d H:i:s').'.pdf');
     }
}   