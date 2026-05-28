@extends('admin.layouts.master')

@section('title', 'Sửa Thương Hiệu')
@section('page_title', 'Chỉnh Sửa Thương Hiệu')

@section('content')
<div class="content-area">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.brands.update', $brand->brand_id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Tên Thương Hiệu</label>
                    <input type="text" class="form-control" name="name" value="{{ $brand->name }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="1" {{ $brand->status == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="0" {{ $brand->status == 0 ? 'selected' : '' }}>Tạm ẩn</option>
                    </select>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Cập nhật thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection