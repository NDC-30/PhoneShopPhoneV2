<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('customer.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        return back()->with('success', 'Đã thêm vào giỏ hàng');
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Đã cập nhật giỏ hàng');
    }

    public function remove(Request $request)
    {
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }
}