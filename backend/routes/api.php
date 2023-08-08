<?php

use App\Http\Controllers\AdminAplikasiController;
use App\Http\Controllers\MitraPengelolaGedungController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/register/get-email', [UsersController::class, 'cekEmailRegister']);
Route::post('/register/save-registrasi-user', [UsersController::class, 'saveRegistrasiUser']);
Route::post('/register/login', [UsersController::class, 'login']);
Route::post('/register/logout', [UsersController::class, 'logout'])->middleware('auth:api');

Route::post('pengelola/simpan-data-mitra', [MitraPengelolaGedungController::class, 'savePendaftaranMitra'])->middleware('auth:api');

Route::get('/adminapp/get-data-mitra-nonvalidasi', [AdminAplikasiController::class, 'getDataMitraNonvalidasi'])->middleware('auth:api');
Route::get('/adminapp/get-detail-mitra-nonvalidasi', [AdminAplikasiController::class, 'getDetailMitraNonvalidasi'])->middleware('auth:api');
Route::get('/adminapp/send-email-to-mitra', [AdminAplikasiController::class, 'sendEmailToMitra'])->middleware('auth:api');
Route::post('/adminapp/simpan-validasi-mitra', [AdminAplikasiController::class, 'saveValidasiMitra'])->middleware('auth:api');