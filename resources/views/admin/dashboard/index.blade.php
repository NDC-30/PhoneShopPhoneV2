@extends('admin.layouts.master')
@section('title', 'Bảng Điều Khiển - Phone Shop')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Doanh Thu</span>
                        <h4 class="fw-bold my-2 text-primary">{{ number_format($totalRevenue) }}đ</h4>
                        @if($totalRevenue > 0)
                            <span class="text-success small fw-bold"><i class="bi bi-graph-up"></i> Đang sinh lời</span>
                        @else
                            <span class="text-muted small fst-italic">Chưa phát sinh giao dịch</span>
                        @endif
                    </div>
                    <div class="bg-light-primary text-primary rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background-color:#eef2ff;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Đơn Hàng</span>
                        <h4 class="fw-bold my-2">{{ $totalOrders }}</h4>
                        @if($totalOrders > 0)
                            <span class="text-info small fw-bold"><i class="bi bi-box-seam"></i> Đang xử lý</span>
                        @else
                            <span class="text-muted small fst-italic">Chờ đơn hàng đầu tiên</span>
                        @endif
                    </div>
                    <div class="text-info rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background-color:#e0f2fe;">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Sản Phẩm</span>
                        <h4 class="fw-bold my-2 text-warning">{{ $totalProducts }}</h4>
                        @if($totalProducts > 0)
                            <span class="text-warning small fw-bold"><i class="bi bi-check2-circle"></i> Sẵn sàng kinh doanh</span>
                        @else
                            <span class="text-danger small fst-italic">Kho đang trống!</span>
                        @endif
                    </div>
                    <div class="text-warning rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background-color:#fef3c7;">
                        <i class="bi bi-phone"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Khách Hàng</span>
                        <h4 class="fw-bold my-2 text-success">{{ $totalCustomers }}</h4>
                        @if($totalCustomers > 0)
                            <span class="text-success small fw-bold"><i class="bi bi-person-lines-fill"></i> Đã có thành viên</span>
                        @else
                            <span class="text-muted small fst-italic">Chưa có ai đăng ký</span>
                        @endif
                    </div>
                    <div class="text-success rounded p-3 fs-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px; background-color:#dcfce7;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="fw-bold text-secondary mb-3">Doanh Thu Theo Tháng</h6>
                <div style="height: 300px; position: relative;">
                    @if($totalRevenue > 0)
                        <canvas id="revenueChart"></canvas>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted fst-italic border rounded bg-light">
                            Hệ thống chưa có đủ dữ liệu doanh thu để vẽ biểu đồ!
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 h-100">
                <h6 class="fw-bold text-secondary mb-3">Sản Phẩm Có Nhiều Phiên Bản</h6>
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $index => $prod)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:24px; height:24px;">{{ $index + 1 }}</span>
                            <div>
                                <h6 class="mb-0 small fw-bold text-dark">{{ $prod->name }}</h6>
                                <small class="text-muted">{{ $prod->variants_count }} phiên bản cấu hình</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">Chưa có sản phẩm nào!</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-secondary mb-0">Đơn Hàng Gần Đây</h6>
            <a href="#" class="btn btn-sm btn-primary">Xem tất cả</a>
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
                        <td><strong>#{{ $order->id ?? $order->order_id ?? 'N/A' }}</strong></td>
                        <td>{{ $order->customer_name ?? 'Khách vãng lai' }}</td>
                        <td>{{ isset($order->created_at) ? date('d/m/Y', strtotime($order->created_at)) : 'N/A' }}</td>
                        <td class="text-danger fw-bold">{{ number_format($order->total_price ?? $order->total ?? $order->tong_tien ?? 0) }}đ</td>
                        <td>
                            @if(isset($order->status) && $order->status == 'completed')
                                <span class="badge bg-success">Thành công</span>
                            @elseif(isset($order->status) && $order->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-secondary">Khác</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Hệ thống chưa phát sinh đơn hàng nào!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartElement = document.getElementById('revenueChart');
        if(chartElement) {
            const ctx = chartElement.getContext('2d');
            const dbData = {!! json_encode($chartData ?? [0,0,0,0,0,0]) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: dbData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endsection