@extends('customer.layouts.app')
@section('title', 'Thanh toán — PhoneShop')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="container">
        <div class="crumb">
            <a href="{{ route('home') }}">Trang chủ</a> <span>/</span>
            <a href="{{ route('cart.index') }}">Giỏ hàng</a> <span>/</span>
            <span>Thanh toán</span>
        </div>

        <h1 style="font-size:32px;letter-spacing:-.03em;margin:24px 0 4px">Thanh toán</h1>
        <p style="color:var(--muted);margin-bottom:8px">Điền thông tin nhận hàng để hoàn tất đơn.</p>

        <form method="POST" action="{{ route('checkout.place') }}" id="checkout-form">
            @csrf
            <div class="checkout">
                {{-- CỘT TRÁI: THÔNG TIN --}}
                <div>
                    {{-- Thông tin nhận hàng --}}
                    <div class="form-card">
                        <h3><span class="n">1</span> Thông tin nhận hàng</h3>

                        <div class="field-row">
                            <div class="field">
                                <label>Họ tên người nhận</label>
                                <input type="text" name="receiver_name"
                                    value="{{ old('receiver_name', $user->fullname) }}" placeholder="Nguyễn Văn A">
                                @error('receiver_name')
                                    <div class="err-text">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Số điện thoại</label>
                                <input type="text" name="receiver_phone"
                                    value="{{ old('receiver_phone', $user->phone) }}" placeholder="09xx xxx xxx">
                                @error('receiver_phone')
                                    <div class="err-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="field-row three">
                            <div class="field">
                                <label>Tỉnh / Thành phố</label>
                                <input type="text" name="province" value="{{ old('province') }}" placeholder="Hà Nội">
                                @error('province')
                                    <div class="err-text">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Quận / Huyện</label>
                                <input type="text" name="district" value="{{ old('district') }}" placeholder="Cầu Giấy">
                                @error('district')
                                    <div class="err-text">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Phường / Xã</label>
                                <input type="text" name="ward" value="{{ old('ward') }}" placeholder="Dịch Vọng">
                                @error('ward')
                                    <div class="err-text">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label>Địa chỉ cụ thể (số nhà, tên đường)</label>
                            <input type="text" name="shipping_address"
                                value="{{ old('shipping_address', $user->address) }}" placeholder="Số 1, đường ABC">
                            @error('shipping_address')
                                <div class="err-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field" style="margin-bottom:0">
                            <label>Ghi chú đơn hàng (tùy chọn)</label>
                            <textarea name="customer_note" rows="2" placeholder="Giao trong giờ hành chính...">{{ old('customer_note') }}</textarea>
                        </div>
                    </div>

                    {{-- Phương thức thanh toán --}}
                    <div class="form-card">
                        <h3><span class="n">2</span> Phương thức thanh toán</h3>

                        <label class="pay-opt active" id="opt-cod">
                            <input type="radio" name="payment_method" value="cod" checked onchange="pickPay('cod')">
                            <div>
                                <div class="pi">Thanh toán khi nhận hàng (COD)</div>
                                <div class="pd-sub">Trả tiền mặt cho shipper khi nhận hàng</div>
                            </div>
                        </label>

                        <label class="pay-opt" id="opt-vnpay">
                            <input type="radio" name="payment_method" value="vnpay" onchange="pickPay('vnpay')">
                            <div>
                                <div class="pi">Thanh toán qua VNPay</div>
                                <div class="pd-sub">Thẻ ATM / Visa / QR — chuyển sang cổng VNPay để thanh toán</div>
                            </div>
                        </label>

                        @error('payment_method')
                            <div class="err-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT --}}
                <aside>
                    <div class="summary">
                        <h3>Đơn hàng của bạn</h3>

                        <div style="max-height:300px;overflow:auto;margin-bottom:14px">
                            @foreach ($items as $item)
                                @php $v = $item->variant; @endphp
                                <div class="mini-item">
                                    <div class="mthumb">
                                        <img src="{{ $v->image_url }}" alt="">
                                        <span class="q">{{ $item->quantity }}</span>
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
                                    <div class="mprice">{{ number_format($item->line_total, 0, ',', '.') }}₫</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Voucher --}}
                        <div class="voucher-apply" style="margin-bottom:14px">
                            <div style="display:flex;gap:8px">
                                <input type="text" id="vcode" class="vinput" placeholder="Nhập mã giảm giá"
                                    value="{{ $voucher['code'] ?? '' }}"
                                    style="flex:1;padding:11px 13px;border:1px solid var(--line);border-radius:9px;background:var(--bg);font-size:14px">
                                <button type="button" class="btn btn-dark" onclick="applyVoucher()"
                                    style="padding:0 18px">Áp dụng</button>
                            </div>
                            <div id="vmsg"
                                style="font-size:12.5px;margin-top:7px;font-weight:500;
                             color:{{ $voucher ? 'var(--ok)' : 'var(--muted)' }}">
                                @if ($voucher)
                                    Đã áp mã {{ $voucher['code'] }} — giảm
                                    {{ number_format($voucher['discount'], 0, ',', '.') }}₫
                                @endif
                            </div>
                        </div>

                        <div class="line"><span>Tạm tính</span><span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="line" id="line-discount" style="{{ $discount ? '' : 'display:none' }}">
                            <span>Giảm giá</span>
                            <span class="accent" id="discount-val">−{{ number_format($discount, 0, ',', '.') }}₫</span>
                        </div>
                        <div class="line">
                            <span>Phí vận chuyển</span>
                            <span>{{ $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee, 0, ',', '.') . '₫' }}</span>
                        </div>
                        <div class="line total">
                            <span>Tổng cộng</span>
                            <span id="grand-total">{{ number_format($grandTotal, 0, ',', '.') }}₫</span>
                        </div>

                        <button type="submit" id="btn-place-order" class="btn btn-dark btn-block"
                            style="margin-top:18px">
                            Đặt hàng
                        </button>
                        <p style="text-align:center;color:var(--muted);font-size:12.5px;margin-top:12px">
                            Bằng việc đặt hàng, bạn đồng ý với điều khoản của cửa hàng.
                        </p>
                    </div>
                </aside>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const SUBTOTAL = {{ (int) $subtotal }};
        const SHIPPING = {{ (int) $shippingFee }};
        const fmt = n => n.toLocaleString('vi-VN') + '₫';

        function pickPay(method) {
            document.getElementById('opt-cod').classList.toggle('active', method === 'cod');
            document.getElementById('opt-vnpay').classList.toggle('active', method === 'vnpay');
        }

        function setTotals(discount) {
            const lineD = document.getElementById('line-discount');
            if (discount > 0) {
                lineD.style.display = 'flex';
                document.getElementById('discount-val').textContent = '−' + fmt(discount);
            } else {
                lineD.style.display = 'none';
            }
            const grand = Math.max(0, SUBTOTAL - discount) + SHIPPING;
            document.getElementById('grand-total').textContent = fmt(grand);
        }

        async function applyVoucher() {
            const code = document.getElementById('vcode').value.trim();
            const msg = document.getElementById('vmsg');
            if (!code) {
                msg.style.color = 'var(--muted)';
                msg.textContent = 'Vui lòng nhập mã.';
                return;
            }

            try {
                const r = await fetch("{{ route('checkout.voucher') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.APP.csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        code
                    })
                });
                const data = await r.json();
                if (r.ok && data.ok) {
                    msg.style.color = 'var(--ok)';
                    msg.textContent = data.message;
                    setTotals(data.discount);
                } else {
                    msg.style.color = 'var(--accent)';
                    msg.textContent = data.message || 'Mã không hợp lệ.';
                    setTotals(0);
                }
            } catch (e) {
                msg.style.color = 'var(--accent)';
                msg.textContent = 'Có lỗi xảy ra, thử lại sau.';
            }
        }
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Xác nhận đặt hàng?',
                text: 'Đơn hàng sẽ được gửi tới cửa hàng để xử lý.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý đặt hàng',
                cancelButtonText: 'Hủy'
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    document.getElementById('checkout-form').submit();
                }

            });
        });
    </script>
@endpush
