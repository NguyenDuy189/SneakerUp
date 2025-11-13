<?php

use App\Http\Controllers\Admin\Api\DashboardApiController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes – SneakerUp
|--------------------------------------------------------------------------|
| Laravel 12 – Chuẩn PSR-12 & thực tế sản phẩm
|--------------------------------------------------------------------------|
*/

// -----------------------------
// 🏠 Trang chủ – redirect login
// -----------------------------
Route::get('/', fn() => redirect()->route('login'));

// -----------------------------
// 🔐 XÁC THỰC (LOGIN / LOGOUT)
// -----------------------------
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.post');
    Route::get('/logout', 'logout')->name('logout');
});

// -----------------------------
// 🧭 ADMIN ROUTES (bắt buộc đăng nhập)
// -----------------------------
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    /* ===============================
       📊 DASHBOARD CHÍNH
    ================================ */
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/data', [DashboardController::class, 'data'])->name('data');

        // Export
        Route::get('/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/excel', [DashboardController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [DashboardController::class, 'exportPdf'])->name('export.pdf');
    });

    /* ===============================
       📦 QUẢN LÝ ĐƠN HÀNG
    ================================ */
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{order}/ajax-update-status', [OrderController::class, 'ajaxUpdateStatus'])->name('ajaxUpdateStatus');
        Route::post('/{order}/add-note', [OrderController::class, 'addNote'])->name('addNote');

        // Export
        Route::get('/export/csv', [OrderController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/excel', [OrderController::class, 'exportExcel'])->name('export.excel');

        // Hóa đơn
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('invoice');
    });

    /* ===============================
       💳 QUẢN LÝ THANH TOÁN
    ================================ */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::patch('/{payment}/status', [PaymentController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('markPaid');
        Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');

        // Export
        Route::get('/export', [PaymentController::class, 'export'])->name('export');
    });

    /* ===============================
       📈 BÁO CÁO & THỐNG KÊ (REPORT)
    ================================ */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');

        // Smart Analytics Premium
        Route::get('/smart-analytics', [ReportController::class, 'smartAnalytics'])->name('smartAnalytics');
    });

    /* ===============================
       🧱 QUẢN LÝ SẢN PHẨM
    ================================ */
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    /* ===============================
       ⚡ API DASHBOARD REALTIME (AJAX)
    ================================ */
    Route::prefix('api')->name('api.')->group(function() {
        Route::get('dashboard/sales-trend', [DashboardApiController::class, 'salesTrend'])->name('dashboard.salesTrend');
        Route::get('dashboard/product-analysis', [DashboardApiController::class, 'productAnalysis'])->name('dashboard.productAnalysis');
        Route::get('dashboard/top-customers', [DashboardApiController::class, 'topCustomers'])->name('dashboard.topCustomers');
        Route::get('dashboard/stats', [DashboardApiController::class, 'stats'])->name('dashboard.stats');

        // Select2 Autocomplete
        Route::get('customers', [DashboardApiController::class, 'autocompleteCustomers'])->name('customers');
        Route::get('categories', [DashboardApiController::class, 'autocompleteCategories'])->name('categories');
    });

    /* ===============================
       🔔 THÔNG BÁO HỆ THỐNG
    ================================ */
    Route::get('/notifications', function () {
        $user = Auth::user() ?? \App\Models\User::first();
        return $user->notifications()->take(20)->get();
    })->name('notifications');

    // === QUẢN LÝ DANH MỤC ===
    Route::prefix('categories')->name('categories.')->group(function () {
        
        // Trang danh sách (Cây danh mục)
        Route::get('/', [CategoryController::class, 'index'])->name('index');

        // Form tạo mới
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        
        // Form chỉnh sửa
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        
        // Xử lý lưu (Tạo mới)
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        
        // Xử lý lưu (Cập nhật)
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        
        // Xử lý Xóa
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        
        // Route AJAX cho việc kéo-thả
        Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
    });

});
