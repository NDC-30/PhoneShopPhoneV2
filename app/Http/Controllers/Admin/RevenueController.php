<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.revenue.index', $this->buildReport($request));
    }

    // Bản in / xuất PDF (trang đứng riêng, tự bật hộp thoại in -> Lưu thành PDF)
    public function printReport(Request $request)
    {
        return view('admin.revenue.print', $this->buildReport($request));
    }

    /** Tính toàn bộ số liệu cho báo cáo theo kỳ Năm -> Tháng -> Ngày */
    private function buildReport(Request $request): array
    {
        $year  = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', 0);  // 0 = cả năm
        $day   = (int) $request->input('day', 0);     // 0 = cả tháng

        $minYear = (int) (Order::whereNotNull('created_at')->min(DB::raw('YEAR(created_at)')) ?: now()->year);
        $years   = range(now()->year, min($minYear, now()->year));

        // Hàm áp điều kiện kỳ cho query Order
        $applyScope = function ($q, string $col = 'created_at') use ($year, $month, $day) {
            $q->whereYear($col, $year);
            if ($month > 0) $q->whereMonth($col, $month);
            if ($month > 0 && $day > 0) $q->whereDay($col, $day);
            return $q;
        };

        // ===== KPI trong kỳ (đơn đã hoàn thành) =====
        $completed = $applyScope(Order::where('status', 'completed'));
        $periodRevenue = (clone $completed)->sum('grand_total');
        $periodOrders  = (clone $completed)->count();
        $aov           = $periodOrders > 0 ? $periodRevenue / $periodOrders : 0;

        $cancelled = $applyScope(Order::where('status', 'cancelled'))->count();
        $returned  = $applyScope(Order::where('status', 'returned'))->count();
        $totalAllStatus = $applyScope(Order::query())->count();
        $cancelRate = $totalAllStatus > 0 ? round(($cancelled + $returned) / $totalAllStatus * 100, 1) : 0;

        // Số sản phẩm đã bán trong kỳ
        $detailBase = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->where('orders.status', 'completed');
        $applyScope($detailBase, 'orders.created_at');
        $itemsSold = (clone $detailBase)->sum('order_details.quantity');

        // ===== Biểu đồ doanh thu theo kỳ =====
        $chartBase = $applyScope(Order::where('status', 'completed'));
        if ($month > 0 && $day > 0) {
            $labels    = ["Ngày $day/$month/$year"];
            $chartData = [(float) (clone $chartBase)->sum('grand_total')];
            $periodLabel = "Ngày $day/$month/$year";
        } elseif ($month > 0) {
            $rows = (clone $chartBase)->selectRaw('DAY(created_at) as k, SUM(grand_total) as total')
                ->groupBy('k')->pluck('total', 'k')->toArray();
            $days = Carbon::create($year, $month, 1)->daysInMonth;
            $labels = [];
            $chartData = [];
            for ($i = 1; $i <= $days; $i++) { $labels[] = (string) $i; $chartData[] = (float) ($rows[$i] ?? 0); }
            $periodLabel = "Tháng $month/$year";
        } else {
            $rows = (clone $chartBase)->selectRaw('MONTH(created_at) as k, SUM(grand_total) as total')
                ->groupBy('k')->pluck('total', 'k')->toArray();
            $labels = [];
            $chartData = [];
            for ($i = 1; $i <= 12; $i++) { $labels[] = 'Tháng ' . $i; $chartData[] = (float) ($rows[$i] ?? 0); }
            $periodLabel = "Năm $year";
        }

        // ===== Bảng doanh thu 12 tháng của năm (tổng quan) =====
        $monthAgg = Order::where('status', 'completed')->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, COUNT(*) as cnt, SUM(grand_total) as total')
            ->groupBy('m')->get()->keyBy('m');
        $monthlyTable = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyTable[] = [
                'month'  => $i,
                'orders' => (int) ($monthAgg[$i]->cnt ?? 0),
                'total'  => (float) ($monthAgg[$i]->total ?? 0),
            ];
        }

        // ===== Top sản phẩm bán chạy trong kỳ =====
        $topProducts = (clone $detailBase)
            ->join('variants', 'order_details.variant_id', '=', 'variants.variant_id')
            ->join('products', 'variants.product_id', '=', 'products.product_id')
            ->select('products.name',
                DB::raw('SUM(order_details.quantity) as qty'),
                DB::raw('SUM(order_details.subtotal) as revenue'))
            ->groupBy('products.product_id', 'products.name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        // ===== Cơ cấu phương thức thanh toán trong kỳ =====
        $payments = $applyScope(Order::where('status', 'completed'))
            ->select('payment_method', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_method')->get();

        return compact(
            'year', 'month', 'day', 'years',
            'periodLabel', 'periodRevenue', 'periodOrders', 'aov',
            'cancelled', 'returned', 'cancelRate', 'itemsSold',
            'labels', 'chartData', 'monthlyTable', 'topProducts', 'payments'
        );
    }
}
