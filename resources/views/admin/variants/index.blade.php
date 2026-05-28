@extends('admin.layouts.master')
@section('title', 'Quản Lý Sản Phẩm: ' . $product->name)
@section('page_title', '⚙️ Cấu hình điện thoại: ' . $product->name)

@section('content')
<div class="content-area">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại Sản Phẩm Gốc
        </a>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addVariantModal">
            <i class="bi bi-plus-lg"></i> Thêm Phiên Bản Cấu Hình
        </button>
    </div>

    <div class="row">
    @forelse($variants as $variant)

    <div class="row">
@forelse($variants as $variant)

<div class="col-12 mb-2">

    <div class="card shadow-sm border-0 rounded-3">

        <div class="card-body p-2">

            <div class="d-flex align-items-start">

                {{-- IMAGE --}}
                <div class="me-3">

                    @if($variant->image)

                        @php
                            $isUrl = Str::startsWith($variant->image, ['http://', 'https://']);
                            $imgSrc = $isUrl
                                ? $variant->image
                                : asset('storage/' . $variant->image);
                        @endphp

                        <img
                            src="{{ $imgSrc }}"
                            class="rounded-3 border"
                            style="
                                width: 110px;
                                height: 90px;
                                object-fit: contain;
                                background: #fff;
                            "
                        >

                    @else

                        <div
                            class="bg-light border rounded-3 d-flex align-items-center justify-content-center text-muted"
                            style="
                                width:110px;
                                height:90px;
                                font-size:12px;
                            "
                        >
                            Không ảnh
                        </div>

                    @endif

                </div>

                {{-- INFO --}}
                <div class="flex-grow-1">

                    {{-- TOP --}}
                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <h6 class="text-danger fw-bold mb-1">
                                {{ number_format($variant->price) }} VND
                            </h6>

                            <div class="small text-muted mb-1">
                                SKU: {{ $variant->sku }}
                            </div>

                            <div class="small">
                                Kho:
                                <strong>{{ $variant->stock }}</strong>
                            </div>

                        </div>

                        {{-- ACTION --}}
                        <div class="d-flex gap-1">

                            <a
                                href="{{ route('admin.products.variants.edit', [$product->product_id, $variant->variant_id]) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form
                                action="{{ route('admin.products.variants.destroy', [$product->product_id, $variant->variant_id]) }}"
                                method="POST"
                                onsubmit="return confirm('Bạn chắc chắn muốn xóa?');"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>

                            </form>

                        </div>

                    </div>

                    {{-- SPECS --}}
                    <div class="row mt-2">

                        @forelse($variant->attributeValues->take(6) as $attrVal)

                            <div class="col-md-4 col-6 mb-1">

                                <div
                                    class="small text-truncate"
                                    title="{{ $attrVal->value }}"
                                >
                                    <span class="fw-bold text-muted">
                                        {{ $attrVal->attribute->name }}:
                                    </span>

                                    {{ $attrVal->value }}
                                </div>

                            </div>

                        @empty

                            <div class="col-12">
                                <span class="small text-muted">
                                    Chưa có thông số kỹ thuật
                                </span>
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@empty

<div class="col-12 text-center py-5 text-muted">

    <h5>Chưa có phiên bản nào</h5>

    <p>Hãy thêm phiên bản cấu hình đầu tiên</p>

</div>

@endforelse
</div>

    @empty

    <div class="col-12 text-center py-5 text-muted">

        <h5>Chưa có phiên bản nào</h5>

        <p>
            Hãy thêm phiên bản cấu hình đầu tiên
        </p>

    </div>

    @endforelse
</div>
</div>

