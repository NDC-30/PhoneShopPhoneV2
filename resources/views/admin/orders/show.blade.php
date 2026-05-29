@extends('admin.layouts.master')
@section('title', 'Chi Tiết Đơn Hàng')
@section('page_title', 'Mã Đơn: ' . ($order->order_number ?? '#'.$order->order_id))

@section('content')
<style>
    /* Thanh tiến trình đơn hàng */
    .order-track { position: relative; display: flex; justify-content: space-between; margin: 30px 0; padding: 0 20px; }
    .order-track::before { content: ''; position: absolute; top: 18px; left: 40px; right: 40px; height: 3px; background: #e9ecef; z-index: 1; }
    .track-step { position: relative; z-index: 2; text-align: center; width: 25%; }
    .track-icon { width: 40px; height: 40px; border-radius: 50%; background: #e9ecef; color: #6c757d; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; font-weight: bold; border: 4px solid #fff; box-shadow: 0 0 0 2px #e9ecef; }
    .track-step.completed .track-icon { background: #198754; color: #fff; box-shadow: 0 0 0 2px #198754; }
    .track-step.active .track-icon { background: #0d6efd; color: #fff; box-shadow: 0 0 0 2px #0d6efd; }
    .track-title { font-size: 0.85rem; font-weight: 600; color: #6c757d; }
    .track-step.active .track-title, .track-step.completed .track-title { color: #212529; }
</style>

<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-warning fw-bold shadow-sm">
                <i class="bi bi-printer"></i> In Hóa Đơn
            </button>
            <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng này?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm">
                    <i class="bi bi-trash"></i> Xóa Đơn Hàng
                </button>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger shadow-sm"><i class="bi bi-x-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            @if($order->status == 'cancelled')
                <div class="text-center py-3">
                    <h4 class="text-danger fw-bold mb-0"><i class="bi bi-x-circle"></i> ĐƠN HÀNG ĐÃ BỊ HỦY</h4>
                    <p class="text-muted mt-2">Dữ liệu sản phẩm đã được hoàn lại vào kho.</p>
                </div>
            @else
                <div class="order-track">
                    <div class="track-step {{ in_array($order->status, ['pending', 'processing', 'shipping', 'completed']) ? 'completed' : '' }}">
                        <div class="track-icon"><i class="bi bi-receipt"></i></div>
                        <div class="track-title">Chờ Duyệt</div>
                    </div>
                    <div class="track-step {{ in_array($order->status, ['processing', 'shipping', 'completed']) ? 'completed' : '' }} {{ $order->status == 'processing' ? 'active' : '' }}">
                        <div class="track-icon"><i class="bi bi-box-seam"></i></div>
                        <div class="track-title">Đóng Gói</div>
                    </div>
                    <div class="track-step {{ in_array($order->status, ['shipping', 'completed']) ? 'completed' : '' }} {{ $order->status == 'shipping' ? 'active' : '' }}">
                        <div class="track-icon"><i class="bi bi-truck"></i></div>
                        <div class="track-title">Giao Hàng</div>
                    </div>
                    <div class="track-step {{ $order->status == 'completed' ? 'completed active' : '' }}">
                        <div class="track-icon"><i class="bi bi-check-lg"></i></div>
                        <div class="track-title">Hoàn Thành</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-cart"></i> Danh Sách Sản Phẩm</div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>SL</th>
                                <th class="text-end pe-3">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $item)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light border rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            @if($item->variant && $item->variant->image)
                                                <img src="{{ Str::startsWith($item->variant->image, 'http') ? $item->variant->image : asset('storage/'.$item->variant->image) }}" class="img-fluid rounded" style="max-height: 40px;">
                                            @else
                                                <i class="bi bi-phone text-muted fs-4"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->variant->product->name ?? 'N/A' }}</div>
                                            <small class="text-muted">SKU: {{ $item->variant->sku ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->unit_price) }}đ</td>
                                <td class="fw-bold">x{{ $item->quantity }}</td>
                                <td class="text-end pe-3 fw-bold text-danger">{{ number_format($item->subtotal) }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-sliders"></i> Xử Lý Đơn Hàng</div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->order_id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Trạng thái hiện tại</label>
                                <select name="status" class="form-select border-primary fw-bold text-primary" {{ $order->status == 'completed' ? 'disabled' : '' }}>
                                    @if($order->status == 'pending')
                                        <option value="pending" selected>Chờ duyệt</option>
                                        <option value="processing">Xác nhận & Đóng gói</option>
                                        <option value="cancelled">Hủy Đơn</option>
                                    @elseif($order->status == 'processing')
                                        <option value="processing" selected>Đang xử lý (Đóng gói)</option>
                                        <option value="shipping">Bắt đầu Giao hàng</option>
                                        <option value="cancelled">Hủy Đơn</option>
                                    @elseif($order->status == 'shipping')
                                        <option value="shipping" selected>Đang giao hàng</option>
                                        <option value="completed">Giao Thành Công</option>
                                        <option value="cancelled">Giao Thất Bại (Hoàn Hàng)</option>
                                    @elseif($order->status == 'completed')
                                        <option value="completed" selected>Đã Hoàn Thành</option>
                                    @elseif($order->status == 'cancelled')
                                        <option value="cancelled" selected>Đã Hủy Đơn</option>
                                        <option value="pending">Khôi phục (Về Chờ duyệt)</option>
                                    @endif
                                </select>
                                @if($order->status == 'completed')
                                    <input type="hidden" name="status" value="completed">
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Đơn vị vận chuyển</label>
                                <select name="carrier" class="form-select">
                                    <option value="">-- Chọn ĐVVC --</option>
                                    <option value="Giao Hàng Nhanh" {{ ($order->shipping->carrier ?? '') == 'Giao Hàng Nhanh' ? 'selected' : '' }}>Giao Hàng Nhanh (GHN)</option>
                                    <option value="GHTK" {{ ($order->shipping->carrier ?? '') == 'GHTK' ? 'selected' : '' }}>Giao Hàng Tiết Kiệm</option>
                                    <option value="Viettel Post" {{ ($order->shipping->carrier ?? '') == 'Viettel Post' ? 'selected' : '' }}>Viettel Post</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Mã Vận Đơn (Tracking)</label>
                                <input type="text" name="tracking_number" class="form-control" value="{{ $order->shipping->tracking_number ?? '' }}" placeholder="VD: GHN123456789">
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <button type="submit" class="btn btn-primary px-4" {{ $order->status == 'completed' ? 'disabled' : '' }}>
                                <i class="bi bi-save me-1"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-person-lines-fill"></i> Thông Tin Khách Hàng</div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3" style="width: 45px; height: 45px;">
                            {{ mb_substr($order->receiver_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $order->receiver_name }}</h6>
                            <small class="text-muted"><i class="bi bi-telephone-fill"></i> {{ $order->receiver_phone }}</small>
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded border">
                        <div class="small fw-bold mb-1">Địa chỉ giao hàng:</div>
                        <div class="small">{{ $order->shipping_address }}</div>
                        <div class="small text-muted">{{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3"><i class="bi bi-credit-card-2-front"></i> Chi Tiết Thanh Toán</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Tổng tiền hàng:</span>
                        <strong class="text-dark">{{ number_format($order->total_amount) }}đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <strong class="text-dark">{{ number_format($order->shipping_fee ?? 0) }}đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-success">
                        <span>Voucher giảm giá:</span>
                        <strong>-{{ number_format($order->discount_amount) }}đ</strong>
                    </div>
                    <hr class="text-muted">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Khách Cần Trả:</span>
                        <strong class="text-danger fs-4">{{ number_format($order->grand_total) }}đ</strong>
                    </div>
                    
                    <div class="bg-light p-2 rounded text-center border">
                        <span class="small text-muted d-block mb-1">Phương thức thanh toán</span>
                        @if($order->payment_method == 'COD')
                            <span class="badge bg-secondary px-3 py-2"><i class="bi bi-cash"></i> Thanh toán khi nhận hàng (COD)</span>
                        @else
                            <span class="badge bg-info px-3 py-2"><i class="bi bi-bank"></i> Chuyển khoản ngân hàng</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection