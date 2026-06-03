@extends('customer.layouts.app')
@section('title', 'Đăng nhập — PhoneShop')

@section('content')
<div class="auth-wrap">
    <div class="auth-aside">
        <div class="a-brand">Phone.Shop</div>
        <div>
            <h2>Chào mừng trở lại 👋</h2>
            <p>Đăng nhập để theo dõi đơn hàng, lưu địa chỉ và thanh toán nhanh hơn.</p>
        </div>
        <div style="font-size:13px;color:#8a8a93">© {{ date('Y') }} PhoneShop. Điện thoại chính hãng.</div>
    </div>

    <div class="auth-form">
        <div class="inner">
            <h1>Đăng nhập</h1>
            <p class="lead">Nhập email và mật khẩu của bạn để tiếp tục.</p>

            @if($errors->any())
                <div class="alert error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('customer.login') }}">
                @csrf
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="ban@email.com" autofocus>
                </div>
                <div class="field">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="••••••••">
                </div>
                <label style="display:flex;align-items:center;gap:8px;font-size:13.5px;color:var(--ink-soft);margin-bottom:20px;cursor:pointer">
                    <input type="checkbox" name="remember" style="accent-color:var(--ink)"> Ghi nhớ đăng nhập
                </label>
                <button type="submit" class="btn btn-dark btn-block">Đăng nhập</button>
            </form>

            <p class="auth-switch">Chưa có tài khoản? <a href="{{ route('customer.register') }}">Đăng ký ngay</a></p>
        </div>
    </div>
</div>
@endsection
