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
use App\Http\Controllers\PerjalananDinasController;
use App\Http\Controllers\FaceCompareController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LiveAttendanceController;
use App\Http\Middleware\SecureApiMiddleware;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/compare-faces', [FaceCompareController::class, 'compareFromApi']);


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
    Route::get('/presensi-employee', [ShiftController::class, 'getPresensiEmployee']);
    Route::get('/shift-active', [ShiftController::class, 'getShiftActive']);

    Route::apiResource('perjalanan-dinas', PerjalananDinasController::class);
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
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('live-attendance', LiveAttendanceController::class);
    Route::post('/live-attendance/keluar', [LiveAttendanceController::class, 'updateKeluar']);
    Route::get('/live-attendance/user/{companies_users_id}', [LiveAttendanceController::class, 'getByUser']);
    Route::get('/live-attendance/company/{company_id}', [LiveAttendanceController::class, 'getByCompany']);


    Route::get('/notifications/user/{userId}', [NotificationController::class, 'notificationsUser']);
    Route::post('/notifications/markasread/{userId}', [NotificationController::class, 'markAsRead']);
    Route::get('/notifications/unread-count/{userId}', [NotificationController::class, 'unreadNotificationCount']);

    Route::get('/perizinan-user/{id}', [PerizinanController::class, 'getPerizinanWhereCompanyUser']);
    Route::get('/perizinan/summary/{id}', [PerizinanController::class, 'getPerizinanSummary']);
    Route::get('/perizinan/summary/hrd/{company_id}', [PerizinanController::class, 'getPerizinanSummaryByCompany']);
    Route::patch('/perizinan/{id}/status', [PerizinanController::class, 'updateStatus']);

    Route::patch('/perjalanan-dinas/{id}/status', [PerjalananDinasController::class, 'updateStatus']);
    Route::get('/perjalanan-dinas/summary/hrd/{company_id}', [PerjalananDinasController::class, 'getPerjalananDinasSummaryByCompany']);
    Route::get('/perjalanan-dinas/summary/{id}', [PerjalananDinasController::class, 'getPerjalananDinasSummaryByCompanyUser']);
    Route::get('/perjalanan-dinas/approved/{companiesUsersId}', [PerjalananDinasController::class, 'getApprovedTrips']);


    Route::get('/lembur-user/{id}', [LemburController::class, 'getLemburWhereCompanyUser']);
    Route::get('/lembur/summary/{id}', [LemburController::class, 'summary']);
    Route::get('/lembur/summary/hrd/{company_id}', [LemburController::class, 'getLemburSummaryByCompany']);
    Route::patch('/lembur/{id}/status', [LemburController::class, 'updateStatus']);

    Route::get('/kasbon-user/{id}', [KasbonController::class, 'getKasbonWhereCompanyUser']);
    Route::get('/dashboard/{id}', [CompanyController::class, 'dashboard']);
    Route::get('/company-detail/{id}', [CompanyController::class, 'getDetailCompany']);

});
