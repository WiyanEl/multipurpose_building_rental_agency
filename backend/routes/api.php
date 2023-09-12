<?php

use App\Http\Controllers\AdminAplikasiController;
use App\Http\Controllers\MitraPengelolaGedungController;
use App\Http\Controllers\PenyewaGedungController;
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

Route::get('register/get-email', [UsersController::class, 'cekEmailRegister']);
Route::post('register/save-registrasi-user', [UsersController::class, 'saveRegistrasiUser']);
Route::post('register/login', [UsersController::class, 'login']);
Route::post('register/logout', [UsersController::class, 'logout'])->middleware('auth:api');

Route::post('penyewa/save-akun-user-penyewa', [PenyewaGedungController::class, 'saveDataAkunPenyewa']);
Route::get('penyewa/get-list-mitra', [PenyewaGedungController::class, 'getListDataMitra']);
Route::get('penyewa/get-data-mitra', [PenyewaGedungController::class, 'getDataMitra']);
Route::get('penyewa/get-data-aset-mitra', [PenyewaGedungController::class, 'getDataAsetMitra']);
Route::get('penyewa/get-data-aset-mitra-detail', [PenyewaGedungController::class, 'getDataAsetMitraDetail']);

Route::post('pengelola/simpan-data-mitra', [MitraPengelolaGedungController::class, 'savePendaftaranMitra'])->middleware('auth:api');
Route::get('pengelola/get-data-mitra', [MitraPengelolaGedungController::class, 'getDataMitra'])->middleware('auth:api');
Route::get('pengelola/get-data-aset-mitra', [MitraPengelolaGedungController::class, 'getDataAsetMitra'])->middleware('auth:api');
Route::get('pengelola/get-list-aset', [MitraPengelolaGedungController::class, 'getListAsetByIdMitra'])->middleware('auth:api');
Route::post('pengelola/save-data-aset', [MitraPengelolaGedungController::class, 'saveDataAset'])->middleware('auth:api');
Route::post('pengelola/simpan-gambar-aset', [MitraPengelolaGedungController::class, 'saveGambarAset'])->middleware('auth:api');
Route::post('pengelola/update-status-aset', [MitraPengelolaGedungController::class, 'updateStatusAset'])->middleware('auth:api');
Route::get('pengelola/get-harga-tambahan-aset', [MitraPengelolaGedungController::class, 'getHargaTambahanAset'])->middleware('auth:api');
Route::post('pengelola/hapus-aset', [MitraPengelolaGedungController::class, 'hapusDataAset'])->middleware('auth:api');
Route::get('pengelola/get-data-diskon-mitra', [MitraPengelolaGedungController::class, 'getDataDiskonMitra'])->middleware('auth:api');
Route::get('pengelola/get-data-diskon-mitra-byid', [MitraPengelolaGedungController::class, 'getDataDiskonMitraById'])->middleware('auth:api');
Route::post('pengelola/save-diskon-mitra', [MitraPengelolaGedungController::class, 'saveDiskonMitra'])->middleware('auth:api');
Route::post('pengelola/update-status-diskon', [MitraPengelolaGedungController::class, 'updateStatusDiskonMitra'])->middleware('auth:api');
Route::get('pengelola/get-data-pesanan-sewa-mitra', [MitraPengelolaGedungController::class, 'getDataPesananMitra'])->middleware('auth:api');
Route::get('pengelola/cek-jadwal-sewa-mitra', [MitraPengelolaGedungController::class, 'getJadwalSewaMitra'])->middleware('auth:api');
Route::post('pengelola/save-jadwal-sewa-manual', [MitraPengelolaGedungController::class, 'saveJadwalSewaManual'])->middleware('auth:api');
Route::get('pengelola/get-data-sewa-byid', [MitraPengelolaGedungController::class, 'getDataSewaById'])->middleware('auth:api');
Route::get('pengelola/get-data-pembayaran-sewa-byid', [MitraPengelolaGedungController::class, 'getDataPembayaranById'])->middleware('auth:api');
Route::get('pengelola/get-data-pesanan-tambahan', [MitraPengelolaGedungController::class, 'getDataPesananTambahan'])->middleware('auth:api');
Route::post('pengelola/batal-pesanan-sewa', [MitraPengelolaGedungController::class, 'saveBatalPesananSewa'])->middleware('auth:api');

Route::get('adminapp/get-data-mitra-nonvalidasi', [AdminAplikasiController::class, 'getDataMitraNonvalidasi'])->middleware('auth:api');
Route::get('adminapp/get-detail-mitra-nonvalidasi', [AdminAplikasiController::class, 'getDetailMitraNonvalidasi'])->middleware('auth:api');
Route::get('adminapp/send-email-to-mitra', [AdminAplikasiController::class, 'sendEmailToMitra'])->middleware('auth:api');
Route::post('adminapp/simpan-validasi-mitra', [AdminAplikasiController::class, 'saveValidasiMitra'])->middleware('auth:api');