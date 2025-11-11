<?php
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;
// Import tất cả các Controller cần thiết
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseLogController;
use App\Http\Controllers\Admin\DiscountController;
//client


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'index']);

// === CÁC ROUTE CHO ADMIN ===

Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login');
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

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
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class)->only(['index', 'destroy']);
    });
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    });
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    });

    //rou này dành cho nút sửa
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    });
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    
    //rou quản lý biến thể màu sắc,size,ảnh
    Route::prefix('admin')->group(function () {
    Route::get('/products/{product}/variants', [ProductVariantController::class, 'index'])->name('variants.index');
    Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('variants.create');
    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
    Route::delete('/variants/{id}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    });

    Route::resource('admin/tags', TagController::class);//Quản lý tags sản phẩm (Hot, New, Sale)
    });

    //Nhập / xuất file Excel danh sách sản phẩm
    Route::get('/admin/products/export', [ProductController::class, 'exportExcel'])->name('products.export');
    Route::post('/admin/products/import', [ProductController::class, 'importExcel'])->name('products.import');
    
    //Bật / tắt hiển thị sản phẩm nổi bật
    Route::post('/admin/products/{id}/toggle-featured', [ProductController::class, 'toggleFeatured'])
    ->name('admin.products.toggle-featured');

    //Quản lý kho hàng
    Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('admin.warehouse.index');
    Route::get('/warehouse/import/{id}', [WarehouseController::class, 'showImportForm'])->name('admin.warehouse.import');
    Route::post('/warehouse/import/{id}', [WarehouseController::class, 'storeImport'])->name('admin.warehouse.import.store');
    Route::get('/warehouse/export/{id}', [WarehouseController::class, 'showExportForm'])->name('admin.warehouse.export');
    Route::post('/warehouse/export/{id}', [WarehouseController::class, 'storeExport'])->name('admin.warehouse.export.store');
    });

    //Nhật kí xuất nhập kho
    Route::prefix('admin')->group(function () {
    Route::get('/warehouse/logs', [WarehouseLogController::class, 'index'])->name('admin.warehouse.logs');
    });


    //quản lý mã giảm giá
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class);
    });


    //ng dùng
