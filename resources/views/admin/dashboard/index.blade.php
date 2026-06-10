@extends('admin.layouts.master')
@section('title', 'Bảng Điều Khiển - Phone Shop')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">

    {{-- NÚT THAO TÁC NHANH --}}
    <div class="card shadow-sm border-0 p-3 mb-4">
        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-lightning-charge-fill text-warning"></i> Thao Tác Nhanh</h6>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary"><i class="bi bi-cart-plus"></i> Tạo đơn tại quầy</a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="bi bi-plus-circle"></i> Thêm sản phẩm</a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark"><i class="bi bi-box-seam"></i> Quản lý đơn hàng</a>
            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-dark"><i class="bi bi-ticket-perforated"></i> Quản lý voucher</a>
            <a href="{{ route('admin.revenue.index') }}" class="btn btn-outline-primary"><i class="bi bi-graph-up"></i> Báo cáo doanh thu</a>
        </div>
    </div>

    {{-- THẺ SỐ LIỆU --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl">
            <div class="card shadow-sm border-0 h-100 p-3">
                <span class="text-muted small text-uppercase fw-bold">Tổng Doanh Thu</span>
                <h4 class="fw-bold my-2 text-primary">{{ number_format($totalRevenue) }}đ</h4>
                <span class="text-success small fw-bold"><i class="bi bi-currency-dollar"></i> Đơn đã hoàn thành</span>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card shadow-sm border-0 h-100 p-3">
                <span class="text-muted small text-uppercase fw-bold">Doanh Thu Tháng Này</span>
                <h4 class="fw-bold my-2 text-dark">{{ number_format($monthRevenue) }}đ</h4>
                <span class="text-muted small fw-bold"><i class="bi bi-calendar-month"></i> Tháng {{ now()->month }}/{{ now()->year }}</span>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card shadow-sm border-0 h-100 p-3">
                <span class="text-muted small text-uppercase fw-bold">Đơn Cần Xử Lý</span>
                <h4 class="fw-bold my-2 text-info">{{ $totalOrders }}</h4>
                <span class="text-info small fw-bold"><i class="bi bi-box-seam"></i> Chờ xác nhận/đóng gói</span>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card shadow-sm border-0 h-100 p-3">
                <span class="text-muted small text-uppercase fw-bold">Sản Phẩm</span>
                <h4 class="fw-bold my-2 text-warning">{{ $totalProducts }}</h4>
                <span class="text-warning small fw-bold"><i class="bi bi-phone"></i> Đang kinh doanh</span>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card shadow-sm border-0 h-100 p-3">
                <span class="text-muted small text-uppercase fw-bold">Khách Hàng</span>
                <h4 class="fw-bold my-2 text-success">{{ $totalCustomers }}</h4>
                <span class="text-success small fw-bold"><i class="bi bi-people"></i> Thành viên</span>
            </div>
        </div>
    </div>

    {{-- BỘ LỌC: NĂM -> THÁNG -> NGÀY --}}
    <div class="card shadow-sm border-0 p-3 mb-4">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-bold mb-1">Năm</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-bold mb-1">Tháng</label>
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <option value="0" {{ $month == 0 ? 'selected' : '' }}>Cả năm</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small fw-bold mb-1">Ngày</label>
                <select name="day" class="form-select" onchange="this.form.submit()" {{ $month == 0 ? 'disabled' : '' }}>
                    <option value="0" {{ $day == 0 ? 'selected' : '' }}>Cả tháng</option>
                    @for($d = 1; $d <= $daysInSelectedMonth; $d++)
                        <option value="{{ $d }}" {{ $day == $d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            </div>
        </form>
    </div>

    {{-- BIỂU ĐỒ DOANH THU --}}
    <div class="card shadow-sm border-0 p-3 mb-4">
        <h6 class="fw-bold text-secondary mb-3">{{ $chartTitle }}</h6>
        <div style="height: 320px; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- TOP 5 BÁN CHẠY: biểu đồ cột ngang + danh sách --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Top 5 Sản Phẩm Bán Chạy</h6>
                    @if($bestSellingProducts->count())
                        <div style="height: {{ max(170, $bestSellingProducts->count() * 72) }}px; position: relative;">
                            <canvas id="topBarChart"></canvas>
                        </div>
                    @else
                        <div class="text-muted fst-italic py-5 text-center">Chưa có dữ liệu bán hàng</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Danh Sách Top 5 Bán Chạy</h6>
                    <div class="list-group">
                        @forelse($bestSellingProducts as $index => $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div><strong>#{{ $index + 1 }}</strong> {{ $product->name }}</div>
                                <span class="badge bg-success">{{ $product->sold }} SP</span>
                            </div>
                        @empty
                            <div class="text-muted">Chưa có dữ liệu bán hàng</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ĐƠN HÀNG GẦN ĐÂY --}}
    <div class="card shadow-sm border-0 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-secondary mb-0">Đơn Hàng Gần Đây</h6>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_number ?? '#' . $order->order_id }}</strong></td>
                            <td>{{ $order->receiver_name ?? 'Khách vãng lai' }}</td>
                            <td>{{ optional($order->created_at)->format('d/m/Y') }}</td>
                            <td class="text-danger fw-bold">{{ number_format($order->grand_total) }}đ</td>
                            <td>
                                @if ($order->status == 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                @elseif($order->status == 'returned')
                                    <span class="badge" style="background:#7c3aed;color:#fff">Hoàn trả</span>
                                @elseif($order->status == 'shipping')
                                    <span class="badge bg-primary">Đang giao</span>
                                @else
                                    <span class="badge bg-info text-dark">Đã xác nhận</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Hệ thống chưa phát sinh đơn hàng nào!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (window.ChartDataLabels) Chart.register(window.ChartDataLabels);

    // Biểu đồ doanh thu (theo tháng/ngày tùy bộ lọc)
    const rev = document.getElementById('revenueChart');
    if (rev) {
        new Chart(rev.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: @json($chartData),
                    backgroundColor: 'rgba(13,110,253,0.8)',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: { display: false },
                    tooltip: { callbacks: { label: c => new Intl.NumberFormat('vi-VN').format(c.raw) + ' đ' } }
                },
                scales: { y: { beginAtZero: true, ticks: { callback: v => new Intl.NumberFormat('vi-VN').format(v) } } }
            }
        });
    }

    // Top 5 bán chạy — cột NGANG
    const bar = document.getElementById('topBarChart');
    if (bar) {
        new Chart(bar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($topLabels),
                datasets: [{
                    label: 'Đã bán',
                    data: @json($topData),
                    backgroundColor: '#b8501f',
                    borderRadius: 5,
                    categoryPercentage: 0.8,
                    barPercentage: 0.95,
                    maxBarThickness: 54
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { right: 48 } },
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'end', color: '#5c3317', font: { weight: 'bold' },
                        formatter: v => new Intl.NumberFormat('vi-VN').format(v) + ' SP'
                    },
                    tooltip: { callbacks: { label: c => c.raw + ' sản phẩm' } }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { ticks: { autoSkip: false, font: { size: 12 } } }
                }
            }
        });
    }
});
</script>
@endsection
