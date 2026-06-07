@extends('customer.layouts.app')
@section('title', 'PhoneShop — Điện thoại chính hãng, giá tốt')

@section('content')
<div class="container">

    {{-- BANNER CAROUSEL (CHỈ HÌNH ẢNH) --}}
    @php
    // Danh sách URL banner hình ảnh (Bác có thể thay đổi list này theo ý muốn)
    $banners = [
        // Banner hiện tại (S26 Ultra)
        'https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:100/plain/https://media-asset.cellphones.com.vn/dashboard-v1/manage-banner/s26-ultra-home-0626.png',
        // Thêm một banner demo khác (Galaxy Fold6)
        'https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:100/plain/https://media-asset.cellphones.com.vn/dashboard-v1/manage-banner/690x300_iPhone17ProMax_0626.png',
        // Thêm một banner demo khác (Galaxy S24 Series)
        'https://cdn2.cellphones.com.vn/insecure/rs:fill:1036:450/q:100/plain/https://media-asset.cellphones.com.vn/dashboard-v1/manage-banner/Oppofin%20x9%20ultra_oppen-home.png'
    ];
    @endphp

    <section class="banner-carousel" style="overflow: hidden; margin-bottom: 20px;">
        @foreach($banners as $bannerUrl)
            <div class="item" style="display: {{ $loop->first ? 'block' : 'none' }};">
                <img src="{{ $bannerUrl }}" alt="Banner {{ $loop->iteration }}" style="width: 100%; height: auto; display: block;">
            </div>
        @endforeach
    </section>

    {{-- BRAND CHIPS --}}
    @if($brands->count())
    <section class="section" style="margin:40px 0">
        <div class="brandbar">
            <a href="{{ route('shop.index') }}" class="chip active">Tất cả</a>
            @foreach($brands as $b)
                <a href="{{ route('shop.index', ['brand' => $b->slug ?? $b->brand_id]) }}" class="chip">{{ $b->name }}</a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- NỔI BẬT --}}
    <section class="section">
        <div class="section-head">
            <div>
                <h2>Sản phẩm nổi bật</h2>
                <p>Được khách hàng lựa chọn nhiều nhất</p>
            </div>
            <a href="{{ route('shop.index') }}">Xem tất cả →</a>
        </div>
        <div class="grid">
            @foreach($featured as $product)
                @include('customer.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

    {{-- HÀNG MỚI --}}
    <section class="section">
        <div class="section-head">
            <div>
                <h2>Hàng mới về</h2>
                <p>Cập nhật những mẫu máy mới nhất</p>
            </div>
            <a href="{{ route('shop.index', ['sort' => 'newest']) }}">Xem tất cả →</a>
        </div>
        <div class="grid">
            @foreach($newest as $product)
                @include('customer.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

</div>

{{-- SCRIPT CHUYỂN BANNER TỰ ĐỘNG --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy tất cả các item banner
        const banners = document.querySelectorAll('.banner-carousel .item');
        let currentBannerIndex = 0;

        // Hàm ẩn banner hiện tại và hiện banner tiếp theo
        function showNextBanner() {
            // Ẩn banner hiện tại
            banners[currentBannerIndex].style.display = 'none';
            // Tính toán index của banner tiếp theo
            currentBannerIndex = (currentBannerIndex + 1) % banners.length;
            // Hiện banner tiếp theo
            banners[currentBannerIndex].style.display = 'block';
        }

        // Chỉ chạy nếu có nhiều hơn 1 banner
        if (banners.length > 1) {
            // Tự động chuyển banner sau mỗi 3 giây (3000 ms)
            setInterval(showNextBanner, 3000); 
        }
    });
</script>

@endsection