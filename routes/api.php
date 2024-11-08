<?php

use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PenjualanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

// level
Route::get('levels', [LevelController::class, 'index']);
Route::post('levels', [LevelController::class, 'store']);
Route::get('levels/{level}', [LevelController::class, 'show']);
Route::put('levels/{level}', [LevelController::class, 'update']);
Route::delete('levels/{level}', [LevelController::class, 'destroy']);

//barang
Route::get('barangs', [BarangController::class, 'index']);
Route::post('barangs', [BarangController::class, 'store']);
Route::get('barangs/{barang}', [BarangController::class, 'show']);
Route::put('barangs/{barang}', [BarangController::class, 'update']);
Route::delete('barangs/{barang}', [BarangController::class, 'destroy']);

//kategori
Route::get('kategoris', [KategoriController::class, 'index']);
Route::post('kategoris', [KategoriController::class, 'store']);
Route::get('kategoris/{kategori}', [KategoriController::class, 'show']);
Route::put('kategoris/{kategori}', [KategoriController::class, 'update']);
Route::delete('kategoris/{kategori}', [KategoriController::class, 'destroy']);

//user
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::put('users/{user}', [UserController::class, 'update']);
Route::delete('users/{user}', [UserController::class, 'destroy']);


//penjualan
Route::get('penjualans', [PenjualanController::class, 'index']);
Route::post('penjualans', [PenjualanController::class, 'store']);
Route::get('penjualans/{penjualan}', [PenjualanController::class, 'show']);
Route::put('penjualans/{penjualan}', [PenjualanController::class, 'update']);
Route::delete('penjualans/{penjualan}', [PenjualanController::class, 'destroy']);


Route::post('/register1', App\Http\Controllers\Api\RegisterController::class)->name('register1');
Route::post('/register', [App\Http\Controllers\Api\RegisterController::class, '__invoke'])->name('register');
Route::post('/login', [App\Http\Controllers\Api\LoginController::class, '__invoke'])->name('login');
Route::post('/logout', [App\Http\Controllers\Api\LogoutController::class, '__invoke'])->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
