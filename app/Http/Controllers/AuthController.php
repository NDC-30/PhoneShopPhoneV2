<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login'); // Nhớ trỏ đúng file view login của admin
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('admin')->attempt($cred)) {
            
            if (Auth::guard('admin')->user()->role !== 'admin') {
                Auth::guard('admin')->logout();
                return back()->with('error', 'Tài khoản không có quyền truy cập.');
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Sai email hoặc mật khẩu.');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}