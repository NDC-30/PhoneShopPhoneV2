@extends('customer.layouts.app')
@section('title', 'PhoneShop — Điện thoại chính hãng, giá tốt')

@section('content')
<div class="container">

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">Bộ sưu tập 2026</span>
            <h1>Công nghệ trong tầm tay.</h1>
            <p>Điện thoại chính hãng, nguyên seal, bảo hành toàn quốc. Trải nghiệm mua sắm tối giản, giao nhanh, đổi trả dễ dàng.</p>
            <div class="hero-actions">
                <a href="{{ route('shop.index') }}" class="btn btn-dark">Mua ngay</a>
                <a href="{{ route('shop.index', ['sort' => 'newest']) }}" class="btn btn-line">Hàng mới về</a>
            </div>
        </div>
        @php
$heroImg = 'https://cdn.images.express.co.uk/img/dynamic/59/940x/secondary/best-iphone-15-pro-deals-uk-4962371.jpg?r=1695486336091';
@endphp

<img src="{{ $heroImg }}" alt="iPhone 15 Pro">
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
@endsection
