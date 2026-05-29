@extends('admin.layouts.master')
@section('title', 'Tạo Đơn Hàng Mới')
@section('page_title', 'Tạo Đơn Bán Tại Quầy')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-primary">1. Chọn Sản Phẩm Máy</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Chọn máy từ kho *</label>
                            <select name="variant_id" class="form-select" required>
                                <option value="">-- Chọn điện thoại khách mua --</option>
                                @foreach($variants as $variant)
                                    <option value="{{ $variant->variant_id }}">
                                        {{ $variant->product->name ?? 'N/A' }} 
                                        - SKU: {{ $variant->sku }} 
                                        (Giá: {{ number_format($variant->price) }}đ - Còn: {{ $variant->stock }} máy)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số lượng *</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required style="width: 150px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-success">2. Thông Tin Khách Mua</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tên khách hàng *</label>
                            <input type="text" name="receiver_name" class="form-control" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số điện thoại *</label>
                            <input type="text" name="receiver_phone" class="form-control" required placeholder="0987654321">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select">
                                <option value="CASH">Tiền mặt (Tại quầy)</option>
                                <option value="BANK_TRANSFER">Chuyển khoản ngân hàng</option>
                                <option value="COD">Thanh toán khi nhận hàng (COD)</option>
                            </select>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">
                            <i class="bi bi-cart-check"></i> Tạo Đơn Hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection