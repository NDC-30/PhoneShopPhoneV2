@extends('admin.layouts.master')
@section('title', 'Quản Lý Sản Phẩm Gốc')
@section('page_title', 'Danh Sách Sản Phẩm (Master)')

@section('content')
<div class="content-area">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="mb-1">Danh sách sản phẩm gốc</h6>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i> Thêm Sản Phẩm Mới
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên Sản Phẩm Gốc</th>
                        <th>Danh Mục</th>
                        <th>Thương Hiệu</th>
                        <th>Số Phiên Bản</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-dark">{{ $product->variants_count }} phiên bản</span></td>
                        <td>
                            <a href="{{ route('admin.products.variants.index', $product->product_id) }}" class="btn btn-sm btn-warning fw-bold">
                                <i class="bi bi-gear-fill"></i> Cấu hình điện thoại
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có sản phẩm nào trong hệ thống!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Thêm Sản Phẩm Gốc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Tên Sản Phẩm *</label>
                            <input type="text" name="name" class="form-control" required >
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Chọn --</option>
                                @foreach($categories as $cat) <option value="{{ $cat->category_id }}">{{ $cat->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Thương hiệu</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- Chọn --</option>
                                @foreach($brands as $brand) <option value="{{ $brand->brand_id }}">{{ $brand->name }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label small fw-bold">Mô tả tổng quan</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Sản Phẩm</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection