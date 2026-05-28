@extends('admin.layouts.master')

@section('title', 'Sửa Danh Mục - Phone Shop')
@section('page_title', 'Chỉnh Sửa Danh Mục')

@section('content')
<div class="content-area">
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Sửa danh mục: {{ $category->name }}</h6>
        </div>
        <div class="card-body">
            
            {{-- Form gửi lên hàm update của Controller. Bắt buộc phải có @method('PUT') --}}
            <form action="{{ route('admin.categories.update', $category->category_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Tên Danh Mục <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Tạm ẩn</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Thứ tự ưu tiên</label>
                    <input type="number" class="form-control" name="sort_order" value="{{ $category->sort_order }}">
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Cập nhật thay đổi</button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection