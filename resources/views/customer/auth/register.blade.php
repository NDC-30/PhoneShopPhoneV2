@extends('customer.layouts.app')
@section('title', 'Đăng ký — PhoneShop')

@section('content')
<div class="auth-wrap">
    <div class="auth-aside">
        <div class="a-brand">Phone.Shop</div>
        <div>
            <h2>Tạo tài khoản mới</h2>
            <p>Mua sắm dễ dàng, theo dõi đơn hàng và nhận ưu đãi dành riêng cho thành viên.</p>
        </div>
        <div style="font-size:13px;color:#8a8a93">© {{ date('Y') }} PhoneShop. Điện thoại chính hãng.</div>
    </div>

    <div class="auth-form">
        <div class="inner">
            <h1>Đăng ký</h1>
            <p class="lead">Chỉ mất một phút để tạo tài khoản.</p>

            <form method="POST" action="{{ route('customer.register') }}">
                @csrf
                <div class="field">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Nguyễn Văn A" autofocus>
                    @error('fullname')<div class="err-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="ban@email.com">
                    @error('email')<div class="err-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="09xx xxx xxx">
                    @error('phone')<div class="err-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="Tối thiểu 6 ký tự">
                    @error('password')<div class="err-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-dark btn-block" style="margin-top:6px">Tạo tài khoản</button>
            </form>

            <p class="auth-switch">Đã có tài khoản? <a href="{{ route('customer.login') }}">Đăng nhập</a></p>
        </div>
    </div>
</div>
@endsection
