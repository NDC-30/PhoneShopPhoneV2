@extends('customer.layouts.app')
@section('title', 'Chi tiết đơn ' . $order->order_number . ' — PhoneShop')

@php
    $statusLabels = [
        'pending'    => 'Chờ xác nhận',
        'processing' => 'Đã xác nhận',
        'shipping'   => 'Đang giao hàng',
        'completed'  => 'Hoàn thành',
        'returned'   => 'Hoàn trả',
        'cancelled'  => 'Đã hủy',
    ];
    $st  = $order->status ?? 'pending';
    $cls = in_array($st, ['pending','processing','shipping','completed','returned','cancelled']) ? $st : 'pending';
@endphp

@section('content')
<div class="container">
    <div class="crumb">
        <a href="{{ route('home') }}">Trang chủ</a> <span>/</span>
        <a href="{{ route('account.orders') }}">Đơn hàng</a> <span>/</span>
        <span>{{ $order->order_number }}</span>
    </div>

    <div class="acct">
        @include('customer.partials.account-nav', ['active' => 'orders'])

        <div>
            <div class="acct-card" style="margin-bottom:22px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
                    <div>
                        <h2 style="margin-bottom:2px">Đơn {{ $order->order_number }}</h2>
                        <p class="sub" style="margin-bottom:0">Đặt ngày {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="status {{ $cls }}">{{ $statusLabels[$cls] ?? $st }}</span>
                </div>

                <div style="margin-top:24px">
                    @foreach($order->details as $d)
                        @php $v = $d->variant; @endphp
                        <div class="mini-item">
                            <div class="mthumb">
                                <img src="{{ $v?->image_url ?? asset('images/placeholder.png') }}" alt="">
                                <span class="q">{{ $d->quantity }}</span>
                            </div>
                            <div class="mname">
                                {{ $v?->product?->name ?? 'Sản phẩm' }}
                                <small>{{ $v?->short_label }}</small>
                            </div>
                            <div class="mprice">{{ number_format($d->subtotal,0,',','.') }}₫</div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:18px">
                    <div style="display:flex;justify-content:space-between;padding:7px 0;color:var(--ink-soft);font-size:14.5px"><span>Tạm tính</span><span>{{ number_format($order->total_amount,0,',','.') }}₫</span></div>
                    @if($order->discount_amount > 0)
                    <div style="display:flex;justify-content:space-between;padding:7px 0;font-size:14.5px"><span>Giảm giá @if($order->voucher)({{ $order->voucher->code }})@endif</span><span style="color:var(--accent)">−{{ number_format($order->discount_amount,0,',','.') }}₫</span></div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding:7px 0;color:var(--ink-soft);font-size:14.5px"><span>Phí vận chuyển</span><span>{{ $order->shipping_fee == 0 ? 'Miễn phí' : number_format($order->shipping_fee,0,',','.').'₫' }}</span></div>
                    <div style="display:flex;justify-content:space-between;padding-top:14px;margin-top:8px;border-top:1px solid var(--line);font-size:19px;font-weight:800"><span>Tổng cộng</span><span>{{ number_format($order->grand_total,0,',','.') }}₫</span></div>
                </div>
            </div>

            <div class="acct-card">
                <h2 style="font-size:18px">Thông tin giao hàng</h2>
                <div style="font-size:14.5px;line-height:1.9;color:var(--ink-soft);margin-top:10px">
                    <div><strong style="color:var(--ink)">{{ $order->receiver_name }}</strong> · {{ $order->receiver_phone }}</div>
                    <div>{{ $order->shipping_address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</div>
                    <div>Thanh toán: {{ $order->payment_method === 'cod' ? 'Khi nhận hàng (COD)' : 'VNPay' }}
                        — {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</div>
                    @if($order->customer_note)<div>Ghi chú: {{ $order->customer_note }}</div>@endif
                </div>
            </div>

            {{-- HỦY ĐƠN: chỉ khi còn "Chờ xác nhận" --}}
            @if($order->status === 'pending')
                <form method="POST" action="{{ route('account.order.cancel', $order->order_id) }}"
                      onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')" style="margin-top:18px">
                    @csrf
                    <button type="submit" class="btn btn-line" style="color:var(--accent);border-color:var(--accent)">Hủy đơn hàng</button>
                </form>
            @elseif($order->status === 'cancelled')
                <p style="margin-top:18px;color:var(--accent);font-size:14px">Đơn hàng đã được hủy.</p>
            @else
                <p style="margin-top:18px;color:var(--muted);font-size:14px">Đơn đã được xác nhận nên không thể tự hủy. Cần hỗ trợ, vui lòng liên hệ shop.</p>
            @endif
        </div>
    </div>
</div>
@endsection
