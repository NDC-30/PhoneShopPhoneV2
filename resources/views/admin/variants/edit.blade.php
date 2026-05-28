@extends('admin.layouts.master')
@section('title', 'Sản Phẩm: ' . $product->name)
@section('page_title', 'Phiên Bản: ' . $product->name)

@section('content')
<div class="content-area">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.products.variants.index', $product->product_id) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Hủy & Quay lại
        </a>
    </div>

    <form action="{{ route('admin.products.variants.update', [$product->product_id, $variant->variant_id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold text-primary">1. Chỉnh sửa Thông tin Bán hàng & Hình ảnh</div>
            <div class="card-body row">
                <div class="col-md-3 mb-3">
                    <label class="form-label small fw-bold">Giá bán (VND) *</label>
                    <input type="number" name="price" class="form-control" value="{{ $variant->price }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label small fw-bold">Số lượng kho</label>
                    <input type="number" name="stock" class="form-control" value="{{ $variant->stock }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label small fw-bold">Mã SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ $variant->sku }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold text-success">Màu sắc phiên bản *</label>
                    <input type="text" name="specs[Màu sắc]" class="form-control border-success" value="{{ $currentSpecs['Màu sắc'] ?? '' }}" required>
                </div>

                <div class="col-md-12 mb-3">
                    @if($variant->image)
                        <div class="mb-2">
                            <span class="small text-muted d-block mb-1">Ảnh hiện tại:</span>
                            @php
                                $imgSrc = Str::startsWith($variant->image, ['http://', 'https://']) ? $variant->image : asset('storage/'.$variant->image);
                            @endphp
                            <img src="{{ $imgSrc }}" width="80" class="rounded border">
                        </div>
                    @endif
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-primary">Đổi ảnh mới (Upload File)</label>
                            <input type="file" name="image" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Hoặc đổi Link ảnh (URL)</label>
                            <input type="url" name="image_url" class="form-control form-control-sm" placeholder="https://...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3 text-primary">2. Bảng Thông Số Kỹ Thuật Đầy Đủ</h6>
        
        @php
            $specGroups = [
                'Cấu hình & Bộ nhớ' => [
                    'Hệ điều hành', 'Chip xử lý (CPU)', 'Tốc độ CPU', 'Chip đồ họa (GPU)', 'RAM', 'Dung lượng lưu trữ (ROM)'
                ],
                'Camera & Màn hình' => [
                    'Kích thước màn hình', 'Công nghệ màn hình', 'Độ phân giải màn hình', 'Tần số quét', 'Độ sáng tối đa', 'Mặt kính cảm ứng', 'Camera sau', 'Quay phim sau', 'Tính năng camera sau', 'Camera trước', 'Tính năng camera trước'
                ],
                'Pin & Sạc' => [
                    'Dung lượng pin', 'Loại pin', 'Hỗ trợ sạc tối đa', 'Công nghệ pin'
                ],
                'Tiện ích' => [
                    'Bảo mật nâng cao', 'Tính năng đặc biệt', 'Kháng nước, bụi'
                ],
                'Kết nối' => [
                    'Mạng di động', 'Số khe SIM', 'Wifi', 'GPS', 'Bluetooth', 'Cổng kết nối/sạc', 'Jack tai nghe'
                ],
                'Thiết kế & Chất liệu' => [
                    'Thiết kế', 'Chất liệu khung viền', 'Chất liệu mặt lưng', 'Kích thước', 'Khối lượng'
                ]
            ];
            $groupId = 0;
        @endphp

        <div class="accordion mb-4" id="specsAccordion">
            @foreach($specGroups as $groupName => $labels)
                @php $groupId++; @endphp
                <div class="accordion-item border-0 shadow-sm mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $groupId == 1 ? '' : 'collapsed' }} fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $groupId }}">
                            {{ $groupName }}
                        </button>
                    </h2>
                    <div id="collapse{{ $groupId }}" class="accordion-collapse collapse {{ $groupId == 1 ? 'show' : '' }}" data-bs-parent="#specsAccordion">
                        <div class="accordion-body bg-light">
                            <div class="row">
                                @foreach($labels as $label)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small text-muted mb-1">{{ $label }}</label>
                                    <input type="text" name="specs[{{ $label }}]" class="form-control form-control-sm" value="{{ $currentSpecs[$label] ?? '' }}">
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-lg btn-success w-100 fw-bold mb-5"><i class="bi bi-save"></i> CẬP NHẬT PHIÊN BẢN & THÔNG SỐ</button>
    </form>
</div>
@endsection