<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. SỐ LIỆU TỔNG QUAN
        $totalProducts  = Product::count();
        $totalOrders    = Order::whereIn('status', ['pending', 'processing'])->count();
        $totalRevenue   = Order::where('status', 'completed')->sum('grand_total');
        $totalCustomers = User::where('role', 'customer')->count();

        $monthRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('grand_total');

        // 2. TOP 5 SẢN PHẨM BÁN CHẠY (cho biểu đồ cột ngang + danh sách)
        $bestSellingProducts = DB::table('order_details')
            ->join('variants', 'order_details.variant_id', '=', 'variants.variant_id')
            ->join('products', 'variants.product_id', '=', 'products.product_id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();
        $topLabels = $bestSellingProducts->pluck('name');
        $topData   = $bestSellingProducts->pluck('sold');

        // 3. 5 ĐƠN HÀNG MỚI NHẤT
        $recentOrders = Order::orderBy('order_id', 'desc')->take(5)->get();

        // 4. BỘ LỌC DOANH THU: Năm -> Tháng -> Ngày
        $year  = (int) request('year', now()->year);
        $month = (int) request('month', 0);   // 0 = cả năm
        $day   = (int) request('day', 0);      // 0 = cả tháng

        $minYear = (int) (Order::whereNotNull('created_at')->min(DB::raw('YEAR(created_at)')) ?: now()->year);
        $years   = range(now()->year, min($minYear, now()->year));

        $base = Order::where('status', 'completed')->whereYear('created_at', $year);

        if ($month > 0 && $day > 0) {
            // Một ngày cụ thể
            $total = (clone $base)->whereMonth('created_at', $month)->whereDay('created_at', $day)->sum('grand_total');
            $labels     = ["Ngày $day/$month/$year"];
            $chartData  = [(float) $total];
            $chartTitle = "Doanh thu ngày $day/$month/$year";
        } elseif ($month > 0) {
            // Các ngày trong tháng (đủ ngày, ngày nào không có = 0)
            $rows = (clone $base)->whereMonth('created_at', $month)
                ->selectRaw('DAY(created_at) as d, SUM(grand_total) as total')
                ->groupBy('d')->pluck('total', 'd')->toArray();
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
            $labels = [];
            $chartData = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[]    = (string) $i;
                $chartData[] = (float) ($rows[$i] ?? 0);
            }
            $chartTitle = "Doanh thu theo ngày — Tháng $month/$year";
        } else {
            // Đủ 12 tháng (tháng nào không có = 0)
            $rows = (clone $base)->selectRaw('MONTH(created_at) as m, SUM(grand_total) as total')
                ->groupBy('m')->pluck('total', 'm')->toArray();
            $labels = [];
            $chartData = [];
            for ($i = 1; $i <= 12; $i++) {
                $labels[]    = 'Tháng ' . $i;
                $chartData[] = (float) ($rows[$i] ?? 0);
            }
            $chartTitle = "Doanh thu 12 tháng — Năm $year";
        }

        // Số ngày của tháng đang chọn (để đổ dropdown ngày)
        $daysInSelectedMonth = $month > 0 ? Carbon::create($year, $month, 1)->daysInMonth : 31;

        return view('admin.dashboard.index', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'monthRevenue',
            'totalCustomers',
            'recentOrders',
            'bestSellingProducts',
            'topLabels',
            'topData',
            'labels',
            'chartData',
            'chartTitle',
            'year',
            'years',
            'month',
            'day',
            'daysInSelectedMonth'
        ));
    }
}
