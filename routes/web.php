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
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\VnpayController;

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
// AUTH ADMIN 
// =========================
use App\Http\Controllers\AuthController;

// Trang login admin
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
// Xử lý login admin
Route::post('/admin/login', [AuthController::class, 'login']);
// Logout admin
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ======================================================
// CUSTOMER ROUTES
// ======================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lien-he', [HomeController::class, 'contact'])->name('contact');

Route::get('/dien-thoai', [ShopController::class, 'index'])->name('shop.index');
Route::get('/san-pham/{slug}', [CustomerProductController::class, 'show'])->name('product.show');

// Xem trước voucher (công khai - trang chi tiết sản phẩm)
Route::post('/voucher/xem-truoc', [CheckoutController::class, 'preview'])->name('voucher.preview');

// VNPay trả kết quả (công khai — VNPay redirect/gọi vào đây)
Route::get('/thanh-toan/vnpay/ket-qua', [VnpayController::class, 'return'])->name('vnpay.return');
Route::get('/thanh-toan/vnpay/ipn', [VnpayController::class, 'ipn'])->name('vnpay.ipn');

// Giỏ hàng (cho cả khách chưa đăng nhập)
Route::prefix('gio-hang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/them', [CartController::class, 'add'])->name('add');
    Route::post('/cap-nhat', [CartController::class, 'update'])->name('update');
    Route::post('/xoa', [CartController::class, 'remove'])->name('remove');
    Route::post('/xoa-het', [CartController::class, 'clear'])->name('clear');
});

// Đăng nhập / đăng ký KHÁCH (tên riêng để không đụng admin)
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
    Route::post('/dang-nhap', [CustomerAuthController::class, 'login']);
    Route::get('/dang-ky', [CustomerAuthController::class, 'showRegister'])->name('customer.register');
    Route::post('/dang-ky', [CustomerAuthController::class, 'register']);
});
Route::post('/dang-xuat', [CustomerAuthController::class, 'logout'])->name('customer.logout');

// Khu vực khách cần đăng nhập (đặt hàng, tài khoản)
Route::middleware('auth')->group(function () {

    Route::prefix('thanh-toan')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/dat-hang', [CheckoutController::class, 'place'])->name('place');
        Route::post('/voucher', [CheckoutController::class, 'applyVoucher'])->name('voucher');
        Route::post('/voucher/xoa', [CheckoutController::class, 'removeVoucher'])->name('voucher.remove');
        Route::get('/hoan-tat/{order}', [CheckoutController::class, 'success'])->name('success');
    });

    // Tạo phiên thanh toán VNPay cho 1 đơn (cần đăng nhập + đúng chủ đơn)
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


// ======================================================
// ADMIN ROUTES (giữ nguyên của bạn)========================================
Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

        // CRUD
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('products', AdminProductController::class);

        // Đăng bán / dừng bán sản phẩm
        Route::post('/products/{id}/publish', [AdminProductController::class, 'publish'])->name('products.publish');
        Route::post('/products/{id}/unpublish', [AdminProductController::class, 'unpublish'])->name('products.unpublish');

        Route::resource('orders', OrderController::class);
        Route::resource('vouchers', VoucherController::class);

        // Variants
        Route::get('products/{product_id}/variants', [VariantController::class, 'index'])->name('products.variants.index');
        Route::post('products/{product_id}/variants', [VariantController::class, 'store'])->name('products.variants.store');
        Route::get('products/{product_id}/variants/{variant_id}/edit', [VariantController::class, 'edit'])->name('products.variants.edit');
        Route::put('products/{product_id}/variants/{variant_id}', [VariantController::class, 'update'])->name('products.variants.update');
        Route::delete('products/{product_id}/variants/{variant_id}', [VariantController::class, 'destroy'])->name('products.variants.destroy');

        // Revenue
        Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');

        // Search
        Route::get('/search', [SearchController::class, 'index'])->name('search');

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
    });
