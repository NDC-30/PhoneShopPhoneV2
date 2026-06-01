@extends('customer.layouts.master')

@section('title','Sản phẩm')

@section('content')

<div class="container mx-auto py-10">

    <div class="flex justify-between mb-8">

        <h1 class="text-4xl font-bold">
            Điện thoại
        </h1>

        <select class="border rounded px-3">
            <option>Mới nhất</option>
            <option>Giá tăng dần</option>
            <option>Giá giảm dần</option>
        </select>

    </div>

    <div class="grid md:grid-cols-4 gap-6">

       @foreach($featuredProducts as $product)

    @php
        $variant = $product->variants->first();
    @endphp

    <div class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">

        <a href="{{ route('shop.detail',$product->slug) }}">

            <div class="overflow-hidden">

                <img
                    src="{{ $variant && $variant->image ? asset($variant->image) : 'https://via.placeholder.com/400x300' }}"
                    class="w-full h-64 object-cover group-hover:scale-110 transition duration-500">

            </div>

        </a>

        <div class="p-5">

            <div class="text-sm text-gray-500 mb-2">
                {{ $product->brand->name ?? 'PhoneShop' }}
            </div>

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

            <div class="mt-5">

                <a
                    href="{{ route('shop.detail',$product->slug) }}"
                    class="block text-center bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-semibold">

                    Xem chi tiết

                </a>

            </div>

        </div>

    </div>

@endforeach