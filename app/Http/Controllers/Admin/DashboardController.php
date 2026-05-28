<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. LẤY SỐ LIỆU SẢN PHẨM (Chuẩn 100%)
        $totalProducts = Product::count();
        
        // 2. TÍNH TOÁN ĐƠN HÀNG VÀ DOANH THU (Chống sập)
        $totalOrders = 0;
        $totalRevenue = 0;
        
        if (Schema::hasTable('orders')) {
            $totalOrders = DB::table('orders')->count();
            
            // Dò tìm tên cột tổng tiền
            if (Schema::hasColumn('orders', 'total_price')) {
                $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total_price');
            } elseif (Schema::hasColumn('orders', 'total')) {
                $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('total');
            } elseif (Schema::hasColumn('orders', 'tong_tien')) {
                $totalRevenue = DB::table('orders')->where('status', 'completed')->sum('tong_tien');
            }
        }

        // 3. ĐẾM KHÁCH HÀNG
        $totalCustomers = 0;
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            $totalCustomers = DB::table('users')->where('role', 'customer')->count();
        } elseif (Schema::hasTable('users')) {
            $totalCustomers = DB::table('users')->count();
        }

        // 4. LẤY TOP SẢN PHẨM NỔI BẬT
        $topProducts = Product::withCount('variants')
            ->orderBy('variants_count', 'desc')
            ->take(3)
            ->get();

        // 5. LẤY ĐƠN HÀNG MỚI NHẤT (Đã fix lỗi tìm cột ID)
        $recentOrders = [];
        if (Schema::hasTable('orders')) {
            $query = DB::table('orders');
            
            // Dò xem cột khóa chính hoặc cột thời gian tên là gì để sắp xếp
            if (Schema::hasColumn('orders', 'id')) {
                $query->orderBy('id', 'desc');
            } elseif (Schema::hasColumn('orders', 'order_id')) {
                $query->orderBy('order_id', 'desc');
            } elseif (Schema::hasColumn('orders', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            }
            
            $recentOrders = $query->take(5)->get();
        }

        // 6. DỮ LIỆU BIỂU ĐỒ (Tạm thời fix cứng đợi module thống kê)
        $chartData = [12000, 19000, 15000, 25000, 22000, 45000]; 

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