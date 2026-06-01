@extends('admin.layouts.master')
@section('title', 'Quản Lý Khách Hàng')
@section('page_title', 'Danh Sách Khách Hàng')

@section('content')
<div class="content-area">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white p-3 rounded-3">
                <div class="small text-uppercase fw-bold opacity-75">Tổng số khách hàng</div>
                <h3 class="fw-bold my-2">{{ $customers->count() }} thành viên</h3>
                <div class="small"><i class="bi bi-people-fill"></i> Dữ liệu tài khoản người dùng</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold py-3 text-secondary">
            <i class="bi bi-person-lines-fill me-1"></i> Quản Lý Tài Khoản Khách Hàng
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">STT</th>
                        <th>Họ và Tên</th>
                        <th>Email</th>
                        <th>Số Điện Thoại</th>
                        <th>Ngày Đăng Ký</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $index => $customer)
                    <tr>
                        <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px; background-color: #eef2ff;">
                                    {{ mb_substr($customer->fullname ?? 'K', 0, 1) }}
                                </div>
                                <span class="fw-bold text-dark">{{ $customer->fullname }}</span>
                            </div>
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone ?? 'N/A' }}</td>
                        <td>{{ $customer->created_at ? date('d/m/Y', strtotime($customer->created_at)) : 'N/A' }}</td>
                        <td>
                            @if(($customer->status ?? 1) == 1)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Hoạt động</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i> Đã khóa</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.customers.toggleStatus', $customer->getKey()) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hoạt động của tài khoản này không?');">
                                @csrf
                                @if(($customer->status ?? 1) == 1)
                                    <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm">
                                        <i class="bi bi-lock"></i> Khóa tài khoản
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-success text-white shadow-sm">
                                        <i class="bi bi-unlock"></i> Mở khóa
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">Hệ thống chưa có tài khoản khách hàng nào đăng ký!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection