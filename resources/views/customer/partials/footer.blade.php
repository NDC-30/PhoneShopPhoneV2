{{-- resources/views/partials/footer.blade.php --}}
<footer class="site">
    <div class="container">
        <div class="cols">
            <div>
                <div class="fbrand">Phone<span class="dot">.</span>Shop</div>
                <p style="margin-top:14px;max-width:300px;font-size:14px;line-height:1.7">
                    Hệ thống bán lẻ điện thoại chính hãng. Sản phẩm nguyên seal, bảo hành toàn quốc, đổi trả trong 7 ngày.
                </p>
            </div>
            <div>
                <h5>Mua sắm</h5>
                <ul>
                    <li><a href="{{ route('shop.index') }}">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('shop.index', ['sort' => 'newest']) }}">Hàng mới về</a></li>
                    <li><a href="{{ route('shop.index', ['sort' => 'price_desc']) }}">Cao cấp</a></li>
                </ul>
            </div>
            <div>
                <h5>Hỗ trợ</h5>
                <ul>
                    <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                </ul>
            </div>
            <div>
                <h5>Liên hệ</h5>
                <ul>
                    <li>1900 1234</li>
                    <li>hotro@phoneshop.vn</li>
                    <li>Hà Nội, Việt Nam</li>
                </ul>
            </div>
        </div>
        <div class="bottom">
            <span>© {{ date('Y') }} PhoneShop. Đồ án tốt nghiệp.</span>
            <span>Thiết kế tối giản · Made in Vietnam</span>
        </div>
    </div>
</footer>
