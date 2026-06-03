@extends('customer.layouts.app')
@section('title', 'Thông tin tài khoản — PhoneShop')

@section('content')
<div class="container">
    <div class="crumb"><a href="{{ route('home') }}">Trang chủ</a> <span>/</span> <span>Tài khoản</span></div>

    <div class="acct">
        @include('customer.partials.account-nav', ['active' => 'info'])

        <div>
            {{-- Thông tin cá nhân --}}
            <div class="acct-card" style="margin-bottom:22px">
                <h2>Thông tin tài khoản</h2>
                <p class="sub">Quản lý thông tin cá nhân của bạn.</p>

                <form method="POST" action="{{ route('account.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               style="background:var(--bg);color:var(--muted)">
                    </div>

                    <div class="field">
                        <label>Họ và tên</label>
                        <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}">
                        @error('fullname')<div class="err-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                        @error('phone')<div class="err-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label>Địa chỉ</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}"
                               placeholder="Địa chỉ nhận hàng mặc định">
                        @error('address')<div class="err-text">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-dark">Lưu thay đổi</button>
                </form>
            </div>

            {{-- Đổi mật khẩu --}}
            <div class="acct-card">
                <h2 style="font-size:19px">Đổi mật khẩu</h2>
                <p class="sub">Để bảo mật, hãy dùng mật khẩu mạnh và không chia sẻ cho ai.</p>

                <form method="POST" action="{{ route('account.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" placeholder="••••••••">
                        @error('current_password')<div class="err-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="password" placeholder="Tối thiểu 6 ký tự">
                            @error('password')<div class="err-text">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Nhập lại mật khẩu mới</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-line">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
