<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+'); // When there is a parameter (id), it must be a number

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function() { 
    // Insert all routes that require authentication here
});

Route::get('/', [WelcomeController::class, 'index']);


// Artinya semua route di dalam group ini harus punya role ADM (Administrator)
Route::middleware(['authorize:ADM'])->group(function() {
    Route::get('/level', [LevelController::class, 'index']);
    Route::post('/level/list', [LevelController::class, 'list']); // untuk list json datatables
    Route::get('/level/create', [LevelController::class, 'create']);
    Route::post('/level', [LevelController::class, 'store']);
    Route::get('/level/{id}/edit', [LevelController::class, 'edit']); // untuk tampilkan form edit
    Route::put('/level/{id}', [LevelController::class, 'update']); // untuk proses update data
    Route::delete('/level/{id}', [LevelController::class, 'destroy']); // untuk proses hapus data
});

// Route group for user-related routes
Route::group(['prefix' => 'user'], function () {
    // Menampilkan halaman awal user
    Route::get('/', [UserController::class, 'index']);

    // Menampilkan data user dalam bentuk JSON untuk DataTables
    Route::post('/list', [UserController::class, 'list']);

    // Menampilkan halaman form tambah user
    Route::get('/create', [UserController::class, 'create']);

    // Menyimpan data user baru
    Route::post('/', [UserController::class, 'store']);

    // Menampilkan halaman form tambah user Ajax
    Route::get('/create_ajax', [UserController::class, 'create_ajax']);

    // Menyimpan data user baru Ajax
    Route::post('/ajax', [UserController::class, 'store_ajax']);

    // Menampilkan detail user
    Route::get('/{id}', [UserController::class, 'show']);

    // Menampilkan halaman form edit user
    Route::get('/{id}/edit', [UserController::class, 'edit']);
    
    // Menyimpan perubahan data user
    Route::put('/{id}', [UserController::class, 'update']);
    
    // Menampilkan halaman form edit user Ajax
    Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);

    // Menyimpan perubahan data user Ajax
    Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);

    // Untuk tampilkan form konfirmasi hapus user Ajax
    Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);

    // Untuk hapus data user Ajax
    Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);

    // Menghapus data user
    Route::delete('/{id}', [UserController::class, 'destroy']);
});


