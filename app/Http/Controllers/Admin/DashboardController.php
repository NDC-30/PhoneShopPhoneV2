<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. LẤY SỐ LIỆU SẢN PHẨM
        $totalProducts = Product::count();
        
        // 2. TÍNH TOÁN ĐƠN HÀNG VÀ DOANH THU (Chuẩn theo bảng orders)
        // Số đơn hàng đang cần xử lý (Pending & Processing)
        $totalOrders = Order::whereIn('status', ['pending', 'processing'])->count();
        
        // Doanh thu (Chỉ cộng tiền những đơn Đã hoàn thành)
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');

        // 3. ĐẾM KHÁCH HÀNG (Lọc role 'customer')
        $totalCustomers = User::where('role', 'customer')->count();

        // 4. LẤY TOP SẢN PHẨM NỔI BẬT
        $topProducts = Product::withCount('variants')
            ->orderBy('variants_count', 'desc')
            ->take(3)
            ->get();

        // 5. LẤY 5 ĐƠN HÀNG MỚI NHẤT
        $recentOrders = Order::orderBy('order_id', 'desc')->take(5)->get();

        // 6. DỮ LIỆU BIỂU ĐỒ 12 THÁNG
        $monthlyData = Order::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        return view('admin.dashboard.index', compact(
            'totalProducts', 
            'totalOrders', 
            'totalRevenue', 
            'totalCustomers',
            'topProducts',
            'recentOrders',
            'chartData'
        ));
    }
}