<div class="modal fade" id="addVariantModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('admin.products.variants.store', $product->product_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-phone-fill"></i> Cấu Hình Phiên Bản & Thông Số Có Sẵn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-bold text-primary">1. Thông tin Bán hàng & Hình ảnh</div>
                        <div class="card-body row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Giá bán (VND) *</label>
                                <input type="number" name="price" class="form-control" required >
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label small fw-bold">Số lượng kho</label>
                                <input type="number" name="stock" class="form-control" value="10">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label small fw-bold">Mã SKU</label>
                                <input type="text" name="sku" class="form-control" placeholder="Để trống tự tạo">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold text-success">Màu sắc phiên bản *</label>
                                <input type="text" name="specs[Màu sắc]" class="form-control border-success"  required>
                            </div>

                            <div class="col-md-6 mb-2 mt-2">
                                <label class="form-label small fw-bold text-primary">Tải ảnh từ máy tính (File)</label>
                                <input type="file" name="image" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-2 mt-2">
                                <label class="form-label small fw-bold text-danger">Hoặc dán Link ảnh trực tiếp (URL)</label>
                                <input type="url" name="image_url" class="form-control form-control-sm" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary">2. Bảng Thông Số Kỹ Thuật Đầy Đủ (Chuẩn Thế Giới Di Động)</h6>
                    
                    @php
                        $specGroups = [
                            'Cấu hình & Bộ nhớ' => [
                                'Hệ điều hành' => 'VD: iOS 17, Android 14',
                                'Chip xử lý (CPU)' => 'VD: Apple A17 Pro 6 nhân',
                                'Tốc độ CPU' => 'VD: 3.78 GHz',
                                'Chip đồ họa (GPU)' => 'VD: Apple GPU 6 nhân',
                                'RAM' => 'VD: 8 GB, 12 GB',
                                'Dung lượng lưu trữ (ROM)' => 'VD: 256 GB, 512 GB'
                            ],
                            'Camera & Màn hình' => [
                                'Kích thước màn hình' => 'VD: 6.7 inch, 6.1 inch',
                                'Công nghệ màn hình' => 'VD: OLED, Super AMOLED',
                                'Độ phân giải màn hình' => 'VD: Super Retina XDR (1290 x 2796 Pixels)',
                                'Tần số quét' => 'VD: 120 Hz',
                                'Độ sáng tối đa' => 'VD: 2000 nits',
                                'Mặt kính cảm ứng' => 'VD: Kính cường lực Ceramic Shield',
                                'Camera sau' => 'VD: Chính 48 MP & Phụ 12 MP, 12 MP',
                                'Quay phim sau' => 'VD: 4K 2160p@60fps, 1080p@60fps',
                                'Tính năng camera sau' => 'VD: Ban đêm (Night Mode), Chống rung quang học (OIS), Zoom quang học',
                                'Camera trước' => 'VD: 12 MP',
                                'Tính năng camera trước' => 'VD: Quay video 4K, HDR, Xóa phông'
                            ],
                            'Pin & Sạc' => [
                                'Dung lượng pin' => 'VD: 4422 mAh, 5000 mAh',
                                'Loại pin' => 'VD: Li-Ion',
                                'Hỗ trợ sạc tối đa' => 'VD: 20 W, 25 W',
                                'Công nghệ pin' => 'VD: Sạc pin nhanh, Sạc không dây MagSafe, Tiết kiệm pin'
                            ],
                            'Tiện ích' => [
                                'Bảo mật nâng cao' => 'VD: Mở khoá khuôn mặt Face ID, Mở khóa vân tay dưới màn hình',
                                'Tính năng đặc biệt' => 'VD: Dynamic Island, Loa kép, Phát hiện va chạm',
                                'Kháng nước, bụi' => 'VD: IP68 (Kháng nước ở độ sâu 6 mét trong 30 phút)'
                            ],
                            'Kết nối' => [
                                'Mạng di động' => 'VD: Hỗ trợ 5G',
                                'Số khe SIM' => 'VD: 1 Nano SIM & 1 eSIM, 2 Nano SIM',
                                'Wifi' => 'VD: Wi-Fi 6E, Wi-Fi 7, Dual-band',
                                'GPS' => 'VD: GPS, GLONASS, BEIDOU, GALILEO',
                                'Bluetooth' => 'VD: v5.3',
                                'Cổng kết nối/sạc' => 'VD: Type-C',
                                'Jack tai nghe' => 'VD: Type-C, Không có jack 3.5mm'
                            ],
                            'Thiết kế & Chất liệu' => [
                                'Thiết kế' => 'VD: Nguyên khối',
                                'Chất liệu khung viền' => 'VD: Titanium, Nhôm',
                                'Chất liệu mặt lưng' => 'VD: Kính cường lực',
                                'Kích thước' => 'VD: Dài 159.9 mm - Ngang 76.7 mm - Dày 8.25 mm',
                                'Khối lượng' => 'VD: 221 g'
                            ]
                        ];
                        $groupId = 0;
                    @endphp

                    <div class="accordion" id="specsAccordion">
                        @foreach($specGroups as $groupName => $specs)
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
                                            @foreach($specs as $label => $placeholder)
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label small text-muted mb-1">{{ $label }}</label>
                                                <input type="text" name="specs[{{ $label }}]" class="form-control form-control-sm" placeholder="{{ $placeholder }}">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="modal-footer border-top bg-white">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="bi bi-save"></i> Lưu Phiên Bản & Thông Số</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection