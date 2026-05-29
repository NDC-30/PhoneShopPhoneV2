<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Hiển thị form login
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    // Xử lý login
    public function login(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Kiểm tra tài khoản
        $user = User::where('email', $request->email)
                    ->where('is_active', 1)
                    ->first();

        // Nếu không tồn tại user
        if (!$user) {
            return back()->withErrors([
                'email' => 'Tài khoản không tồn tại',
            ]);
        }

        // Thử đăng nhập
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {

            // Tạo session mới
            $request->session()->regenerate();

            // Chuyển vào admin
            return redirect('/admin');
        }

        // Sai mật khẩu
        return back()->withErrors([
            'password' => 'Sai mật khẩu',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    // Update tài khoản
    public function updateSettings(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $request->validate([
        'fullname' => 'required',
        'password' => 'nullable|confirmed|min:6',
    ]);

    // Update tên
    $user->fullname = $request->fullname;

    // Nếu có nhập mật khẩu mới
    if ($request->password) {

        $user->password = Hash::make($request->password);
    }

    $user->save();

    return back()->with(
        'success',
        'Cập nhật tài khoản thành công'
    );
}
}
