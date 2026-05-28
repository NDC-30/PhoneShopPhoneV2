<?php

use Illuminate\Support\Facades\Route;

// =========================
// CUSTOMER CONTROLLERS
// =========================
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;

// =========================
// ADMIN CONTROLLERS
// =========================
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\VoucherController;

// =========================
// AUTH CONTROLLER
// =========================
use App\Http\Controllers\AuthController;



// ======================================================
// AUTH ADMIN
// ======================================================

// Trang login admin
Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->name('login');

// Xử lý login
Route::post('/admin/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');



// ======================================================
// CUSTOMER ROUTES
// ======================================================

// Trang chủ
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Danh sách điện thoại
Route::get('/dien-thoai', [ShopController::class, 'index'])
    ->name('shop.index');

// Chi tiết sản phẩm
Route::get('/san-pham/{slug}', [CustomerProductController::class, 'show'])
    ->name('shop.detail');



// ======================================================
// GIỎ HÀNG
// ======================================================

Route::prefix('gio-hang')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/add', [CartController::class, 'add'])
        ->name('add');

    Route::post('/update', [CartController::class, 'update'])
        ->name('update');

    Route::post('/remove', [CartController::class, 'remove'])
        ->name('remove');
});



// ======================================================
// THANH TOÁN
// ======================================================

Route::prefix('thanh-toan')->name('checkout.')->group(function () {

    Route::get('/', [CheckoutController::class, 'index'])
        ->name('index');

    Route::post('/process', [CheckoutController::class, 'process'])
        ->name('process');

    Route::post('/apply-voucher', [CheckoutController::class, 'applyVoucher'])
        ->name('applyVoucher');
});



// ======================================================
// ADMIN ROUTES - BẮT BUỘC LOGIN
// ======================================================

Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        // =========================
        // DASHBOARD
        // =========================
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');



        // =========================
        // SETTINGS
        // =========================

        // Trang cài đặt tài khoản
        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings');

        // Update tài khoản
        Route::post(
            '/settings/update',
            [AuthController::class, 'updateSettings']
        )->name('settings.update');



        // =========================
        // CATEGORY
        // =========================
        Route::resource('categories', CategoryController::class);



        // =========================
        // BRAND
        // =========================
        Route::resource('brands', BrandController::class);



        // =========================
        // PRODUCTS
        // =========================
        Route::resource('products', AdminProductController::class);



        // =========================
        // VARIANTS
        // =========================

        // Danh sách biến thể
        Route::get(
            'products/{product_id}/variants',
            [VariantController::class, 'index']
        )->name('products.variants.index');

        // Thêm biến thể
        Route::post(
            'products/{product_id}/variants',
            [VariantController::class, 'store']
        )->name('products.variants.store');

        // Form sửa biến thể
        Route::get(
            'products/{product_id}/variants/{variant_id}/edit',
            [VariantController::class, 'edit']
        )->name('products.variants.edit');

        // Update biến thể
        Route::put(
            'products/{product_id}/variants/{variant_id}',
            [VariantController::class, 'update']
        )->name('products.variants.update');

        // Xóa biến thể
        Route::delete(
            'products/{product_id}/variants/{variant_id}',
            [VariantController::class, 'destroy']
        )->name('products.variants.destroy');



        // =========================
        // ORDERS
        // =========================
        Route::resource('orders', OrderController::class);



        // =========================
        // VOUCHERS
        // =========================
        Route::resource('vouchers', VoucherController::class);
    });
