<?php

use Illuminate\Support\Facades\Route;

// === CONTROLLER CỦA ADMIN ===
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Client\HomeController;

// === CONTROLLER CỦA BREEZE (CLIENT) ===
use App\Http\Controllers\ProfileController;

// === CONTROLLER "TÀI KHOẢN CỦA TÔI" ===
use App\Http\Controllers\Client\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// === ROUTE CHUNG ===
Route::get('/', [HomeController::class, 'index'])->name('client.home');

// === CÁC ROUTE CHO ADMIN (CỦA BẠN) ===
Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login.form');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

// Nhóm TẤT CẢ các route admin cần được bảo vệ
Route::prefix('admin')->middleware('admin.auth')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class)->names('users');
    Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update'])->names('customers');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::controller(StaffController::class)->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', 'index')->middleware('can:user-list')->name('index');
        Route::get('/create', 'create')->middleware('can:user-create')->name('create');
        Route::post('/', 'store')->middleware('can:user-create')->name('store');
        Route::get('/{staff}/edit', 'edit')->middleware('can:user-edit')->name('edit');
        Route::put('/{staff}', 'update')->middleware('can:user-edit')->name('update');
        Route::delete('/{staff}', 'destroy')->middleware('can:user-delete')->name('destroy');
    });

    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->middleware('can:role-list')->name('index');
        Route::get('/create', 'create')->middleware('can:role-create')->name('create');
        Route::post('/', 'store')->middleware('can:role-create')->name('store');
        Route::get('/{role}/edit', 'edit')->middleware('can:role-edit')->name('edit');
        Route::put('/{role}', 'update')->middleware('can:role-edit')->name('update');
        Route::delete('/{role}', 'destroy')->middleware('can:role-delete')->name('destroy');
    });

    Route::resource('post-categories', PostCategoryController::class)
          ->names('post-categories');
    Route::resource('posts', PostController::class)
          ->names('posts');

    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });
});

// === CÁC ROUTE CỦA BREEZE (CLIENT) ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// File này chứa các route /login, /register, /logout... của CLIENT
require __DIR__.'/auth.php';


// === CÁC ROUTE "TÀI KHOẢN CỦA TÔI" ===
Route::middleware(['auth'])->prefix('tai-khoan')->name('client.')->group(function () {

    // Task: "Thông tin người dùng" & "Thay đổi thông tin"
    Route::get('/thong-tin', [AccountController::class, 'showProfileForm'])->name('profile.show');
    Route::post('/thong-tin', [AccountController::class, 'updateProfile'])->name('profile.update');

    // Task: "Xem lịch sử mua hàng"
    Route::get('/don-hang', [AccountController::class, 'showOrders'])->name('orders.index');
    Route::get('/don-hang/{order}', [AccountController::class, 'showOrderDetail'])->name('orders.show');

    // Task: "Địa chỉ giao hàng" (Đã xóa khối bị lặp)
    Route::get('/dia-chi/them-moi', [AccountController::class, 'addressCreate'])->name('address.create');
    Route::post('/dia-chi', [AccountController::class, 'addressStore'])->name('address.store');
    Route::get('/dia-chi/{address}/sua', [AccountController::class, 'addressEdit'])->name('address.edit');
    Route::put('/dia-chi/{address}', [AccountController::class, 'addressUpdate'])->name('address.update');
    Route::delete('/dia-chi/{address}', [AccountController::class, 'addressDestroy'])->name('address.destroy');
    Route::patch('/dia-chi/{address}/mac-dinh', [AccountController::class, 'addressSetDefault'])->name('address.setDefault');
    Route::get('/dia-chi', [AccountController::class, 'addressIndex'])->name('address.index');
});
