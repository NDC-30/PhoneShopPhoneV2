@extends('admin.layouts.master')
@section('title', 'Báo Cáo Doanh Thu')
@section('page_title', '📊 Phân Tích & Báo Cáo Doanh Thu')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white text-center p-3 rounded-3">
                <div class="small text-uppercase fw-bold opacity-75">Tổng Doanh Thu</div>
                <h3 class="fw-bold my-2">{{ number_format($totalRevenue) }}đ</h3>
                <div class="small"><i class="bi bi-wallet2"></i> Toàn hệ thống</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white text-center p-3 rounded-3">
                <div class="small text-uppercase fw-bold opacity-75">Doanh Thu Hôm Nay</div>
                <h3 class="fw-bold my-2">{{ number_format($todayRevenue) }}đ</h3>
                <div class="small"><i class="bi bi-graph-up-arrow"></i> Tiền về trong ngày</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white text-center p-3 rounded-3">
                <div class="small text-uppercase fw-bold opacity-75">Đơn Hàng Thành Công</div>
                <h3 class="fw-bold my-2">{{ $totalOrders }} đơn</h3>
                <div class="small"><i class="bi bi-cart-check-fill"></i> Đã giao hoàn tất</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white text-center p-3 rounded-3">
                <div class="small text-uppercase fw-bold opacity-75">Đơn Hàng Bị Hủy / Bom</div>
                <h3 class="fw-bold my-2">{{ $cancelledOrders }} đơn</h3>
                <div class="small"><i class="bi bi-trash3-fill"></i> Đã hoàn lại kho</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-calendar3-event text-primary"></i> Biểu đồ doanh thu theo các tháng (Năm {{ date('Y') }})</div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-pie-chart-fill text-success"></i> Phương thức thanh toán</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 250px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Cấu hình vẽ Biểu đồ Cột (Doanh thu 12 tháng)
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (VND)',
                data: @json($chartData), // Truyền mảng dữ liệu từ PHP sang JS
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 2. Cấu hình vẽ Biểu đồ Tròn (Cơ cấu thanh toán)
    const ctxPayment = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctxPayment, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentStats->pluck('payment_method')) !!},
            datasets: [{
                data: @json($paymentStats->pluck('count')),
                backgroundColor: ['#6c757d', '#0dcaf0', '#ffc107'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection