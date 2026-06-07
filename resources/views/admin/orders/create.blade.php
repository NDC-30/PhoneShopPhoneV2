@extends('admin.layouts.master')
@section('title', 'Tạo Đơn Hàng Mới')
@section('page_title', 'Tạo Đơn Bán Tại Quầy')

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-x-circle"></i> {{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-primary">1. Chọn Sản Phẩm Máy</div>
                    <div class="card-body">
                        <label class="form-label small fw-bold">Tìm sản phẩm *</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="productSearch" class="form-control" placeholder="Gõ tên máy hoặc SKU... (vd: iPhone, S25, SKU123)" autocomplete="off">
                        </div>

                        <input type="hidden" name="variant_id" id="variantId" required>

                        {{-- Sản phẩm đã chọn --}}
                        <div id="selectedBox" class="alert alert-success d-none align-items-center justify-content-between">
                            <div id="selectedText" class="small"></div>
                            <button type="button" class="btn-close" id="clearSelected" title="Bỏ chọn"></button>
                        </div>

                        {{-- Danh sách kết quả --}}
                        <div id="productList" class="list-group" style="max-height: 360px; overflow-y: auto;">
                            @forelse($variants as $variant)
                                <button type="button" class="list-group-item list-group-item-action product-item"
                                    data-id="{{ $variant->variant_id }}"
                                    data-name="{{ $variant->product->name ?? 'N/A' }}"
                                    data-sku="{{ $variant->sku }}"
                                    data-price="{{ $variant->price }}"
                                    data-stock="{{ $variant->stock }}"
                                    data-search="{{ \Illuminate\Support\Str::lower(($variant->product->name ?? '').' '.$variant->sku) }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $variant->product->name ?? 'N/A' }}</div>
                                            <small class="text-muted">SKU: {{ $variant->sku }} · Còn {{ $variant->stock }} máy</small>
                                        </div>
                                        <span class="fw-bold text-danger">{{ number_format($variant->price) }}đ</span>
                                    </div>
                                </button>
                            @empty
                                <div class="text-muted p-3">Kho chưa có sản phẩm nào còn hàng.</div>
                            @endforelse
                        </div>
                        <div id="noResult" class="text-muted p-3 d-none">Không tìm thấy sản phẩm phù hợp.</div>

                        <div class="mt-3" style="width: 180px;">
                            <label class="form-label small fw-bold">Số lượng *</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-success">2. Thông Tin Khách Mua</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tên khách hàng *</label>
                            <input type="text" name="receiver_name" class="form-control" required placeholder="Nguyễn Văn A" value="{{ old('receiver_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Số điện thoại *</label>
                            <input type="text" name="receiver_phone" class="form-control" required placeholder="0987654321" value="{{ old('receiver_phone') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash">Tiền mặt (Tại quầy)</option>
                                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                            </select>
                        </div>

                        <div id="totalPreview" class="bg-light border rounded p-2 text-center mb-3 d-none">
                            <span class="small text-muted d-block">Tạm tính</span>
                            <strong class="text-danger fs-5" id="totalPreviewVal"></strong>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2"><i class="bi bi-cart-check"></i> Tạo Đơn Hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const search = document.getElementById('productSearch');
    const items = Array.from(document.querySelectorAll('.product-item'));
    const variantId = document.getElementById('variantId');
    const selectedBox = document.getElementById('selectedBox');
    const selectedText = document.getElementById('selectedText');
    const clearSelected = document.getElementById('clearSelected');
    const noResult = document.getElementById('noResult');
    const qty = document.getElementById('quantity');
    const totalPreview = document.getElementById('totalPreview');
    const totalPreviewVal = document.getElementById('totalPreviewVal');
    let price = 0, stock = 0;
    const fmt = n => new Intl.NumberFormat('vi-VN').format(n) + 'đ';

    // Tìm kiếm
    search.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        let visible = 0;
        items.forEach(it => {
            const ok = it.dataset.search.includes(q);
            it.style.display = ok ? '' : 'none';
            if (ok) visible++;
        });
        noResult.classList.toggle('d-none', visible > 0);
    });

    // Chọn sản phẩm
    items.forEach(it => it.addEventListener('click', function () {
        variantId.value = this.dataset.id;
        price = parseFloat(this.dataset.price);
        stock = parseInt(this.dataset.stock);
        selectedText.innerHTML = '<strong>' + this.dataset.name + '</strong> · SKU: ' + this.dataset.sku + ' · ' + fmt(price) + ' · Còn ' + stock + ' máy';
        selectedBox.classList.remove('d-none'); selectedBox.classList.add('d-flex');
        items.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        qty.max = stock;
        updateTotal();
    }));

    // Bỏ chọn
    clearSelected.addEventListener('click', function () {
        variantId.value = ''; price = 0; stock = 0;
        selectedBox.classList.add('d-none'); selectedBox.classList.remove('d-flex');
        items.forEach(x => x.classList.remove('active'));
        totalPreview.classList.add('d-none');
    });

    function updateTotal() {
        if (price > 0) {
            let q = parseInt(qty.value) || 1;
            if (stock > 0 && q > stock) { q = stock; qty.value = q; }
            totalPreviewVal.textContent = fmt(price * q);
            totalPreview.classList.remove('d-none');
        }
    }
    qty.addEventListener('input', updateTotal);

    // Chặn submit nếu chưa chọn sản phẩm
    document.getElementById('orderForm').addEventListener('submit', function (e) {
        if (!variantId.value) { e.preventDefault(); alert('Vui lòng tìm và chọn 1 sản phẩm từ danh sách.'); }
    });
})();
</script>
@endsection
