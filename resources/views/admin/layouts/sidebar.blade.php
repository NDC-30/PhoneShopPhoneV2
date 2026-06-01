<aside class="sidebar">
    <div class="sidebar-header">
        <div class="brand-logo">
            <i class="bi bi-phone-vibrate-fill text-primary"></i>
            <span class="fw-bold ms-2 tracking-wide">PHONE<span class="text-primary">SHOP</span></span>
        </div>
        <button class="btn-toggle-sidebar d-lg-none" id="toggleSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="sidebar-nav scroll-nav">
        <p class="nav-heading">QUẢN LÝ CỬA HÀNG</p>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i> <span>Sản Phẩm</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill"></i> <span>Đơn Hàng</span>
        </a>
        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> <span>Khách Hàng</span>
        </a>

        <p class="nav-heading mt-3">DANH MỤC & ƯU ĐÃI</p>
        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags-fill"></i> <span>Danh Mục</span>
        </a>
        <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
            <i class="bi bi-bookmark-star-fill"></i> <span>Thương Hiệu</span>
        </a>
        <a href="{{ route('admin.vouchers.index') }}" class="nav-link">
            <i class="bi bi-ticket-perforated-fill"></i> <span>Vouchers</span>
        </a>

        <p class="nav-heading mt-3">THỐNG KÊ </p>
        <a href="{{ route('admin.revenue.index') }}" class="nav-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line-fill"></i> <span>Báo Cáo Doanh Thu</span>
        </a>
        
    </nav>

    <div class="sidebar-footer">
        <div class="admin-profile">
            <div class="avatar bg-primary text-white">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="admin-info">
                <h6 class="mb-0 text-white text-truncate" style="max-width: 120px;">Administrator</h6>
                <small class="text-muted" style="font-size: 11px;">Trực Tuyến</small>
            </div>
        </div>
        
        <form action="/logout" method="POST" class="w-100">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        </form>
    </div>
</aside>