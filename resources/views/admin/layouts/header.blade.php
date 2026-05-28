<nav class="topbar">
    <div class="topbar-left">
        <button class="btn-menu" id="toggleSidebarLarge">
            <i class="bi bi-list"></i>
        </button>

        <h5 class="page-title mb-0">
            @yield('page_title', 'Bảng Điều Khiển')
        </h5>
    </div>

    <div class="topbar-right">

        <div class="search-box">
            <input type="text"
                   class="form-control form-control-sm"
                   placeholder="Tìm kiếm...">

            <i class="bi bi-search"></i>
        </div>

        <div class="notifications">
            <button class="btn btn-icon">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger">3</span>
            </button>
        </div>

        <div class="user-menu">

            <button class="btn btn-icon dropdown-toggle"
                    data-bs-toggle="dropdown">

                <i class="bi bi-person-circle"></i>

                <span class="ms-1">
                    {{ Auth::user()->fullname }}
                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <!-- Tài khoản -->
                <li>
                    <a class="dropdown-item"
                       href="{{ route('admin.settings') }}">

                        <i class="bi bi-person me-2"></i>

                        Tài khoản
                    </a>
                </li>

                <!-- Cài đặt -->
                <li>
                    <a class="dropdown-item"
                       href="{{ route('admin.settings') }}">

                        <i class="bi bi-gear me-2"></i>

                        Cài đặt
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <!-- Logout -->
                <li>

                    <form action="/logout" method="POST">
                        @csrf

                        <button type="submit"
                                class="dropdown-item text-danger border-0 bg-white w-100 text-start">

                            <i class="bi bi-box-arrow-right me-2"></i>

                            Đăng xuất
                        </button>
                    </form>

                </li>

            </ul>

        </div>
    </div>
</nav>