@extends('customer.layouts.master')

@section('title', $product->name)

@section('content')

<div class="container mx-auto px-6 py-10">

<div class="grid lg:grid-cols-2 gap-12">

    <!-- IMAGE -->

    <div>

        @if($variant && $variant->image)

            <img
                src="{{ asset($variant->image) }}"
                class="w-full rounded-2xl shadow-lg">

        @else

            <img
                src="https://via.placeholder.com/700x700"
                class="w-full rounded-2xl shadow-lg">

        @endif

    </div>

    <!-- INFO -->

    <div>

        <h1 class="text-4xl font-bold mb-4">
            {{ $product->name }}
        </h1>

        <div class="mb-4">

            <span class="bg-red-100 text-red-600 px-4 py-2 rounded-full">

                {{ $product->brand->name ?? 'PhoneShop' }}

            </span>

        </div>

        @if($variant)

            <div class="text-red-600 text-4xl font-bold mb-6">

                {{ number_format($variant->price) }}đ

            </div>

        @endif

        <div class="bg-white rounded-xl p-5 shadow mb-6">

            {!! nl2br(e($product->description)) !!}

        </div>

        <!-- THÔNG SỐ -->

        <div class="bg-white rounded-xl shadow p-5 mb-6">

            <h3 class="text-xl font-bold mb-4">
                Thông số sản phẩm
            </h3>

            @if($variant)

                @foreach($variant->attributeValues as $attributeValue)

                    <div class="flex justify-between py-2 border-b">

                        <span class="font-semibold">

                            {{ $attributeValue->attribute->display_name }}

                        </span>

                        <span>

                            {{ $attributeValue->value }}

                        </span>

                    </div>

                @endforeach

            @endif

        </div>

        <!-- STOCK -->

        @if($variant)

            <div class="mb-6">

                <span class="text-green-600 font-semibold">

                    Còn {{ $variant->stock }} sản phẩm

                </span>

            </div>

        @endif

        <!-- BUTTONS -->

        <div class="flex gap-4">

            <button
                class="flex-1 bg-red-600 text-white py-4 rounded-xl font-bold hover:bg-red-700">

                Mua ngay

            </button>

            <button
                class="flex-1 border border-red-600 text-red-600 py-4 rounded-xl font-bold">

                Thêm vào giỏ hàng

            </button>

        </div>

    </div>

</div>
```

</div>

@endsection
