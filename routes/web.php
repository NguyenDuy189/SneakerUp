<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes – SneakerUp
|--------------------------------------------------------------------------
| Toàn bộ route cho hệ thống SneakerUp (admin + login)
| Laravel 12 – chuẩn PSR-12 & triển khai thực tế
|--------------------------------------------------------------------------
*/

// -------------------- //
// 🔹 Trang chủ – tự động chuyển đến login
// -------------------- //
Route::get('/', function () {
    return redirect()->route('login');
});

// -------------------- //
// 🔹 Xác thực (Login / Logout)
// -------------------- //
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------- //
// 🔹 Nhóm route dành cho ADMIN (bắt buộc đăng nhập)
// -------------------- //
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 📊 Trang Dashboard chính
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API endpoint cho AJAX charts & filters
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Exports
    Route::get('/dashboard/export/csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');
    Route::get('/dashboard/export/excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export.excel');
    Route::get('/dashboard/export/pdf', [DashboardController::class, 'exportPdf'])->name('dashboard.export.pdf');

    // 📦 Quản lý đơn hàng
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{order}/ajax-update-status', [OrderController::class, 'ajaxUpdateStatus'])->name('ajaxUpdateStatus');
        Route::post('/{order}/add-note', [OrderController::class, 'addNote'])->name('addNote');
        Route::get('/export/csv', [OrderController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/excel', [OrderController::class, 'exportExcel'])->name('export.excel');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    // 💳 Quản lý thanh toán
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::patch('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('markPaid');
        Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
    });

    // 🔔 Thông báo
    Route::get('/notifications', function () {
        $user = Auth::user() ?? \App\Models\User::first();
        return $user->notifications()->take(20)->get();
    })->name('notifications');
});
