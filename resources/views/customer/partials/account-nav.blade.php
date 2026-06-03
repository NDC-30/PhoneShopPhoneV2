{{-- partials/account-nav.blade.php — $active = info|orders --}}
<nav class="acct-nav">
    <a href="{{ route('account.index') }}" class="{{ ($active ?? '') === 'info' ? 'active' : '' }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
        Thông tin tài khoản
    </a>
    <a href="{{ route('account.orders') }}" class="{{ ($active ?? '') === 'orders' ? 'active' : '' }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6M9 13h7M9 17h7"/></svg>
        Đơn hàng của tôi
    </a>
    <a href="{{ route('cart.index') }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2 3h3l2.4 12.5a1 1 0 0 0 1 .8h8.7a1 1 0 0 0 1-.8L21 6H6"/></svg>
        Giỏ hàng
    </a>
    <form method="POST" action="{{ route('customer.logout') }}" style="margin-top:4px;border-top:1px solid var(--line);padding-top:6px">
        @csrf
        <button type="submit" style="width:100%;text-align:left;display:flex;align-items:center;gap:11px;padding:11px 14px;border-radius:9px;font-size:14.5px;color:var(--accent);font-weight:500;background:none;border:none;cursor:pointer">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            Đăng xuất
        </button>
    </form>
</nav>
