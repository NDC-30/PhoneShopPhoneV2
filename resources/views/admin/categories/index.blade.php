@extends('admin.layouts.master')

@section('title', 'Quản Lý Danh Mục - Phone Shop')
@section('page_title', 'Quản Lý Danh Mục')

@section('content')
<div class="content-area">
    
    {{-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG HOẶC LỖI TỪ CONTROLLER --}}
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

    {{-- Báo lỗi Validate (nếu nhập sai/thiếu thông tin) --}}
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
            <h6 class="mb-1">Danh sách danh mục</h6>
            {{-- Đếm tổng số lượng mảng dữ liệu lấy từ DB --}}
            <p class="mb-0 text-muted" id="categoryCount">Tổng: {{ $categories->count() }} danh mục</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus"></i> Thêm Danh Mục
        </button>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Danh Mục</th>
                        <th>Số Sản Phẩm</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- VÒNG LẶP XUẤT DỮ LIỆU TỪ DATABASE --}}
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><strong>{{ $category->name }}</strong></td>
                            
                            {{-- Đếm số sản phẩm thông qua hàm quan hệ đã viết ở Model --}}
                            <td><span class="badge bg-info">{{ $category->products->count() ?? 0 }}</span></td>
                            
                            <td>
                                @if($category->status == 1)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Tạm ẩn</span>
                                @endif
                            </td>
                            <td>
                                {{-- Nút Sửa --}}
                                <a href="{{ route('admin.categories.edit', $category->category_id) }}" class="btn btn-sm btn-info edit-btn text-white">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Nút Xóa (Phải dùng Form bảo mật của Laravel) --}}
                                <form action="{{ route('admin.categories.destroy', $category->category_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
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
                            <td colspan="5" class="text-center text-muted py-4">Chưa có danh mục nào trong hệ thống!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL THÊM DANH MỤC --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Danh Mục Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- FORM SUBMIT CHUẨN LARAVEL --}}
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                        {{-- Thêm name="name" để gửi lên Controller --}}
                        <input type="text" class="form-control" name="name" required placeholder="Ví dụ: Điện thoại di động">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="1">Hoạt động</option>
                            <option value="0">Tạm ẩn</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Thứ tự ưu tiên</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                        <small class="text-muted">Số càng nhỏ hiển thị càng trước (Ví dụ: 0, 1, 2...)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Danh Mục</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection