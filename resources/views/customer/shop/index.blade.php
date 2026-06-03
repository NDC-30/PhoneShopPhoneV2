@extends('customer.layouts.app')
@section('title', 'Sản phẩm — PhoneShop')

@section('content')
<div class="container">
    <div class="crumb">
        <a href="{{ route('home') }}">Trang chủ</a> <span>/</span> <span>Sản phẩm</span>
        @if(request('q')) <span>/</span> <span>Kết quả cho “{{ request('q') }}”</span> @endif
    </div>

    <div class="shop">
        {{-- BỘ LỌC --}}
        <aside class="filters">
            <form method="GET" id="filterForm">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                <div class="filter-group">
                    <h4>Thương hiệu</h4>
                    @foreach($brands as $b)
                        @php $val = $b->slug ?? $b->brand_id; @endphp
                        <label>
                            <input type="checkbox" name="brand[]" value="{{ $val }}"
                                onchange="document.getElementById('filterForm').submit()"
                                {{ in_array($val, (array) request('brand', [])) ? 'checked' : '' }}>
                            {{ $b->name }} <span style="color:var(--muted);font-size:12px">({{ $b->products_count }})</span>
                        </label>
                    @endforeach
                </div>

                @if($categories->count())
                <div class="filter-group">
                    <h4>Danh mục</h4>
                    @foreach($categories as $c)
                        <label>
                            <input type="radio" name="category" value="{{ $c->category_id }}"
                                onchange="document.getElementById('filterForm').submit()"
                                {{ request('category') == $c->category_id ? 'checked' : '' }}>
                            {{ $c->name }}
                        </label>
                    @endforeach
                </div>
                @endif

                @if(request()->hasAny(['brand','category','q']))
                    <a href="{{ route('shop.index') }}" class="btn btn-line btn-block" style="margin-top:18px">Xóa bộ lọc</a>
                @endif
            </form>
        </aside>

        {{-- LƯỚI SẢN PHẨM --}}
        <div>
            <div class="shop-head">
                <span class="count">{{ $products->total() }} sản phẩm</span>
                <form method="GET">
                    @foreach(request()->except('sort') as $k => $v)
                        @if(is_array($v))@foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
                        @else<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest"     {{ $sort=='newest'?'selected':'' }}>Mới nhất</option>
                        <option value="price_asc"  {{ $sort=='price_asc'?'selected':'' }}>Giá: Thấp → Cao</option>
                        <option value="price_desc" {{ $sort=='price_desc'?'selected':'' }}>Giá: Cao → Thấp</option>
                        <option value="name"        {{ $sort=='name'?'selected':'' }}>Tên A → Z</option>
                    </select>
                </form>
            </div>

            @if($products->count())
                <div class="grid">
                    @foreach($products as $product)
                        @include('customer.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div style="margin-top:40px">{{ $products->links() }}</div>
            @else
                <div class="empty">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <h3>Không tìm thấy sản phẩm</h3>
                    <p>Thử thay đổi từ khóa hoặc bộ lọc khác.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
