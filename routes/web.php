<?php

use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KhaoSatController;

// Nạp các route dành cho admin:
require __DIR__ . '/admin.php';

// Public routes
Route::get('/', function () {
    return redirect()->route('khao-sat.index');
});
Route::get('/thank-you', [KhaoSatController::class, 'thanks'])->name('thanks');
Route::get('/test-config', function () {
    // Lấy giá trị config 'app.name'
    $appName = config('app.name');

    // Lấy giá trị config 'mail.from.address'
    $mailFrom = config('mail.from.address');

    // In ra để kiểm tra
    dd([
        'app.name from config()' => $appName,
        'mail.from.address from config()' => $mailFrom
    ]);
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
});
