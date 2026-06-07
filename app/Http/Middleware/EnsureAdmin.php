<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (!$user || $user->role !== 'admin') {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập tài khoản quản trị.');
        }

        return $next($request);
    }
}