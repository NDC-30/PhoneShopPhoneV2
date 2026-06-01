@extends('customer.layouts.master')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-5">

    <h2 class="mb-4">Giỏ hàng</h2>

    @if(count($cart))
        <div class="alert alert-success">
            Có {{ count($cart) }} sản phẩm trong giỏ hàng
        </div>
    @else
        <div class="alert alert-info">
            Giỏ hàng đang trống
        </div>
    @endif

</div>
@endsection