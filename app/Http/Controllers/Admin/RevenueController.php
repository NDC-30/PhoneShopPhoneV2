<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index()
    {
        // 1. Tính toán các số liệu tổng quan (Chỉ tính đơn đã hoàn thành)
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');
        $totalOrders = Order::where('status', 'completed')->count();
        $todayRevenue = Order::where('status', 'completed')->whereDate('created_at', date('Y-m-d'))->sum('grand_total');
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // 2. Thống kê doanh thu theo 12 tháng trong năm hiện tại
        $monthlyData = Order::where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Đảm bảo tháng nào không có tiền thì hiển thị số 0 chứ không bị rỗng
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        // 3. Thống kê tỷ lệ Phương thức thanh toán (COD vs Chuyển khoản)
        $paymentStats = Order::where('status', 'completed')
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'totalOrders',
            'todayRevenue',
            'cancelledOrders',
            'chartData',
            'paymentStats'
        ));
    }
}