<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->input('keyword'));

        if (empty($keyword)) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm!');
        }

        // 1. Lục tìm trong bảng Sản Phẩm (Tìm theo tên)
        $products = Product::where('name', 'like', "%{$keyword}%")->get();

        // 2. Lục tìm trong bảng Đơn Hàng (Mã đơn, Tên khách, SĐT)
        $orders = Order::where('order_number', 'like', "%{$keyword}%")
                       ->orWhere('receiver_name', 'like', "%{$keyword}%")
                       ->orWhere('receiver_phone', 'like', "%{$keyword}%")
                       ->orderBy('created_at', 'desc')
                       ->get();

        return view('admin.search.index', compact('keyword', 'products', 'orders'));
    }
}