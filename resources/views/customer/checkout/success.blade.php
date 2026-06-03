@extends('customer.layouts.app')
@section('title', 'Đặt hàng thành công — PhoneShop')

@section('content')
    <div class="container" style="max-width:760px">
        <div style="text-align:center;padding:48px 0 28px">
            <div
                style="width:74px;height:74px;border-radius:50%;background:#e7f6ec;display:grid;place-items:center;margin:0 auto 18px">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--ok)" stroke-width="2.2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>
            <h1 style="font-size:30px;letter-spacing:-.03em;margin-bottom:8px">Đặt hàng thành công!</h1>
            <p style="color:var(--muted)">
                Cảm ơn bạn đã mua sắm. Mã đơn hàng của bạn là
                <strong style="color:var(--ink)">{{ $order->order_number }}</strong>.
            </p>
        </div>

        <div class="form-card">
            <h3 style="margin-bottom:18px">Sản phẩm đã đặt</h3>
            @foreach ($order->details as $d)
                @php $v = $d->variant; @endphp
                <div class="mini-item">
                    <div class="mthumb">
                        <img src="{{ $v?->image_url ?? asset('images/placeholder.png') }}" alt="">
                        <span class="q">{{ $d->quantity }}</span>
                    </div>
                    <div class="mname">
                        <div class="product-name">
                            {{ $v->product->name }}
                        </div>

                        <div class="product-attrs">

                            <div class="attr-row">
                                <strong>Màu:</strong>
                                <span>{{ $v->color }}</span>
                            </div>

                            <div class="attr-row">
                                <strong>RAM:</strong>
                                <span>{{ $v->ram }}</span>
                            </div>

                            <div class="attr-row">
                                <strong>ROM:</strong>
                                <span>{{ $v->rom }}</span>
                            </div>

                        </div>
                    </div>
                    <div class="mprice">{{ number_format($d->subtotal, 0, ',', '.') }}₫</div>
                </div>
            @endforeach

            <div style="margin-top:18px">
                <div class="line"
                    style="display:flex;justify-content:space-between;padding:7px 0;color:var(--ink-soft);font-size:14.5px">
                    <span>Tạm tính</span><span>{{ number_format($order->total_amount, 0, ',', '.') }}₫</span></div>
                @if ($order->discount_amount > 0)
                    <div style="display:flex;justify-content:space-between;padding:7px 0;font-size:14.5px"><span>Giảm
                            giá</span><span
                            style="color:var(--accent)">−{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                    </div>
                @endif
                <div
                    style="display:flex;justify-content:space-between;padding:7px 0;color:var(--ink-soft);font-size:14.5px">
                    <span>Phí vận
                        chuyển</span><span>{{ $order->shipping_fee == 0 ? 'Miễn phí' : number_format($order->shipping_fee, 0, ',', '.') . '₫' }}</span>
                </div>
                <div
                    style="display:flex;justify-content:space-between;padding-top:14px;margin-top:8px;border-top:1px solid var(--line);font-size:19px;font-weight:800">
                    <span>Tổng cộng</span><span>{{ number_format($order->grand_total, 0, ',', '.') }}₫</span></div>
            </div>
        </div>

        <div class="form-card">
            <h3 style="margin-bottom:16px">Thông tin giao hàng</h3>
            <div style="font-size:14.5px;line-height:1.9;color:var(--ink-soft)">
                <div><strong style="color:var(--ink)">{{ $order->receiver_name }}</strong> · {{ $order->receiver_phone }}
                </div>
                <div>{{ $order->shipping_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}
                </div>
                <div>Thanh toán: {{ $order->payment_method === 'cod' ? 'Khi nhận hàng (COD)' : 'VNPay' }}</div>
                @if ($order->customer_note)
                    <div>Ghi chú: {{ $order->customer_note }}</div>
                @endif
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:center;margin:26px 0 60px">
            <a href="{{ route('account.orders') }}" class="btn btn-line">Xem đơn hàng của tôi</a>
            <a href="{{ route('shop.index') }}" class="btn btn-dark">Tiếp tục mua sắm</a>
        </div>
    </div>
@endsection
