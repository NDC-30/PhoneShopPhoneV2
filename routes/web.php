<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ShopController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\VnpayController;

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

use App\Http\Controllers\AuthController;

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lien-he', [HomeController::class, 'contact'])->name('contact');

Route::get('/dien-thoai', [ShopController::class, 'index'])->name('shop.index');
Route::get('/san-pham/{slug}', [CustomerProductController::class, 'show'])->name('product.show');

Route::post('/voucher/xem-truoc', [CheckoutController::class, 'preview'])->name('voucher.preview');

Route::get('/thanh-toan/vnpay/ket-qua', [VnpayController::class, 'return'])->name('vnpay.return');
Route::get('/thanh-toan/vnpay/ipn', [VnpayController::class, 'ipn'])->name('vnpay.ipn');

Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/them', [CartController::class, 'add'])->name('add');
    Route::post('/cap-nhat', [CartController::class, 'update'])->name('update');
    Route::post('/xoa', [CartController::class, 'remove'])->name('remove');
    Route::post('/xoa-het', [CartController::class, 'clear'])->name('clear');
});

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/dang-nhap', [CustomerAuthController::class, 'login']);
    Route::get('/dang-ky', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/dang-ky', [CustomerAuthController::class, 'register']);
});

Route::post('/dang-xuat', [CustomerAuthController::class, 'logout'])->name('customer.logout');

Route::middleware('auth')->group(function () {
    Route::prefix('thanh-toan')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/dat-hang', [CheckoutController::class, 'place'])->name('place');
        Route::post('/voucher', [CheckoutController::class, 'applyVoucher'])->name('voucher');
        Route::post('/voucher/xoa', [CheckoutController::class, 'removeVoucher'])->name('voucher.remove');
        Route::get('/hoan-tat/{order}', [CheckoutController::class, 'success'])->name('success');
    });

    Route::get('/thanh-toan/vnpay/tao/{order}', [VnpayController::class, 'create'])->name('vnpay.create');

    Route::prefix('tai-khoan')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/', [AccountController::class, 'update'])->name('update');
        Route::put('/mat-khau', [AccountController::class, 'password'])->name('password');
        Route::get('/don-hang', [AccountController::class, 'orders'])->name('orders');
        Route::get('/don-hang/{order}', [AccountController::class, 'orderShow'])->name('order.show');
        Route::post('/don-hang/{order}/huy', [AccountController::class, 'cancelOrder'])->name('order.cancel');
    });
});

Route::prefix('admin')
    ->middleware([\App\Http\Middleware\EnsureAdmin::class])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('products', AdminProductController::class);

        Route::post('/products/{id}/publish', [AdminProductController::class, 'publish'])->name('products.publish');
        Route::post('/products/{id}/unpublish', [AdminProductController::class, 'unpublish'])->name('products.unpublish');

        Route::get('/orders/tracking/generate', [OrderController::class, 'generateTracking'])->name('orders.tracking.generate');
        Route::resource('orders', OrderController::class);
        Route::resource('vouchers', VoucherController::class);

        Route::get('products/{product_id}/variants', [VariantController::class, 'index'])->name('products.variants.index');
        Route::post('products/{product_id}/variants', [VariantController::class, 'store'])->name('products.variants.store');
        Route::get('products/{product_id}/variants/{variant_id}/edit', [VariantController::class, 'edit'])->name('products.variants.edit');
        Route::put('products/{product_id}/variants/{variant_id}', [VariantController::class, 'update'])->name('products.variants.update');
        Route::delete('products/{product_id}/variants/{variant_id}', [VariantController::class, 'destroy'])->name('products.variants.destroy');

        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');
        Route::get('/revenue/pdf', [RevenueController::class, 'printReport'])->name('revenue.pdf');

        Route::get('/search', [SearchController::class, 'index'])->name('search');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
    });