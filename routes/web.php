<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix'=>'user'], function(){
    Route::get('/', [UserController::class, 'index']);
    Route::get('/tambah', [UserController::class, 'tambah']);
    Route::get('/tambah_simpan', [UserController::class, 'tambah_simpan']);
    Route::get('/ubah/{id}', [UserController::class, 'ubah']);
    Route::get('/ubah_simpan/{id}',[UserController::class, 'ubah_simpan']) -> name('user.ubah_simpan');
    Route::get('/hapus/{id}', [UserController::class, 'hapus']);
});

// Route::get('/level',[LevelController::class, 'index']);
// Route::get('/kategori', [KategoriController::class, 'index']);
// Route::get('/user',[UserController::class, 'index']);
// Route::get('/user/tambah', [UserController::class, 'tambah']);
// Route::get('/user/tambah_simpan', [UserController::class, 'tambah_simpan']);
// Route::get('/user/ubah/{id}', [UserController::class, 'ubah']);
// Route::get('/user/ubah_simpan/{id}',[UserController::class, 'ubah_simpan']) -> name('user.ubah_simpan');
// Route::get('/user/hapus/{id}', [UserController::class, 'hapus']);
// Route::get('/', [WelcomeController::class, 'index']);