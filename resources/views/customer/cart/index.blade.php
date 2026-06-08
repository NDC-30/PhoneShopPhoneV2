@extends('customer.layouts.app')
@section('title', 'Giỏ hàng — PhoneShop')

@section('content')
    <div class="container">
        <div class="crumb"><a href="{{ route('home') }}">Trang chủ</a> <span>/</span> <span>Giỏ hàng</span></div>

        @if ($items->isEmpty())
            <div class="empty">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <circle cx="9" cy="20" r="1.6" />
                    <circle cx="18" cy="20" r="1.6" />
                    <path d="M2 3h3l2.4 12.5a1 1 0 0 0 1 .8h8.7a1 1 0 0 0 1-.8L21 6H6" />
                </svg>
                <h3>Giỏ hàng trống</h3>
                <p style="margin-bottom:22px">Hãy chọn cho mình một chiếc điện thoại ưng ý nhé.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-dark">Khám phá sản phẩm</a>
            </div>
        @else
            <h1 style="font-size:32px;letter-spacing:-.03em;margin:24px 0 4px">Giỏ hàng</h1>
            <p style="color:var(--muted);margin-bottom:8px">{{ $items->sum('quantity') }} sản phẩm trong giỏ</p>

            <div class="cart-wrap">
                <div class="cart-list">
                    @foreach ($items as $item)
                        @php $v = $item->variant; @endphp
                        
                        <div class="cart-item">

    <a href="{{ route('product.show', $v->product->slug ?? $v->product->product_id) }}"
        class="thumb">
        <img src="{{ $v->image }}" alt="{{ $v->product->name }}">
    </a>

    <div class="meta">

        <div class="info">
            <h4>{{ $v->product->name }}</h4>

            <div class="attrs">
                <div class="attr-item">
                    <strong>Màu:</strong>
                    <span>{{ $v->color }}</span>
                </div>

                <div class="attr-item">
                    <strong>RAM:</strong>
                    <span>{{ $v->ram }}</span>
                </div>

                <div class="attr-item">
                    <strong>ROM:</strong>
                    <span>{{ $v->rom }}</span>
                </div>
            </div>

            <div class="unit">
                {{ number_format($v->price, 0, ',', '.') }}₫
            </div>

            <form method="POST"
                action="{{ route('cart.remove') }}"
                onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                @csrf
                <input type="hidden" name="variant_id" value="{{ $v->variant_id }}">
                <button class="remove" type="submit">Xóa</button>
            </form>
        </div>

        <div class="right">
            <div class="qty">
                <button type="button" onclick="stepQty({{ $v->variant_id }}, -1)">−</button>

                <input type="number"
                    min="1"
                    id="qty-{{ $v->variant_id }}"
                    value="{{ $item->quantity }}"
                    onchange="updateQty({{ $v->variant_id }}, this.value)">

                <button type="button" onclick="stepQty({{ $v->variant_id }}, 1)">+</button>
            </div>

            <div class="line-total">
                {{ number_format($item->line_total, 0, ',', '.') }}₫
            </div>
        </div>

    </div>

</div>
                            <a href="{{ route('product.show', $v->product->slug ?? $v->product->product_id) }}"
                                class="thumb">
                                <img src="{{ $v->image }}" alt="{{ $v->product->name }}" width="380px" height="380px">
                            </a>
                            {{-- <div class="meta">
                                <h4>{{ $v->product->name }}</h4>
                                <div class="attrs">
                                    Màu: {{ $v->color }} &nbsp;|&nbsp; RAM: {{ $v->ram }} &nbsp;|&nbsp; ROM: {{ $v->rom }}
                                </div>
                                <div class="unit">{{ number_format($v->price, 0, ',', '.') }}₫</div>
                                
                                <form method="POST" action="{{ route('cart.remove') }}" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                    @csrf
                                    <input type="hidden" name="variant_id" value="{{ $v->variant_id }}">
                                    <button class="remove" type="submit">Xóa</button>
                                </form>
                            </div>

                            {{-- CỘT 3: SỐ LƯỢNG & TỔNG TIỀN --}}
                            <div class="right">
                                <div class="qty">
                                    <button type="button" onclick="stepQty({{ $v->variant_id }}, -1)">−</button>
                                    <input type="number" min="1" inputmode="numeric" id="qty-{{ $v->variant_id }}" value="{{ $item->quantity }}" onchange="updateQty({{ $v->variant_id }}, this.value)">
                                    <button type="button" onclick="stepQty({{ $v->variant_id }}, 1)">+</button>
                                </div>
                                <div class="line-total">{{ number_format($item->line_total, 0, ',', '.') }}₫</div>
                            </div>
                            
                        </div> {{-- Đã bổ sung thẻ đóng </div> bị thiếu cho cart-item --}}
                    @endforeach

                    <form method="POST" action="{{ route('cart.clear') }}" style="margin-top:18px" onsubmit="return confirm('Xóa toàn bộ sản phẩm trong giỏ hàng?')">
                        @csrf
                        <button class="remove" type="submit" style="color:var(--muted)">Xóa toàn bộ giỏ hàng</button>
                    </form>
                </div>

                {{-- CỘT BÊN PHẢI: TÓM TẮT ĐƠN HÀNG --}}
                <aside class="summary">
                    <h3>Tóm tắt đơn hàng</h3>
                    <div class="line"><span>Tạm tính</span><span>{{ number_format($subtotal, 0, ',', '.') }}₫</span></div>
                    
                    @if ($voucher)
                        <div class="line">
                            <span>Voucher ({{ $voucher['code'] }})</span>
                            <span class="accent">−{{ number_format($voucher['discount'], 0, ',', '.') }}₫</span>
                        </div>
                    @endif
                    
                    <div class="line">
                        <span>Phí vận chuyển</span>
                        <span>{{ $subtotal >= 5000000 ? 'Miễn phí' : number_format(30000, 0, ',', '.') . '₫' }}</span>
                    </div>
                    
                    @php
                        $disc = $voucher['discount'] ?? 0;
                        $ship = $subtotal >= 5000000 ? 0 : 30000;
                        $total = max(0, $subtotal - $disc) + $ship;
                    @endphp
                    
                    <div class="line total"><span>Tổng cộng</span><span>{{ number_format($total, 0, ',', '.') }}₫</span></div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn btn-dark btn-block" style="margin-top:18px">Tiến hành thanh toán</a>
                    @else
                        <a href="{{ route('customer.login') }}" class="btn btn-dark btn-block" style="margin-top:18px">Đăng nhập để thanh toán</a>
                        <p style="text-align:center;color:var(--muted);font-size:13px;margin-top:12px">Bạn cần đăng nhập để đặt hàng</p>
                    @endauth
                    
                    <a href="{{ route('shop.index') }}" class="btn btn-line btn-block" style="margin-top:10px">Tiếp tục mua sắm</a>
                </aside>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function stepQty(variantId, delta) {
            const el = document.getElementById('qty-' + variantId);
            let n = parseInt(el.value || '1') + delta;
            if (n < 1) n = 1;
            updateQty(variantId, n);
        }
        async function updateQty(variantId, qty) {
            qty = parseInt(qty);
            if (isNaN(qty) || qty < 1) qty = 1;
            const r = await fetch("{{ route('cart.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.APP.csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ variant_id: variantId, quantity: qty })
            });
            if (r.ok) {
                location.reload();
            }
        }
    </script>
@endpush