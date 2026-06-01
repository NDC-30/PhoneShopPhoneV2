<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order; 
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
                         ->orderBy('created_at', 'desc')
                         ->get();
        
        foreach ($customers as $customer) {
            $customer->order_count = Order::where('user_id', $customer->getKey())->count();
        }

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::findOrFail($id);
        
        $orders = Order::where('user_id', $customer->getKey())
                       ->orderBy('created_at', 'desc')
                       ->get();

        return view('admin.customers.show', compact('customer', 'orders'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        $message = $user->status == 1 ? 'Đã mở khóa tài khoản thành công!' : 'Đã khóa tài khoản khách hàng thành công!';
        return redirect()->back()->with('success', $message);
    }
}