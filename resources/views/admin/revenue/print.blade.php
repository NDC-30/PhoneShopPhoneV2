<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Báo cáo doanh thu — {{ $periodLabel }}</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #222; margin: 28px; font-size: 13px; }
    .head { text-align: center; border-bottom: 3px solid #0d6efd; padding-bottom: 12px; margin-bottom: 18px; }
    .head h1 { margin: 0 0 4px; font-size: 22px; letter-spacing: .5px; }
    .head .shop { font-size: 15px; font-weight: bold; color: #0d6efd; }
    .head .meta { color: #666; font-size: 12px; margin-top: 4px; }
    .kpis { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .kpi { flex: 1; min-width: 120px; border: 1px solid #e2e2e2; border-radius: 8px; padding: 10px 12px; }
    .kpi .l { font-size: 10.5px; text-transform: uppercase; color: #888; font-weight: bold; }
    .kpi .v { font-size: 17px; font-weight: bold; margin-top: 4px; color: #0d6efd; }
    h2 { font-size: 14px; border-left: 4px solid #0d6efd; padding-left: 8px; margin: 22px 0 10px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    th, td { border: 1px solid #ddd; padding: 7px 9px; font-size: 12px; }
    th { background: #f4f6fb; text-align: left; }
    .r { text-align: right; }
    .c { text-align: center; }
    .bar-wrap { background: #eef; border-radius: 4px; height: 14px; width: 100%; position: relative; }
    .bar { background: #0d6efd; height: 14px; border-radius: 4px; }
    .foot { margin-top: 30px; display: flex; justify-content: space-between; font-size: 12px; color: #555; }
    .sign { text-align: center; width: 220px; }
    .sign .role { font-weight: bold; }
    .sign .space { height: 60px; }
    .btns { text-align: center; margin-bottom: 16px; }
    .btns button, .btns a { padding: 8px 18px; font-size: 13px; border-radius: 6px; border: none; cursor: pointer; margin: 0 4px; text-decoration: none; }
    .btns .print { background: #0d6efd; color: #fff; }
    .btns .close { background: #eee; color: #333; }
    @media print { .btns { display: none; } body { margin: 0; } }
    @php $maxMonth = max(array_map(fn($r) => $r['total'], $monthlyTable)) ?: 1; @endphp
</style>
</head>
<body>

    <div class="btns">
        <button class="print" onclick="window.print()">🖨️ In / Lưu PDF</button>
        <a class="close" href="{{ route('admin.revenue.index', request()->query()) }}">Đóng</a>
    </div>

    <div class="head">
        <div class="shop">PHONE.SHOP</div>
        <h1>BÁO CÁO DOANH THU</h1>
        <div class="meta">Kỳ báo cáo: <strong>{{ $periodLabel }}</strong> &nbsp;•&nbsp; Ngày xuất: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="kpis">
        <div class="kpi"><div class="l">Doanh thu trong kỳ</div><div class="v">{{ number_format($periodRevenue) }}đ</div></div>
        <div class="kpi"><div class="l">Đơn hoàn thành</div><div class="v">{{ $periodOrders }}</div></div>
        <div class="kpi"><div class="l">Giá trị TB/đơn</div><div class="v">{{ number_format($aov) }}đ</div></div>
        <div class="kpi"><div class="l">SP đã bán</div><div class="v">{{ $itemsSold }}</div></div>
        <div class="kpi"><div class="l">Hủy + Hoàn ({{ $cancelRate }}%)</div><div class="v">{{ $cancelled + $returned }}</div></div>
    </div>

    <h2>Doanh thu theo tháng (Năm {{ $year }})</h2>
    <table>
        <thead><tr><th style="width:90px">Tháng</th><th class="c" style="width:80px">Số đơn</th><th class="r" style="width:140px">Doanh thu</th><th>Biểu đồ</th></tr></thead>
        <tbody>
        @foreach($monthlyTable as $row)
            <tr>
                <td>Tháng {{ $row['month'] }}</td>
                <td class="c">{{ $row['orders'] }}</td>
                <td class="r">{{ number_format($row['total']) }}đ</td>
                <td><div class="bar-wrap"><div class="bar" style="width: {{ round($row['total'] / $maxMonth * 100) }}%"></div></div></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Sản phẩm bán chạy ({{ $periodLabel }})</h2>
    <table>
        <thead><tr><th style="width:40px">#</th><th>Sản phẩm</th><th class="c" style="width:90px">SL bán</th><th class="r" style="width:150px">Doanh thu</th></tr></thead>
        <tbody>
        @forelse($topProducts as $i => $tp)
            <tr><td>{{ $i + 1 }}</td><td>{{ $tp->name }}</td><td class="c">{{ $tp->qty }}</td><td class="r">{{ number_format($tp->revenue) }}đ</td></tr>
        @empty
            <tr><td colspan="4" class="c">Chưa có dữ liệu bán hàng</td></tr>
        @endforelse
        </tbody>
    </table>

    <h2>Cơ cấu phương thức thanh toán</h2>
    <table>
        <thead><tr><th>Hình thức</th><th class="c" style="width:90px">Số đơn</th><th class="r" style="width:150px">Doanh thu</th></tr></thead>
        <tbody>
        @php
            $pmLabel = fn($pm) => match(strtolower($pm ?? '')) {
                'cod' => 'Khi nhận hàng (COD)', 'cash' => 'Tiền mặt (tại quầy)',
                'vnpay' => 'VNPay', 'visa' => 'Thẻ Visa/Master', default => 'Chuyển khoản',
            };
        @endphp
        @forelse($payments as $p)
            <tr><td>{{ $pmLabel($p->payment_method) }}</td><td class="c">{{ $p->cnt }}</td><td class="r">{{ number_format($p->total) }}đ</td></tr>
        @empty
            <tr><td colspan="3" class="c">Chưa có dữ liệu</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="foot">
        <div class="sign"><div class="role">Người lập báo cáo</div><div class="space"></div><div>(Ký, ghi rõ họ tên)</div></div>
        <div class="sign"><div class="role">Quản lý</div><div class="space"></div><div>(Ký, ghi rõ họ tên)</div></div>
    </div>

    <script>
        // Tự mở hộp thoại in để lưu PDF
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    </script>
</body>
</html>
