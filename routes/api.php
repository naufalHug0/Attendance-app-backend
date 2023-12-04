<?php

use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Http\Resources\Karyawan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\RequestWFAController;
use App\Http\Controllers\RequestWFHController;
use App\Http\Controllers\RequestCutiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RequestPulangController;
use App\Http\Controllers\RequestSakitController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/karyawan/{id}', [KaryawanController::class,'show']);
Route::post('/karyawan/create', [KaryawanController::class, 'create'])->middleware('verifyToken');

Route::delete('/karyawan/delete', [KaryawanController::class, 'destroy'])->middleware('verifyToken');

Route::get('/karyawan/{id}', [KaryawanController::class, 'show'])->middleware('verifyToken');

Route::put('/karyawan/{id}', [KaryawanController::class, 'edit'])->middleware('verifyToken');

Route::put('/karyawan/edit/password', [KaryawanController::class, 'edit_pass'])->middleware('verifyToken');

Route::post('/karyawan/edit/profile-image', [KaryawanController::class, 'edit_profile'])->middleware('verifyToken');

Route::get('/admin/{id}', [AdminController::class, 'show'])->middleware('verifyToken');

Route::post('/absen', [AbsensiController::class,'index'])->middleware('verifyToken');

Route::post('/absen/confirm', [AbsensiController::class,'confirm'])->middleware('verifyToken');

Route::post('/absen/confirm/pulang', [RequestPulangController::class,'create'])->middleware('verifyToken');

Route::put('/absen/izin', [RequestPulangController::class,'index'])->middleware('verifyToken');

Route::get('/absen/today', [AbsensiController::class,'getToday'])->middleware('verifyToken');

Route::get('/absen/yesterday', [AbsensiController::class,'checkHaventCheckout'])->middleware('verifyToken');

Route::get('/absen/month', [AbsensiController::class,'getMonthAttendance'])->middleware('verifyToken');

Route::get('/absen/working-hours', [AbsensiController::class,'getWorkingHours'])->middleware('verifyToken');





// ========================= REQUESTS =========================

// cuti
Route::post('request/cuti', [RequestCutiController::class,'index'])->middleware('verifyToken');

Route::get('request/cuti/{id}', [RequestCutiController::class,'show'])->middleware('verifyToken');

Route::post('request/cuti/approve', [RequestCutiController::class,'update'])->middleware('cors');

// WFH
Route::post('request/wfh', [RequestWFHController::class,'index'])->middleware('verifyToken');

Route::get('request/wfh/{id}', [RequestWFHController::class,'show'])->middleware('verifyToken');

Route::post('request/wfh/approve', [RequestWFHController::class,'update'])->middleware('cors');

// WFA
Route::post('request/wfa', [RequestWFAController::class,'store'])->middleware('verifyToken');

Route::get('request/wfa/{id}', [RequestWFAController::class,'show'])->middleware('verifyToken');

Route::post('request/wfa/approve', [RequestWFAController::class,'update'])->middleware('cors');

// Sakit
Route::post('request/sakit', [RequestSakitController::class,'store'])->middleware('verifyToken');

Route::get('request/sakit/{id}', [RequestSakitController::class,'show'])->middleware('verifyToken');

Route::post('request/sakit/upload', [RequestSakitController::class,'update'])->middleware('verifyToken');

// ========================= END REQUESTS =========================





Route::post('/auth/login', [LoginController::class,'login'])->middleware('cors');

Route::get('/auth/forgot', [LoginController::class, 'update']);

Route::get('/notification/admin', [NotificationController::class,'index'])->middleware('verifyToken');

Route::get('/notification/karyawan', [NotificationController::class,'getKaryawan'])->middleware('verifyToken');

Route::get('/notification/{id}', [NotificationController::class,'show'])->middleware('verifyToken');

Route::delete('/notification/delete', [NotificationController::class,'destroy'])->middleware('verifyToken');

Route::post('/notification/{id}/read', [NotificationController::class,'update'])->middleware('verifyToken');




Route::get('admin/karyawan/all', [KaryawanController::class,'index'])->middleware('verifyToken');

Route::get('admin/karyawan/attendance/all', [AbsensiController::class,'getTotalAttendanceToday'])->middleware('verifyToken');

Route::get('admin/karyawan/report', [AbsensiController::class,'getAttendanceReport'])->middleware('verifyToken');
