<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MauKhaoSatController;
use App\Http\Controllers\CauHoiController;
use App\Http\Controllers\DotKhaoSatController;
use App\Http\Controllers\KhaoSatController;
use App\Http\Controllers\BaoCaoController;

// Public routes
Route::get('/', function () {
    return redirect()->route('khao-sat.index');
});

// Authentication
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Khảo sát công khai
Route::prefix('khao-sat')->name('khao-sat.')->group(function () {
    Route::get('/', [KhaoSatController::class, 'index'])->name('index');
    Route::get('/{dotKhaoSat}', [KhaoSatController::class, 'show'])->name('show');
    Route::post('/{dotKhaoSat}', [KhaoSatController::class, 'store'])->name('store');
    Route::get('/thank-you', [KhaoSatController::class, 'thankYou'])->name('thank-you');
});

// Admin routes (chỉ cần đăng nhập)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mẫu khảo sát
    Route::resource('mau-khao-sat', MauKhaoSatController::class);
    Route::post('mau-khao-sat/{mauKhaoSat}/copy', [MauKhaoSatController::class, 'copy'])
        ->name('mau-khao-sat.copy');

    // Câu hỏi
    Route::post('mau-khao-sat/{mauKhaoSat}/cau-hoi', [CauHoiController::class, 'store'])
        ->name('cau-hoi.store');
    Route::put('cau-hoi/{cauHoi}', [CauHoiController::class, 'update'])
        ->name('cau-hoi.update');
    Route::delete('cau-hoi/{cauHoi}', [CauHoiController::class, 'destroy'])
        ->name('cau-hoi.destroy');

    // Đợt khảo sát
    Route::resource('dot-khao-sat', DotKhaoSatController::class);
    Route::post('dot-khao-sat/{dotKhaoSat}/activate', [DotKhaoSatController::class, 'activate'])
        ->name('dot-khao-sat.activate');
    Route::post('dot-khao-sat/{dotKhaoSat}/close', [DotKhaoSatController::class, 'close'])
        ->name('dot-khao-sat.close');

    // Báo cáo
    Route::prefix('bao-cao')->name('bao-cao.')->group(function () {
        Route::get('/', [BaoCaoController::class, 'index'])->name('index');
        Route::get('/dot-khao-sat/{dotKhaoSat}', [BaoCaoController::class, 'dotKhaoSat'])
            ->name('dot-khao-sat');
        Route::get('/export/{dotKhaoSat}', [BaoCaoController::class, 'export'])
            ->name('export');
    });
});