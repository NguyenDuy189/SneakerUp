<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes – SneakerUp
|--------------------------------------------------------------------------
| Toàn bộ route cho hệ thống SneakerUp (admin + login)
| Laravel 12 – chuẩn cho đồ án & triển khai thực tế
|--------------------------------------------------------------------------
*/

// -------------------- //
// 🔹 Trang chủ (tùy chọn)
// -------------------- //
Route::get('/', function () {
    return redirect()->route('login'); // chuyển thẳng đến login
});

// -------------------- //
// 🔹 Đăng nhập / Đăng xuất Admin
// -------------------- //
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------- //
// 🔹 Nhóm route Admin
// -------------------- //
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 📊 Trang dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // 📦 Quản lý đơn hàng
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/ajax-update-status', [OrderController::class, 'ajaxUpdateStatus'])->name('orders.ajaxUpdateStatus');
    Route::post('orders/{order}/add-note', [OrderController::class, 'addNote'])->name('orders.addNote');

    // 📑 Xuất file & Hóa đơn PDF
    Route::get('orders/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
    Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // 💳 Quản lý thanh toán
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');
    Route::post('payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.markPaid');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
    Route::get('payments/export', [PaymentController::class, 'export'])->name('payments.export');

    // 🔔 Xem thông báo (Notifications)
    Route::get('/user/notifications', function () {
        $user = Auth::user() ?? \App\Models\User::first();
        return $user->notifications()->take(20)->get();
    })->name('user.notifications');
});
