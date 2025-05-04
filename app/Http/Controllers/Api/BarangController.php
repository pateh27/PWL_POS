<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use function PHPSTORM_META\map;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = BarangModel::with('kategori')->get();
        return response()->json($barangs);
    }

    public function show(BarangModel $barang)
    {
       return response()->json($barang->load(['kategori']));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id' => 'required',
            'barang_kode' => 'required|unique:m_barang',
            'barang_nama' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors(), 422]);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->store('image', 'public');
            $data['image'] = $image->hashName();
        }

        $barang = BarangModel::create($data);

        if ($barang) {
            return response()->json([
                'success' => true,
                'barang' => $barang,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }

    public function update(Request $request, BarangModel $barang)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required',
            'barang_kode' => 'required|unique:m_barang,barang_kode,' . $barang->barang_id . ',barang_id',
            'barang_nama' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($barang->image_path) {
                Storage::delete('public/' . $barang->image_path);
            }

            $image = $request->file('image');
            $image->store('images', 'public');
            $data['image_path'] = $image->hashName();
        }

        $barang->update($data);
        return response()->json($barang->load(['kategori']));
    }

    public function destroy(BarangModel $barang)
    {
        if ($barang->image_path) {
            Storage::delete('public/' . $barang->image_path);
        }

        $barang->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Terhapus',
        ]);
    }
}
