<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');

Route::get('levels', [App\Http\Controllers\Api\LevelController::class, 'index']);
route::post('levels', [App\Http\Controllers\Api\LevelController::class, 'store']);
route::get('levels/{level}', [App\Http\Controllers\Api\LevelController::class, 'show']);
route::put('levels/{level}', [App\Http\Controllers\Api\LevelController::class, 'update']);
route::delete('levels/{level}', [App\Http\Controllers\Api\LevelController::class, 'destroy']);

Route::get('users', [App\Http\Controllers\Api\UserController::class, 'index']);
Route::post('users', [App\Http\Controllers\Api\UserController::class, 'store']);
route::get('users/{user}', [App\Http\Controllers\Api\UserController::class, 'show']);
route::put('users/{user}', [App\Http\Controllers\Api\UserController::class, 'update']);
route::delete('users/{user}', [App\Http\Controllers\Api\UserController::class, 'destroy']);

Route::get('kategoris', [App\Http\Controllers\Api\KategoriController::class, 'index']);
route::post('kategoris', [App\Http\Controllers\Api\KategoriController::class, 'store']);
route::get('kategoris/{kategori}', [App\Http\Controllers\Api\KategoriController::class, 'show']);
route::put('kategoris/{kategori}', [App\Http\Controllers\Api\KategoriController::class, 'update']);
route::delete('kategoris/{kategori}', [App\Http\Controllers\Api\KategoriController::class, 'destroy']);

Route::get('barangs', [App\Http\Controllers\Api\BarangController::class, 'index']);
route::post('barangs', [App\Http\Controllers\Api\BarangController::class, 'store']);
route::get('barangs/{barang}', [App\Http\Controllers\Api\BarangController::class, 'show']);
route::put('barangs/{barang}', [App\Http\Controllers\Api\BarangController::class, 'update']);
route::delete('barangs/{barang}', [App\Http\Controllers\Api\BarangController::class, 'destroy']);

Route::post('/register1', RegisterController::class)->name('register1');