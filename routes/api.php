<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JenisIzinController;
use App\Http\Controllers\CategoryIzinController;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\JamController;
use App\Http\Controllers\PresensiMasukController;
use App\Http\Controllers\PresensiKeluarController;
use App\Http\Middleware\SecureApiMiddleware;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([SecureApiMiddleware::class])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/create-company', [AuthController::class, 'createCompanyAndUser']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/register-company', [CompanyController::class, 'register']);
    Route::post('/create-company-user', [CompanyUserController::class, 'store']);
    Route::get('/company-user/{id}', [CompanyUserController::class, 'show']);
    Route::put('/company-user/{id}', [CompanyUserController::class, 'update']);
    Route::post('/send-email', [CompanyUserController::class, 'sendEmail']);
    Route::delete('/company-user/{id}', [CompanyUserController::class, 'deleteEmployee']);

    Route::get('/all-company-user/{id}', [CompanyUserController::class, 'getCompanyUserWhereCompany']);
    Route::get('/presensi', [ShiftController::class, 'getPreseni']);
    Route::get('/shift-active', [ShiftController::class, 'getShiftActive']);

    Route::apiResource('division', DivisionController::class);
    Route::apiResource('jabatan', JabatanController::class);
    Route::apiResource('jenis-izin', JenisIzinController::class);
    Route::apiResource('category-izin', CategoryIzinController::class);
    Route::apiResource('perizinan', PerizinanController::class);
    Route::apiResource('lembur', LemburController::class);
    Route::apiResource('kasbon', KasbonController::class);
    Route::apiResource('shift', ShiftController::class);
    Route::apiResource('jam', JamController::class);
    Route::apiResource('presensi-masuk', PresensiMasukController::class);
    Route::apiResource('presensi-keluar', PresensiKeluarController::class);

    Route::get('/perizinan-user/{id}', [PerizinanController::class, 'getPerizinanWhereCompanyUser']);
    Route::get('/lembur-user/{id}', [LemburController::class, 'getLemburWhereCompanyUser']);
    Route::get('/kasbon-user/{id}', [KasbonController::class, 'getKasbonWhereCompanyUser']);
    Route::get('/dashboard/{id}', [CompanyController::class, 'dashboard']);
    Route::get('/company-detail/{id}', [CompanyController::class, 'getDetailCompany']);

});
