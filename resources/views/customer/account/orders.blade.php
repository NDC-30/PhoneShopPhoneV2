@extends('customer.layouts.app')
@section('title', 'Đơn hàng của tôi — PhoneShop')

@php
    $statusLabels = [
        'pending'    => 'Chờ xác nhận',
        'processing' => 'Đã xác nhận',
        'shipping'   => 'Đang giao hàng',
        'completed'  => 'Hoàn thành',
        'returned'   => 'Hoàn trả',
        'cancelled'  => 'Đã hủy',
    ];
@endphp

@section('content')
<div class="container">
    <div class="crumb"><a href="{{ route('home') }}">Trang chủ</a> <span>/</span> <span>Đơn hàng</span></div>

    <div class="acct">
        @include('customer.partials.account-nav', ['active' => 'orders'])

        <div>
            <div class="acct-card">
                <h2>Đơn hàng của tôi</h2>
                <p class="sub">Theo dõi tình trạng các đơn hàng bạn đã đặt.</p>

                @forelse($orders as $order)
                    @php
                        $st = $order->status ?? 'pending';
                        $cls = in_array($st, ['pending','processing','shipping','completed','returned','cancelled']) ? $st : 'pending';
                    @endphp
                    <div class="order-row">
                        <div class="top">
                            <div>
                                <span class="code">{{ $order->order_number }}</span>
                                <span class="date"> · {{ $order->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="status {{ $cls }}">{{ $statusLabels[$cls] ?? $st }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="color:var(--muted);font-size:13.5px">{{ $order->details_count ?? $order->details->count() }} sản phẩm · {{ $order->payment_method === 'cod' ? 'COD' : 'VNPay' }}</span>
                            <span style="font-weight:700">{{ number_format($order->grand_total,0,',','.') }}₫</span>
                        </div>
                        <div style="margin-top:12px;text-align:right">
                            <a href="{{ route('account.order.show', $order->order_id) }}" class="btn btn-line" style="padding:8px 16px;font-size:13px">Xem chi tiết</a>
                        </div>
                    </div>
                @empty
                    <div class="empty" style="padding:50px 20px">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6M9 13h7M9 17h7"/></svg>
                        <h3>Chưa có đơn hàng nào</h3>
                        <p style="margin-bottom:20px">Hãy chọn cho mình sản phẩm yêu thích nhé.</p>
                        <a href="{{ route('shop.index') }}" class="btn btn-dark">Mua sắm ngay</a>
                    </div>
                @endforelse

                @if($orders->hasPages())
                    <div style="margin-top:20px">{{ $orders->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
