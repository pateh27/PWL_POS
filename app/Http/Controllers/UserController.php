<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
       $user = UserModel::create(
        [
           'username' => 'manager55',
           'nama' => 'Manager Lima Lima',
           'password' => Hash::make('12345'),
           'level_id' => 2
            ]
       );
      
       $user->username = 'manager56';

       $user->isDirty();
       $user->isDirty('username');
       $user->isDirty('nama');
       $user->isDirty('nama', 'username');

       $user->isClean();
       $user->isClean('username');
       $user->isClean('nama');
       $user->isClean('nama', 'username');

       $user->isDirty();
       $user->isClean();
       dd($user->isDirty());
    }
}