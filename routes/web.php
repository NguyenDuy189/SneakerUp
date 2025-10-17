<?php

use Illuminate\Support\Facades\Route;
// Import tất cả các Controller cần thiết
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// === CÁC ROUTE CHO ADMIN ===

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// Nhóm TẤT CẢ các route admin cần được bảo vệ
Route::prefix('admin')->middleware('admin.auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Quản lý Tất cả Tài khoản
    Route::resource('users', UserController::class)->names('admin.users');

    // Quản lý Khách hàng
    Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update'])->names('admin.customers');

    // Quản lý Đơn hàng
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');

    // === BẢO VỆ TOÀN DIỆN CHO NHÂN VIÊN ===
    Route::controller(StaffController::class)->prefix('staff')->name('admin.staff.')->group(function () {
        Route::get('/', 'index')->middleware('can:user-list')->name('index');
        Route::get('/create', 'create')->middleware('can:user-create')->name('create');
        Route::post('/', 'store')->middleware('can:user-create')->name('store');
        Route::get('/{staff}/edit', 'edit')->middleware('can:user-edit')->name('edit');
        Route::put('/{staff}', 'update')->middleware('can:user-edit')->name('update');
        Route::delete('/{staff}', 'destroy')->middleware('can:user-delete')->name('destroy');
    });

    // === BẢO VỆ TOÀN DIỆN CHO VAI TRÒ & QUYỀN HẠN ===
    // Sửa lại khối này để đồng bộ với StaffController
    Route::controller(RoleController::class)->prefix('roles')->name('admin.roles.')->group(function () {
        Route::get('/', 'index')->middleware('can:role-list')->name('index');
        Route::get('/create', 'create')->middleware('can:role-create')->name('create');
        Route::post('/', 'store')->middleware('can:role-create')->name('store');
        Route::get('/{role}/edit', 'edit')->middleware('can:role-edit')->name('edit');
        Route::put('/{role}', 'update')->middleware('can:role-edit')->name('update');
        Route::delete('/{role}', 'destroy')->middleware('can:role-delete')->name('destroy');
    });

});
