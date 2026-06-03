@extends('customer.layouts.app')
@section('title', $product->name . ' — PhoneShop')

@section('content')
<div class="container">
    <div class="crumb">
        <a href="{{ route('home') }}">Trang chủ</a> <span>/</span>
        <a href="{{ route('shop.index') }}">Sản phẩm</a> <span>/</span>
        <a href="{{ route('shop.index', ['brand' => $product->brand->slug ?? $product->brand_id]) }}">{{ $product->brand->name ?? '' }}</a>
        <span>/</span> <span>{{ $product->name }}</span>
    </div>

    <div class="pd">
        {{-- GALLERY --}}
        <div class="gallery">
            <div class="gallery-main">
                <img id="mainImg" src="{{ $gallery->first() }}" alt="{{ $product->name }}">
            </div>
            @if($gallery->count() > 1)
            <div class="gallery-thumbs">
                @foreach($gallery->take(6) as $i => $g)
                    <button class="thumb-btn {{ $i===0?'active':'' }}" onclick="setMain(this,'{{ $g }}')">
                        <img src="{{ $g }}" alt="">
                    </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- THÔNG TIN + MUA --}}
        <div class="pd-info">
            <span class="pd-brand">{{ $product->brand->name ?? '' }}</span>
            <h1>{{ $product->name }}</h1>

            <div class="pd-price">
                <span class="now" id="pdPrice">—</span>
                <span class="was" id="pdCompare" style="display:none"></span>
                <span class="off" id="pdOff" style="display:none"></span>
            </div>
            <div class="pd-stock" id="pdStock"><i></i><span>Chọn phiên bản</span></div>

            {{-- CHỌN BIẾN THỂ (chỉ các thuộc tính có nhiều lựa chọn: màu/RAM/ROM...) --}}
            <form id="buyForm">
                @forelse($optionGroups as $group)
                <div class="opt-group" data-group="{{ $loop->index }}">
                    <div class="opt-label">
                        <span>{{ $group['name'] }}</span>
                        <span class="opt-cur" id="cur-{{ $loop->index }}"></span>
                    </div>
                    <div class="opts">
                        @foreach($group['values'] as $val)
                            <button type="button" class="opt"
                                data-group="{{ $loop->parent->index }}"
                                data-value="{{ $val['value_id'] }}"
                                onclick="pickOption(this)">
                                {{ $val['value'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @empty
                @endforelse

                {{-- VOUCHER --}}
                <div class="voucher-box">
                    <div class="vh">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9a3 3 0 0 0 0 6v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3a3 3 0 0 1 0-6V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/><path d="M14 5v14" stroke-dasharray="2 2"/></svg>
                        Mã giảm giá
                    </div>
                    <div class="voucher-row">
                        <input type="text" id="voucherCode" >
                        <button type="button" class="btn btn-line" style="padding:11px 20px" onclick="checkVoucher()">Áp dụng</button>
                    </div>
                    <div class="voucher-msg" id="voucherMsg"></div>
                    @if($vouchers->count())
                    <div class="voucher-chips">
                        @foreach($vouchers as $v)
                            <span class="vchip" onclick="document.getElementById('voucherCode').value='{{ $v->code }}'">{{ $v->code }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- SỐ LƯỢNG + NÚT --}}
                <div class="pd-actions">
                    <div class="qty">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="text" id="qty" value="1" readonly>
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                    <button type="button" class="btn btn-dark" style="flex:1" id="addBtn" onclick="addToCart()" disabled>
                        Thêm vào giỏ
                    </button>
                </div>
                <button type="button" class="btn btn-accent btn-block" id="buyNowBtn" onclick="buyNow()" disabled>Mua ngay</button>
            </form>
        </div>
    </div>

    {{-- THÔNG SỐ KỸ THUẬT + THÔNG TIN SẢN PHẨM (dạng tab) --}}
    <section class="section spec-section">
        <div class="spec-tabs">
            <button type="button" class="spec-tab active" onclick="showSpecTab('specs', this)">Thông số kỹ thuật</button>
            <button type="button" class="spec-tab" onclick="showSpecTab('info', this)">Thông tin sản phẩm</button>
        </div>

        <div id="tab-specs" class="spec-pane">
            <div class="spec-acc open">
                <button type="button" class="spec-acc-head" onclick="toggleAcc(this)">
                    <span>Cấu hình &amp; Thông số</span>
                    <svg class="chev" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="spec-acc-body">
                    @foreach($specs as $s)
                        <div class="spec-row">
                            <span class="spec-k">{{ $s['name'] }}</span>
                            <span class="spec-v">{{ $s['value'] }}</span>
                        </div>
                    @endforeach
                    <div class="spec-row"><span class="spec-k">Thương hiệu</span><span class="spec-v">{{ $product->brand->name ?? '—' }}</span></div>
                    <div class="spec-row"><span class="spec-k">Danh mục</span><span class="spec-v">{{ $product->category->name ?? '—' }}</span></div>
                    <div class="spec-row"><span class="spec-k">Bảo hành</span><span class="spec-v">Chính hãng 12 tháng</span></div>
                </div>
            </div>
        </div>

        <div id="tab-info" class="spec-pane" style="display:none">
            @if($product->description)
                <div class="spec-card" style="padding:26px 24px;line-height:1.85;color:var(--ink-soft)">{!! nl2br(e($product->description)) !!}</div>
            @else
                <p style="color:var(--muted);padding:8px 2px">Chưa có mô tả cho sản phẩm này.</p>
            @endif
        </div>
    </section>

    {{-- SẢN PHẨM TƯƠNG TỰ --}}
    @if($similar->count())
    <section class="section">
        <div class="section-head">
            <div><h2>Sản phẩm tương tự</h2><p>Cùng thương hiệu {{ $product->brand->name ?? '' }}</p></div>
            <a href="{{ route('shop.index', ['brand' => $product->brand->slug ?? $product->brand_id]) }}">Xem thêm →</a>
        </div>
        <div class="grid">
            @foreach($similar as $sp)
                @include('customer.partials.product-card', ['product' => $sp])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
const VARIANTS = @json($variantMap);
const GROUP_COUNT = {{ count($optionGroups) }};
const fmt = n => new Intl.NumberFormat('vi-VN').format(Math.round(n)) + '₫';
let selected = {};          // { groupIndex: value_id }
let currentVariant = null;

function setMain(btn, src){
    document.getElementById('mainImg').src = src;
    document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function pickOption(btn){
    const g = btn.dataset.group, v = parseInt(btn.dataset.value);
    document.querySelectorAll('.opt[data-group="'+g+'"]').forEach(o => o.classList.remove('selected'));
    btn.classList.add('selected');
    selected[g] = v;
    document.getElementById('cur-'+g).textContent = btn.textContent.trim();
    resolveVariant();
}

function resolveVariant(){
    // Phải chọn đủ các nhóm tuỳ chọn (màu/RAM/ROM...) mới tìm biến thể
    if(Object.keys(selected).length < GROUP_COUNT){ return; }
    const want = Object.values(selected);
    // Biến thể khớp nếu CHỨA tất cả value_id đã chọn (các thông số cố định giống nhau ở mọi biến thể)
    currentVariant = VARIANTS.find(v => want.every(id => v.value_ids.includes(id))) || null;
    renderVariant();
}

// Chuyển tab Thông số / Thông tin
function showSpecTab(which, btn){
    document.getElementById('tab-specs').style.display = (which === 'specs') ? '' : 'none';
    document.getElementById('tab-info').style.display  = (which === 'info')  ? '' : 'none';
    document.querySelectorAll('.spec-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// Gập / mở khối thông số
function toggleAcc(btn){
    btn.parentElement.classList.toggle('open');
}

function renderVariant(){
    const priceEl=document.getElementById('pdPrice'), cmpEl=document.getElementById('pdCompare'),
          offEl=document.getElementById('pdOff'), stockEl=document.getElementById('pdStock'),
          addBtn=document.getElementById('addBtn'), buyBtn=document.getElementById('buyNowBtn');

    if(!currentVariant){
        priceEl.textContent='—'; cmpEl.style.display='none'; offEl.style.display='none';
        addBtn.disabled=true; buyBtn.disabled=true;
        stockEl.className='pd-stock'; stockEl.innerHTML='<i></i><span>Phiên bản này tạm hết</span>';
        return;
    }
    priceEl.textContent = fmt(currentVariant.price);
    if(currentVariant.compare_price > currentVariant.price){
        cmpEl.style.display='inline'; cmpEl.textContent = fmt(currentVariant.compare_price);
        const off = Math.round((currentVariant.compare_price-currentVariant.price)/currentVariant.compare_price*100);
        offEl.style.display='inline'; offEl.textContent='-'+off+'%';
    } else { cmpEl.style.display='none'; offEl.style.display='none'; }

    if(currentVariant.image){ document.getElementById('mainImg').src = currentVariant.image; }

    if(currentVariant.stock > 0){
        stockEl.className='pd-stock'; stockEl.innerHTML='<i></i><span>Còn hàng ('+currentVariant.stock+')</span>';
        addBtn.disabled=false; buyBtn.disabled=false;
    } else {
        stockEl.className='pd-stock out'; stockEl.innerHTML='<i></i><span>Hết hàng</span>';
        addBtn.disabled=true; buyBtn.disabled=true;
    }
    // giới hạn số lượng theo tồn
    const q=document.getElementById('qty'); if(parseInt(q.value) > currentVariant.stock) q.value=Math.max(1,currentVariant.stock);
}

function changeQty(d){
    const q=document.getElementById('qty'); let n=parseInt(q.value)+d;
    const max = currentVariant ? currentVariant.stock : 99;
    if(n<1) n=1; if(n>max) n=max; q.value=n;
}

async function postJSON(url, body){
    const r = await fetch(url, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.APP.csrf,'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify(body)
    });
    return { ok:r.ok, data: await r.json() };
}

async function addToCart(redirect=false){
    if(!currentVariant){ return; }
    const btn=document.getElementById('addBtn'); btn.disabled=true; btn.textContent='Đang thêm…';
    const {ok,data} = await postJSON("{{ route('cart.add') }}", {
        variant_id: currentVariant.variant_id, quantity: parseInt(document.getElementById('qty').value)
    });
    btn.disabled=false; btn.textContent='Thêm vào giỏ';
    if(ok){
        // cập nhật badge
        const badge=document.querySelector('.cart-btn .badge');
        if(badge){ badge.textContent=data.count; } else {
            const c=document.createElement('span'); c.className='badge'; c.textContent=data.count;
            document.querySelector('.cart-btn').appendChild(c);
        }
        if(redirect){ window.location="{{ route('cart.index') }}"; }
        else { btn.textContent='✓ Đã thêm'; setTimeout(()=>btn.textContent='Thêm vào giỏ',1200); }
    } else { alert(data.message || 'Có lỗi xảy ra'); }
}
function buyNow(){ addToCart(true); }

async function checkVoucher(){
    const code=document.getElementById('voucherCode').value.trim();
    const msg=document.getElementById('voucherMsg');
    if(!code){ msg.className='voucher-msg err'; msg.textContent='Vui lòng nhập mã.'; return; }
    const amount = currentVariant ? currentVariant.price * parseInt(document.getElementById('qty').value) : 0;
    const {ok,data} = await postJSON("{{ route('voucher.preview') }}", { code, amount });
    msg.className = 'voucher-msg ' + (ok ? 'ok' : 'err');
    msg.textContent = data.message;
}

// Tự chọn biến thể còn hàng đầu tiên
window.addEventListener('DOMContentLoaded', () => {
    const first = VARIANTS.find(v => v.stock > 0) || VARIANTS[0];
    if(first){
        first.value_ids.forEach(vid => {
            const btn = document.querySelector('.opt[data-value="'+vid+'"]');
            if(btn) pickOption(btn);
        });
    }
});
</script>
@endpush
