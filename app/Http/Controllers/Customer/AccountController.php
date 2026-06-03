<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    // Trang thông tin khách hàng (tên, sđt, địa chỉ)
    public function index()
    {
        $user = auth()->user();
        return view('customer.account.index', compact('user'));
    }

    // Cập nhật thông tin cá nhân
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'fullname' => 'required|string|max:100',
            'phone'    => 'required|string|max:20|unique:users,phone,' . $user->user_id . ',user_id',
            'address'  => 'nullable|string|max:255',
        ]);

        $user->update($data);
        return back()->with('success', 'Đã cập nhật thông tin tài khoản.');
    }

    // Đổi mật khẩu
    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Đã đổi mật khẩu.');
    }

    // Lịch sử đơn hàng
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->withCount('details')
            ->latest('order_id')->paginate(8);

        return view('customer.account.orders', compact('orders'));
    }

    // Chi tiết một đơn
    public function orderShow(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load('details.variant.product', 'shipping', 'payment', 'voucher');

        return view('customer.account.order-show', compact('order'));
    }
}
