<?php

namespace App\Http\Controllers\Api;

use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke(Request $request){
        
        // //upload image
        // $image = $request->file('image');
        // $image->store('public/profile');

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required|min:5|confirmed',
            'level_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        //if validator fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->store('images', 'public');
            $user = UserModel::create([
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => bcrypt($request->password),
                'level_id' => $request->level_id,
                'image' => $image->hashName(),
            ]);
        }
        //return respone Json user is created
        if($user){
            return response()->json([
                'success' => true,
                'user' => $user
            ], 201);
        }

        //return JSON process insert failed
        return response()->json([
            'success' => false
        ], 409);
    }
}
