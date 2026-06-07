@extends('admin.layouts.master')
@section('title', 'Quản Lý Đơn Hàng')
@section('page_title', 'Danh Sách Đơn Hàng')

@section('content')
<div class="content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-1"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-secondary mb-0">Danh Sách Đơn Hàng</h4>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary shadow-sm"><i class="bi bi-plus-lg"></i> Tạo Đơn Hàng Bán Tại Quầy</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th width="22%">Sản Phẩm Mua</th>
                        <th>Khách Hàng</th>
                        <th>Ngày Đặt</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number ?? '#'.$order->order_id }}</strong></td>
                        <td>
                            @if(isset($order->details) && $order->details->count() > 0)
                                @foreach($order->details as $detail)
                                    <div class="small text-truncate mb-1" style="max-width: 230px;">• {{ $detail->variant->product->name ?? 'Sản phẩm' }} <span class="text-danger">(x{{ $detail->quantity }})</span></div>
                                @endforeach
                            @else
                                <span class="small text-muted fst-italic">Không có chi tiết</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->receiver_name }}</div>
                            <div class="small">{{ $order->receiver_phone }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ optional($order->created_at)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ optional($order->created_at)->format('H:i') }}</small>
                        </td>
                        <td class="text-danger fw-bold">{{ number_format($order->grand_total) }}đ</td>
                        <td>
                            @php $st = $order->status; @endphp
                            @if(in_array($st, ['completed','cancelled','returned']))
                                @if($st == 'completed')
                                    <span class="badge bg-success">Hoàn thành</span>
                                @elseif($st == 'cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                @else
                                    <span class="badge" style="background:#7c3aed;color:#fff">Hoàn trả</span>
                                @endif
                                <div class="text-muted mt-1" style="font-size:.72rem"><i class="bi bi-lock"></i> Đã chốt — không đổi được</div>
                            @else
                                <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 140px;">
                                        <option value="pending" {{ $st == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                        <option value="processing" {{ $st == 'processing' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="shipping" {{ $st == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                        <option value="completed" {{ $st == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="returned" {{ $st == 'returned' ? 'selected' : '' }}>Hoàn trả</option>
                                        <option value="cancelled" {{ $st == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </form>
                                <small class="text-muted d-block mt-1" style="font-size:.72rem">Sang "Đang giao/Hoàn thành" cần ĐVVC + mã vận đơn (ở trang chi tiết).</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết"><i class="bi bi-eye"></i> Xem</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Hệ thống chưa có đơn hàng nào!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
