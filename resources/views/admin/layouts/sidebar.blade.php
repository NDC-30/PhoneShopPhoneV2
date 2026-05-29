<aside class="sidebar">
    <div class="sidebar-header">
        <h4 class="mb-0"><i class="bi bi-phone"></i> Phone Shop</h4>
        <button class="btn-toggle-sidebar d-lg-none" id="toggleSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box"></i> Sản Phẩm
        </a>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag"></i> Đơn Hàng
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-people"></i> Khách Hàng
        </a>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Danh Mục
        </a>
        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="bi bi-bookmark"></i> Thương Hiệu
        </a>
        <a href="{{ route('admin.vouchers.index') }}" class="nav-link">
            <i class="bi bi-ticket-perforated"></i> Vouchers
        </a>
        <a href="#" class="nav-link">
            <i class="bi bi-gear"></i> Cài Đặt
        </a>
    </nav>
    <div class="sidebar-footer">
        <form action="/logout" method="POST">
    @csrf

    <button type="submit">
        Đăng xuất
    </button>
</form>
    </div>
</aside>