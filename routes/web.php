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
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\CustomerController;

// =========================
// AUTH CONTROLLER
// =========================
use App\Http\Controllers\AuthController;


// ======================================================
// AUTH ADMIN
// ======================================================

// Trang login admin
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');

// Xử lý login
Route::post('/admin/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ======================================================
// CUSTOMER ROUTES
// ======================================================

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh sách điện thoại
Route::get('/dien-thoai', [ShopController::class, 'index'])->name('shop.index');

// Chi tiết sản phẩm
Route::get('/san-pham/{slug}', [CustomerProductController::class, 'show'])->name('shop.detail');


// ======================================================
// GIỎ HÀNG
// ======================================================

Route::prefix('gio-hang')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])->name('index');

    Route::post('/add', [CartController::class, 'add'])->name('add');

    Route::post('/update', [CartController::class, 'update'])->name('update');

    Route::post('/remove', [CartController::class, 'remove'])->name('remove');

});


// ======================================================
// THANH TOÁN
// ======================================================

Route::prefix('thanh-toan')->name('checkout.')->group(function () {

    Route::get('/', [CheckoutController::class, 'index'])->name('index');

    Route::post('/process', [CheckoutController::class, 'process'])->name('process');

    Route::post('/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('applyVoucher');

});


// ======================================================
// ADMIN ROUTES
// ======================================================

Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])
            ->name('settings');

        Route::post('/settings/update', [SettingController::class, 'update'])
            ->name('settings.update');

        // CRUD
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('products', AdminProductController::class);

// Đăng bán sản phẩm
Route::post(
    '/products/{id}/publish',
    [AdminProductController::class, 'publish']
)->name('products.publish');

// Dừng bán sản phẩm
Route::post(
    '/products/{id}/unpublish',
    [AdminProductController::class, 'unpublish']
)->name('products.unpublish');
        Route::resource('orders', OrderController::class);
        Route::resource('vouchers', VoucherController::class);

        // Variants
        Route::get(
            'products/{product_id}/variants',
            [VariantController::class, 'index']
        )->name('products.variants.index');

        Route::post(
            'products/{product_id}/variants',
            [VariantController::class, 'store']
        )->name('products.variants.store');

        Route::get(
            'products/{product_id}/variants/{variant_id}/edit',
            [VariantController::class, 'edit']
        )->name('products.variants.edit');

        Route::put(
            'products/{product_id}/variants/{variant_id}',
            [VariantController::class, 'update']
        )->name('products.variants.update');

        Route::delete(
            'products/{product_id}/variants/{variant_id}',
            [VariantController::class, 'destroy']
        )->name('products.variants.destroy');

        // Revenue
        Route::get(
            '/revenue',
            [RevenueController::class, 'index']
        )->name('revenue.index');

        // Search
        Route::get(
            '/search',
            [SearchController::class, 'index']
        )->name('search');

        // Customers
        Route::get(
            '/customers',
            [CustomerController::class, 'index']
        )->name('customers.index');

        Route::get(
            '/customers/{id}',
            [CustomerController::class, 'show']
        )->name('customers.show');

        Route::post(
            '/customers/{id}/toggle-status',
            [CustomerController::class, 'toggleStatus']
        )->name('customers.toggleStatus');
    });
