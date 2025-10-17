<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes – SneakerUp
|--------------------------------------------------------------------------
| Toàn bộ route cho hệ thống SneakerUp (admin + login)
| Laravel 12 – bản chuẩn cho đồ án & thực tế
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
// Middleware `auth` đảm bảo chỉ admin đã login mới vào được
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 📊 Trang dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // 📦 Quản lý đơn hàng
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/add-note', [OrderController::class, 'addNote'])->name('orders.addNote');

    // 📑 Export & PDF Invoice
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/export/csv', [OrderController::class, 'exportCsv'])->name('orders.export.csv');
    Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');

    // ⚙️ AJAX cập nhật trạng thái (nếu dùng)
    Route::post('orders/{order}/ajax-update-status', [OrderController::class, 'ajaxUpdateStatus'])->name('orders.ajaxUpdateStatus');

    // 🔔 Xem thông báo (Notifications)
    Route::get('/user/notifications', function () {
        $user = Auth::user() ?? \App\Models\User::first();
        return $user->notifications()->take(20)->get();
    })->name('user.notifications');
});
