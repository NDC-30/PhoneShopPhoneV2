@extends('admin.layouts.master')

@section('title', 'Quản Lý Thương Hiệu - Phone Shop')
@section('page_title', 'Quản Lý Thương Hiệu')

@section('content')
<div class="content-area">
    
    {{-- THÔNG BÁO THÀNH CÔNG/LỖI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Báo lỗi Validate Form --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="mb-1">Danh sách thương hiệu</h6>
            <p class="mb-0 text-muted" id="brandCount">Tổng: {{ $brands->count() }} thương hiệu</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="bi bi-plus"></i> Thêm Thương Hiệu
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Thương Hiệu</th>
                        <th>Số Sản Phẩm</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody id="brandsTableBody">
                    {{-- LẶP DỮ LIỆU TỪ DATABASE --}}
                    @forelse($brands as $key => $brand)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>{{ $brand->name }}</strong></td>
                            
                            {{-- Đếm số sản phẩm thông qua móc nối ảo ở Model --}}
                            <td><span class="badge bg-info">{{ $brand->products->count() ?? 0 }}</span></td>
                            
                            <td>
                                @if($brand->status == 1)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Tạm ẩn</span>
                                @endif
                            </td>
                            <td>
                                {{-- Nút Sửa --}}
                                <a href="{{ route('admin.brands.edit', $brand->brand_id) }}" class="btn btn-sm btn-info edit-btn text-white">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Nút Xóa (Bảo mật bằng Form POST + @method('DELETE')) --}}
                                <form action="{{ route('admin.brands.destroy', $brand->brand_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có thương hiệu nào trong hệ thống!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL THÊM THƯƠNG HIỆU --}}
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Thương Hiệu Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- FORM SUBMIT CHUẨN LARAVEL --}}
            <form action="{{ route('admin.brands.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Thương Hiệu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="Ví dụ: Apple, Samsung...">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="1">Hoạt động</option>
                            <option value="0">Tạm ẩn</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Thương Hiệu</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection