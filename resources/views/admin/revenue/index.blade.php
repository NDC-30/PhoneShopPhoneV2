@extends('admin.layouts.master')
@section('title', 'Báo Cáo Doanh Thu')
@section('page_title', '📊 Báo Cáo Doanh Thu')

@php
    $pmLabel = fn($pm) => match(strtolower($pm ?? '')) {
        'cod'   => 'Khi nhận hàng (COD)',
        'cash'  => 'Tiền mặt (tại quầy)',
        'vnpay' => 'VNPay',
        'visa'  => 'Thẻ Visa/Master',
        default => 'Chuyển khoản',
    };
@endphp

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content-area">

    {{-- BỘ LỌC + XUẤT PDF --}}
    <div class="card border-0 shadow-sm p-3 mb-4">
        <form method="GET" class="row g-2 align-items-end">
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
                    @for($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}" {{ $day == $d ? 'selected' : '' }}>Ngày {{ $d }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.revenue.pdf', request()->query()) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                </a>
            </div>
        </form>
        <div class="mt-2 small text-muted">Kỳ báo cáo: <strong class="text-dark">{{ $periodLabel }}</strong></div>
    </div>

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm bg-primary text-white p-3 rounded-3 h-100">
                <div class="small text-uppercase fw-bold opacity-75">Doanh thu trong kỳ</div>
                <h4 class="fw-bold my-2">{{ number_format($periodRevenue) }}đ</h4>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm bg-success text-white p-3 rounded-3 h-100">
                <div class="small text-uppercase fw-bold opacity-75">Đơn hoàn thành</div>
                <h4 class="fw-bold my-2">{{ $periodOrders }}</h4>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm bg-dark text-white p-3 rounded-3 h-100">
                <div class="small text-uppercase fw-bold opacity-75">Giá trị TB / đơn</div>
                <h4 class="fw-bold my-2">{{ number_format($aov) }}đ</h4>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm bg-info text-white p-3 rounded-3 h-100">
                <div class="small text-uppercase fw-bold opacity-75">SP đã bán</div>
                <h4 class="fw-bold my-2">{{ $itemsSold }}</h4>
            </div>
        </div>
        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm bg-danger text-white p-3 rounded-3 h-100">
                <div class="small text-uppercase fw-bold opacity-75">Hủy + Hoàn / Tỷ lệ</div>
                <h4 class="fw-bold my-2">{{ $cancelled + $returned }} <small class="fs-6">({{ $cancelRate }}%)</small></h4>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- BIỂU ĐỒ --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-bar-chart-line text-primary"></i> Doanh thu — {{ $periodLabel }}</div>
                <div class="card-body"><canvas id="revenueChart" style="max-height:340px"></canvas></div>
            </div>
        </div>
        {{-- PHƯƠNG THỨC THANH TOÁN --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-wallet2 text-success"></i> Phương thức thanh toán</div>
                <div class="card-body">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Hình thức</th><th class="text-center">Đơn</th><th class="text-end">Doanh thu</th></tr></thead>
                        <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td>{{ $pmLabel($p->payment_method) }}</td>
                                <td class="text-center">{{ $p->cnt }}</td>
                                <td class="text-end fw-bold">{{ number_format($p->total) }}đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- BẢNG DOANH THU 12 THÁNG --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-calendar3 text-primary"></i> Doanh thu theo tháng (Năm {{ $year }})</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Tháng</th><th class="text-center">Số đơn</th><th class="text-end pe-3">Doanh thu</th></tr></thead>
                        <tbody>
                        @foreach($monthlyTable as $row)
                            <tr>
                                <td class="ps-3">Tháng {{ $row['month'] }}</td>
                                <td class="text-center">{{ $row['orders'] }}</td>
                                <td class="text-end pe-3 fw-bold {{ $row['total'] > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($row['total']) }}đ</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- TOP SẢN PHẨM --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-trophy text-warning"></i> Sản phẩm bán chạy ({{ $periodLabel }})</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Sản phẩm</th><th class="text-center">SL bán</th><th class="text-end pe-3">Doanh thu</th></tr></thead>
                        <tbody>
                        @forelse($topProducts as $i => $tp)
                            <tr>
                                <td class="ps-3"><strong>#{{ $i + 1 }}</strong> {{ $tp->name }}</td>
                                <td class="text-center"><span class="badge bg-success">{{ $tp->qty }}</span></td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($tp->revenue) }}đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{ label: 'Doanh thu (VND)', data: @json($chartData), backgroundColor: 'rgba(13,110,253,0.8)', borderRadius: 5 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => new Intl.NumberFormat('vi-VN').format(c.raw) + ' đ' } } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => new Intl.NumberFormat('vi-VN').format(v) } } }
        }
    });
</script>
@endsection
