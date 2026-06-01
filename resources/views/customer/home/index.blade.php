@extends('customer.layouts.master')

@section('title','PhoneShop')

@section('content')

<section class="bg-gradient-to-r from-red-600 via-red-500 to-pink-600">

<div class="container mx-auto px-6 py-24 text-center text-white">

    <h1 class="text-6xl font-extrabold mb-6">
        PhoneShop
    </h1>

    <p class="text-xl mb-8">
        iPhone • Samsung • Xiaomi • Oppo • Vivo
    </p>

    <a href="/dien-thoai"
       class="bg-white text-red-600 px-8 py-4 rounded-full font-bold">
        Khám phá ngay
    </a>

</div>

</section>

<section class="container mx-auto py-10">

<section class="container mx-auto py-16">

    <div class="text-center mb-10">

        <h2 class="text-4xl font-extrabold">
            Thương hiệu nổi bật
        </h2>
    </div>

    <div class="flex flex-wrap justify-center gap-5">

        @foreach($brands as $brand)

            <a href="#"
               class="bg-white px-8 py-4 rounded-2xl shadow hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <span class="font-semibold text-lg">
                    {{ $brand->name }}
                </span>

            </a>

        @endforeach

    </div>

</section>

<section class="container mx-auto py-10">

<div class="flex justify-between items-center mb-8">

    <h2 class="text-4xl font-bold">
        Sản phẩm mới nhất
    </h2>

</div>

<div class="grid md:grid-cols-4 gap-6">

    @foreach($featuredProducts as $product)

        @php
            $variant = $product->variants->first();
        @endphp

        <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300">

            <a href="{{ route('shop.detail',$product->slug) }}">

                <img
                     src="{{ $variant && $variant->image ? asset($variant->image) : 'https://via.placeholder.com/400x300' }}"
                    class="w-full h-64 object-cover">

            </a>

            <div class="p-5">

                <h3 class="font-bold text-lg min-h-[60px]">

                    {{ $product->name }}

                </h3>

                @if($variant)

                    <div class="mt-3">

                        <span class="text-red-600 text-2xl font-bold">

                            {{ number_format($variant->price) }}đ

                        </span>

                    </div>

                @endif

                <div class="mt-4 flex gap-2">

                    <a
                        href="{{ route('shop.detail',$product->slug) }}"
                        class="flex-1 bg-red-600 text-white text-center py-3 rounded-xl">

                        Xem chi tiết

                    </a>

                </div>

            </div>

        </div>

    @endforeach

</div>

</section>

@endsection
