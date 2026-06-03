@extends('customer.layouts.app')
@section('title', 'Liên hệ — PhoneShop')

@section('content')
<div class="container">
    <div class="crumb"><a href="{{ route('home') }}">Trang chủ</a> <span>/</span> <span>Liên hệ</span></div>

    <div style="text-align:center;max-width:620px;margin:36px auto 8px">
        <h1 style="font-size:38px;letter-spacing:-.03em;margin-bottom:10px">Liên hệ với chúng tôi</h1>
        <p style="color:var(--muted);font-size:15px">Có thắc mắc về sản phẩm, đơn hàng hay bảo hành? Đội ngũ PhoneShop luôn sẵn sàng hỗ trợ bạn.</p>
    </div>

    <div class="checkout" style="margin-top:36px">
        {{-- Thông tin liên hệ --}}
        <div>
            <div class="form-card">
                <h3>Thông tin cửa hàng</h3>
                <div style="display:grid;gap:20px">
                    <div style="display:flex;gap:14px;align-items:flex-start">
                        <span style="width:40px;height:40px;border-radius:10px;background:var(--bg);display:grid;place-items:center;flex-shrink:0">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="1.7"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:14.5px">Địa chỉ</div>
                            <div style="color:var(--muted);font-size:14px"> Hà Nội</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;align-items:flex-start">
                        <span style="width:40px;height:40px;border-radius:10px;background:var(--bg);display:grid;place-items:center;flex-shrink:0">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="1.7"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:14.5px">Hotline</div>
                            <div style="color:var(--muted);font-size:14px">0000</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;align-items:flex-start">
                        <span style="width:40px;height:40px;border-radius:10px;background:var(--bg);display:grid;place-items:center;flex-shrink:0">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="var(--ink)" stroke-width="1.7"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 6 10 7 10-7"/></svg>
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:14.5px">Email</div>
                            <div style="color:var(--muted);font-size:14px">hotro@phoneshop.vn</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form gửi tin nhắn (demo) --}}
        <div class="form-card" style="margin-bottom:0">
            <h3>Gửi tin nhắn cho chúng tôi</h3>
            <div class="field">
                <label>Họ tên</label>
                <input type="text" placeholder="Tên của bạn">
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" placeholder="ban@email.com">
            </div>
            <div class="field">
                <label>Nội dung</label>
                <textarea rows="4" placeholder="Chúng tôi có thể giúp gì cho bạn?"></textarea>
            </div>
            <button type="button" class="btn btn-dark btn-block"
                    onclick="this.textContent='Đã gửi! Cảm ơn bạn ♥'">Gửi tin nhắn</button>
        </div>
    </div>
</div>
@endsection
