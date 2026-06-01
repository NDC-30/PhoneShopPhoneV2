@extends('admin.layouts.master')
@section('title', 'Kết quả tìm kiếm')
@section('page_title', 'Kết quả tìm kiếm cho: "' . $keyword . '"')

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-info text-white h-100 p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase opacity-75">Sản Phẩm Tìm Thấy</div>
                        <h3 class="fw-bold my-2">{{ $products->count() }}</h3>
                    </div>
                    <i class="bi bi-box fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white h-100 p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase opacity-75">Đơn Hàng Tìm Thấy</div>
                        <h3 class="fw-bold my-2">{{ $orders->count() }}</h3>
                    </div>
                    <i class="bi bi-bag-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold py-3 text-success">
            <i class="bi bi-bag-check-fill me-1"></i> Kết quả Đơn hàng ({{ $orders->count() }})
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Số Điện Thoại</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->order_number ?? '#'.$order->order_id }}</strong></td>
                        <td>{{ $order->receiver_name }}</td>
                        <td>{{ $order->receiver_phone }}</td>
                        <td class="text-danger fw-bold">{{ number_format($order->grand_total) }}đ</td>
                        <td>
                            @if($order->status == 'completed') <span class="badge bg-success">Thành công</span>
                            @elseif($order->status == 'pending') <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif($order->status == 'cancelled') <span class="badge bg-danger">Đã hủy</span>
                            @else <span class="badge bg-info text-dark">Đang xử lý</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->order_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Xem</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Không tìm thấy đơn hàng nào!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold py-3 text-info">
            <i class="bi bi-box-fill me-1"></i> Kết quả Sản phẩm ({{ $products->count() }})
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên Sản Phẩm</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th> </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="fw-bold text-dark">
                            <i class="bi bi-phone me-2 text-secondary"></i> {{ $product->name }}
                        </td>
                        <td>
                            @if($product->status == 1)
                                <span class="badge bg-success">Hiển thị</span>
                            @else
                                <span class="badge bg-secondary">Đã ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product->product_id ?? $product->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Cập nhật
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">Không tìm thấy sản phẩm nào!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection