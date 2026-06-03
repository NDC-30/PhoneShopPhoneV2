<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $bestSellingProducts = DB::table('order_details')
            ->join('variants', 'order_details.variant_id', '=', 'variants.variant_id')
            ->join('products', 'variants.product_id', '=', 'products.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(order_details.quantity) as sold')
            )
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        $pieLabels = $bestSellingProducts->pluck('name');
        $pieData = $bestSellingProducts->pluck('sold');
        // 6. DỮ LIỆU BIỂU ĐỒ 12 THÁNG
        $type = request('type', 'month');
        $chartTitle = match ($type) {
            'day' => 'Doanh Thu Theo Ngày',
            'year' => 'Doanh Thu Theo Năm',
            default => 'Doanh Thu Theo Tháng'
        };
        $from = request('from');
        $to = request('to');

        $query = Order::where('status', 'completed');

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($type == 'day') {

            $data = $query
    ->selectRaw("
        DATE(CONVERT_TZ(created_at,'+00:00','+07:00')) as label,
        SUM(grand_total) as total
    ")
    ->groupBy('label')
    ->orderBy('label')
    ->get();
            $labels = $data->pluck('label');
            $chartData = $data->pluck('total');
        } elseif ($type == 'month') {

            $data = $query
                ->selectRaw('MONTH(created_at) as label, SUM(grand_total) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $labels = [];
            $chartData = [];

            foreach ($data as $item) {
                $labels[] = 'Tháng ' . $item->label;
                $chartData[] = $item->total;
            }
        } else {

            $data = $query
                ->selectRaw('YEAR(created_at) as label, SUM(grand_total) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $labels = $data->pluck('label');
            $chartData = $data->pluck('total');
        }


        return view('admin.dashboard.index', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'totalCustomers',
            'topProducts',
            'recentOrders',
            'chartData',
            'labels',
            'type',
            'chartTitle',
            'bestSellingProducts',
            'pieLabels',
            'pieData',
        ));
    }
}
