@extends('admin.layouts.master')
@section('title', 'Cập nhật Sản Phẩm')
@section('page_title', 'Chỉnh sửa: ' . $product->name)

@section('content')
<div class="content-area">
    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Quay lại Danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3 text-primary">
                    <i class="bi bi-pencil-square me-1"></i> Cập nhật Thông tin Sản phẩm
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product->product_id ?? $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id ?? $category->id }}" 
                                            {{ $product->category_id == ($category->category_id ?? $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thương hiệu <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->brand_id ?? $brand->id }}" 
                                            {{ $product->brand_id == ($brand->brand_id ?? $brand->id) ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả sản phẩm</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả sản phẩm...">{{ $product->description }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái hiển thị</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Hiển thị công khai</option>
                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Ẩn sản phẩm</option>
                            </select>
                        </div>

                        <hr class="text-muted">

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($product->variants && $product->variants->count() > 0)
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white fw-bold py-3 text-secondary">
                    <i class="bi bi-layers"></i> Các phiên bản cấu hình (Đang có {{ $product->variants->count() }})
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">SKU</th>
                                <th>Giá bán</th>
                                <th>Kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $variant->sku }}</td>
                                <td class="text-danger fw-bold">{{ number_format($variant->price) }}đ</td>
                                <td>
                                    @if($variant->stock > 0)
                                        <span class="badge bg-success">{{ $variant->stock }} máy</span>
                                    @else
                                        <span class="badge bg-danger">Hết hàng</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection