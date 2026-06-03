{{-- resources/views/partials/product-card.blade.php --}}
@php
    // $product được truyền vào, đã eager-load: brand, variants, defaultImage
    $minVariant = $product->variants->sortBy('price')->first();
    $price      = $minVariant->price ?? 0;
    $compare    = $minVariant->compare_price ?? 0;
    $hasSale    = $compare && $compare > $price;
    $img        = $product->thumbnail;
    $totalStock = $product->variants->sum('stock');
@endphp
<a href="{{ route('product.show', $product->slug ?? $product->product_id) }}" class="card">
    <div class="card-media">
        @if($hasSale)
            <span class="card-tag">-{{ round(($compare - $price) / $compare * 100) }}%</span>
        @elseif($product->is_featured)
            <span class="card-tag dark">Nổi bật</span>
        @endif
        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy">
    </div>
    <div class="card-body">
        <span class="card-brand">{{ $product->brand->name ?? '' }}</span>
        <span class="card-name">{{ $product->name }}</span>
        <div class="card-price">
            <span class="now">{{ number_format($price, 0, ',', '.') }}₫</span>
            @if($hasSale)<span class="was">{{ number_format($compare, 0, ',', '.') }}₫</span>@endif
        </div>
    </div>
</a>
