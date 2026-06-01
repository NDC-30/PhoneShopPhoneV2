@extends('admin.layouts.master')
@section('title', 'Chi tiết Khách Hàng')
@section('page_title', 'Hồ sơ: ' . $customer->fullname)

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại Danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-5">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 100px; height: 100px; font-size: 40px;">
                        {{ mb_substr($customer->fullname ?? 'K', 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $customer->fullname }}</h5>
                    <p class="text-muted small mb-3"><i class="bi bi-envelope-fill me-1"></i> {{ $customer->email }}</p>
                    
                    @if(($customer->status ?? 1) == 1)
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Đang hoạt động</span>
                    @else
                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-lock-fill me-1"></i> Tài khoản bị khóa</span>
                    @endif
                    
                    <hr class="my-4 text-muted">
                    
                    <div class="text-start px-3">
                        <p class="mb-2"><i class="bi bi-telephone-fill text-muted me-2"></i> <strong>SĐT:</strong> {{ $customer->phone ?? 'Chưa cập nhật' }}</p>
                        <p class="mb-2"><i class="bi bi-geo-alt-fill text-muted me-2"></i> <strong>Địa chỉ:</strong> {{ $customer->address ?? 'Chưa cập nhật' }}</p>
                        <p class="mb-0"><i class="bi bi-calendar-check-fill text-muted me-2"></i> <strong>Ngày tham gia:</strong> {{ $customer->created_at ? date('d/m/Y', strtotime($customer->created_at)) : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3 text-primary d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bag-check-fill me-1"></i> Lịch Sử Mua Hàng</span>
                    <span class="badge bg-primary rounded-pill">{{ $orders->count() }} đơn</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mã Đơn</th>
                                    <th>Ngày Đặt</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Chi Tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $order->order_number ?? '#'.$order->order_id }}</td>
                                    <td>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($order->grand_total ?? $order->total_amount) }}đ</td>
                                    <td>
                                        @if($order->status == 'completed') <span class="badge bg-success">Thành công</span>
                                        @elseif($order->status == 'pending') <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                        @elseif($order->status == 'cancelled') <span class="badge bg-danger">Đã hủy</span>
                                        @else <span class="badge bg-info text-dark">Đang xử lý</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->order_id ?? $order->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-box-arrow-up-right"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">Khách hàng này chưa phát sinh đơn hàng nào!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection