@extends('admin.layouts.master')
@section('title', 'Quản Lý Vouchers')
@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary">
            <i class="bi bi-ticket-perforated-fill"></i> Quản lý Vouchers
        </h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
            <i class="bi bi-plus-circle"></i> Tạo Voucher
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-primary">
                        <tr class="text-center">
                            <th>Mã</th>
                            <th>Tên</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Số lượng</th>
                            <th>Đã dùng</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $v)
                        <tr>
                            <td class="fw-bold text-primary">{{ $v->code }}</td>
                            <td>{{ $v->name ?? 'N/A' }}</td>
                            <td class="text-center">
                                @if($v->discount_type == 'fixed')
                                    <span class="badge bg-success">Tiền mặt</span>
                                @else
                                    <span class="badge bg-info">Phần trăm</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($v->discount_type == 'fixed')
                                    <strong class="text-danger">{{ number_format($v->discount_value) }}đ</strong>
                                @else
                                    <strong class="text-danger">{{ $v->discount_value }}%</strong>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($v->min_order_value) }}đ</td>
                            <td class="text-center">
                                @if($v->usage_limit)
                                    <span class="badge bg-secondary">{{ $v->usage_limit }}</span>
                                @else
                                    <span class="badge bg-warning">Không giới hạn</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $v->used_count ?? 0 }}</td>
                            <td class="text-center">
                                @if($v->status)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Khóa</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.vouchers.destroy', $v->voucher_id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?')"
                                      class="d-inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-ticket-perforated" style="font-size: 48px; color: #ccc;"></i>
                                <p class="mt-3 text-muted">Chưa có voucher nào. Hãy tạo voucher đầu tiên!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo voucher -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle"></i> Tạo Voucher Mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Mã Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="VD: SALE20" required>
                        <small class="text-muted">Mã sẽ tự động chuyển thành chữ hoa</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tên Voucher</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Khuyến mãi 20k">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Loại giảm giá <span class="text-danger">*</span></label>
                        <select name="discount_type" class="form-select" required>
                            <option value="fixed">💰 Giảm tiền mặt</option>
                            <option value="percent">📊 Giảm phần trăm</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Giá trị giảm <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" class="form-control" placeholder="20,000 hoặc 10" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Đơn hàng tối thiểu</label>
                        <input type="number" name="min_order_value" class="form-control" placeholder="0" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Giảm tối đa (cho %)</label>
                        <input type="number" name="max_discount" class="form-control" placeholder="50,000" value="0">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng voucher</label>
                    <input type="number" name="usage_limit" class="form-control" placeholder="0 = không giới hạn" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
        transition: all 0.3s;
    }
    .badge {
        padding: 5px 10px;
        font-weight: 500;
    }
    .card {
        border-radius: 10px;
    }
    .btn {
        border-radius: 5px;
    }
</style>
@endsection