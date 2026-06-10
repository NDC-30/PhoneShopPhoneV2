<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();

        return view('admin.settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $request->validate([
            'fullname' => 'required|max:255',
            'phone' => 'nullable|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $user->fullname = $request->fullname;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {

            $path = $request->file('avatar')
                ->store('avatars', 'public');

            $user->avatar = $path;
        }

        $user->save();

        return back()->with(
            'success',
            'Cập nhật thông tin thành công!'
        );
    }
}