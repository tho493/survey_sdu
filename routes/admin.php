<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemConfigController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\DoiTuongController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    // Đối tượng khảo sát
    Route::resource('doi-tuong', DoiTuongController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/survey/{dotKhaoSat}', [ReportController::class, 'survey'])->name('survey');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
        Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    });

    // System Configuration
    Route::prefix('config')->name('config.')->group(function () {
        Route::get('/', [SystemConfigController::class, 'index'])->name('index');
        Route::post('/update', [SystemConfigController::class, 'update'])->name('update');
        Route::put('/email-template/{template}', [SystemConfigController::class, 'updateEmailTemplate'])
            ->name('update-email-template');
        Route::post('/test-email', [SystemConfigController::class, 'testEmail'])->name('test-email');
        Route::post('/backup', [SystemConfigController::class, 'backup'])->name('backup');
    });

    // System Logs
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/user', [LogController::class, 'userLogs'])->name('user');
        Route::get('/system', [LogController::class, 'systemLogs'])->name('system');
        Route::delete('/clear', [LogController::class, 'clear'])->name('clear');
    });
});