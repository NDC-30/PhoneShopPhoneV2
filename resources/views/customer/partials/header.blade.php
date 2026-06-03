{{-- resources/views/partials/header.blade.php --}}
<div class="topbar">Miễn phí giao hàng toàn quốc cho đơn từ <b>5.000.000₫</b> · Bảo hành chính hãng 12 tháng</div>

<header class="site">
    <nav class="nav">

        {{-- TRÁI: thanh tìm kiếm --}}
        <div class="nav-left">
            <form action="{{ route('shop.index') }}" method="GET" class="search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm iPhone, Samsung, Xiaomi…" autocomplete="off">
            </form>
        </div>

        {{-- GIỮA: logo + menu --}}
        <div style="text-align:center">
            <a href="{{ route('home') }}" class="brand">Phone<span class="dot">.</span>Shop</a>
            <ul class="mainmenu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a></li>
                <li><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') || request()->routeIs('product.*') ? 'active' : '' }}">Sản phẩm</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Liên hệ</a></li>
            </ul>
        </div>

        {{-- PHẢI: tài khoản + giỏ hàng --}}
        <div class="nav-right">
            @auth
                <div class="account">
                    <span class="avatar">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset(auth()->user()->avatar) }}" alt="">
                        @else
                            {{ mb_strtoupper(mb_substr(auth()->user()->fullname, 0, 1)) }}
                        @endif
                    </span>
                    <div class="who">
                        <small>Xin chào</small>
                        <b>{{ \Illuminate\Support\Str::limit(auth()->user()->fullname, 14) }}</b>
                    </div>

                    {{-- bấm vào avatar/tên -> menu chứa "Tài khoản" -> thông tin KH --}}
                    <div class="account-menu">
                        <a href="{{ route('account.index') }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                            Tài khoản
                        </a>
                        <a href="{{ route('account.orders') }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h12l2 5v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7z"/><path d="M4 7h16"/></svg>
                            Đơn hàng của tôi
                        </a>
                        <a href="{{ route('cart.index') }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.4 12.5a1 1 0 0 0 1 .8h8.7a1 1 0 0 0 1-.8L21 6H6"/></svg>
                            Giỏ hàng
                        </a>
                        <div class="sep"></div>
                        <form action="{{ route('customer.logout') }}" method="POST">
                            @csrf
                            <button type="submit">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('customer.login') }}" class="btn btn-line" style="padding:9px 18px">Đăng nhập</a>
            @endauth

            {{-- giỏ hàng --}}
            <a href="{{ route('cart.index') }}" class="cart-btn" aria-label="Giỏ hàng">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2 3h3l2.4 12.5a1 1 0 0 0 1 .8h8.7a1 1 0 0 0 1-.8L21 6H6"/></svg>
                @if(($cartCount ?? 0) > 0)<span class="badge">{{ $cartCount }}</span>@endif
            </a>
        </div>
    </nav>
</header>
