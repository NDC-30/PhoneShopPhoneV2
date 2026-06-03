<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Chia sẻ số lượng sản phẩm trong giỏ cho header trên mọi trang
        View::composer('partials.header', function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });
    }
}
