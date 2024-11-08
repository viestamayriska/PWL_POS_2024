<?php

use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LevelController;

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




Route::post('/register', [App\Http\Controllers\Api\RegisterController::class, '__invoke'])->name('register');
Route::post('/login', [App\Http\Controllers\Api\LoginController::class, '__invoke'])->name('login');
Route::post('/logout', [App\Http\Controllers\Api\LogoutController::class, '__invoke'])->name('logout');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
