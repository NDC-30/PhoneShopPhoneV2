<nav class="topbar d-flex justify-content-between align-items-center" style="padding: 15px 30px; background: #fff; border-bottom: 1px solid #dee2e6;">
    <div class="topbar-left d-flex align-items-center gap-3">
        <button class="btn-menu border-0 bg-transparent text-dark" id="toggleSidebarLarge" style="font-size: 22px;">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="page-title mb-0 fw-bold text-secondary">
            @yield('page_title', 'Bảng Điều Khiển')
        </h5>
    </div>

    <div class="topbar-right d-flex align-items-center gap-4">
        
        <form action="{{ route('admin.search') }}" method="GET" class="search-box m-0 position-relative">
            <input type="text"
                   name="keyword"
                   class="form-control form-control-sm"
                   style="padding-right: 35px; border-radius: 20px; min-width: 220px;"
                   placeholder="Tìm sản phẩm, mã đơn..."
                   value="{{ request('keyword') }}"
                   required>
            <i class="bi bi-search position-absolute" style="top: 50%; right: 12px; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
            <button type="submit" class="d-none"></button> 
        </form>

        <div class="user-menu dropdown">
            <button class="btn border-0 bg-transparent d-flex align-items-center gap-2 p-0" data-bs-toggle="dropdown" aria-expanded="false">
                
                @if(Auth::check() && Auth::user()->avatar && file_exists(public_path('storage/' . Auth::user()->avatar)))
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle shadow-sm" style="width: 38px; height: 38px; object-fit: cover; border: 2px solid #eef2ff;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; font-weight: bold; font-size: 16px;">
                        {{ mb_substr(Auth::user()->fullname ?? 'A', 0, 1) }}
                    </div>
                @endif
                
                <span class="fw-bold text-dark d-none d-md-block ms-1" style="font-size: 14px;">
                    {{ Auth::user()->fullname ?? 'Administrator' }}
                </span>
                <i class="bi bi-chevron-down text-muted" style="font-size: 12px;"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3" style="border-radius: 12px; min-width: 200px;">
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('admin.settings') }}">
                        <i class="bi bi-person me-3 text-primary fs-5"></i> Hồ sơ cá nhân
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('admin.settings') }}">
                        <i class="bi bi-shield-lock me-3 text-warning fs-5"></i> Đổi mật khẩu
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider my-2">
                </li>
                <li>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger py-2 d-flex align-items-center border-0 bg-white w-100 text-start">
                            <i class="bi bi-box-arrow-right me-3 fs-5"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>