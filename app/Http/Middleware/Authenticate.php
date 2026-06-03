<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Trang chuyển hướng khi user CHƯA đăng nhập.
     * - Khu /admin  -> trang login admin (route 'login')
     * - Còn lại     -> trang login khách (route 'customer.login')
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        return $request->is('admin', 'admin/*')
            ? route('login')            // login admin (/admin/login)
            : route('customer.login');  // login khách (/dang-nhap)
    }
}
