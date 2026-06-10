@extends('admin.layouts.master')
@section('title', 'Cài đặt Tài khoản')
@section('page_title', 'Cài đặt Tài khoản')
dd($request->all());
@section('content')
<div class="content-area">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body py-5">
                        
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow border border-3 border-white" style="width: 120px; height: 120px; overflow: hidden; background-color: #0d6efd;">
                                @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                                    <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <span id="avatarText" class="text-white fw-bold" style="font-size: 45px;">{{ mb_substr($user->fullname ?? 'A', 0, 1) }}</span>
                                    <img id="avatarPreview" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                @endif
                            </div>
                            <label for="avatarInput" class="btn btn-sm btn-light rounded-circle position-absolute bottom-0 end-0 shadow-sm border" style="cursor: pointer; padding: 4px 8px;">
                                <i class="bi bi-camera-fill text-secondary"></i>
                            </label>
                        </div>

                        <div class="px-3 mb-3">
                            <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm" accept="image/*">
                            <small class="text-muted d-block mt-1">Định dạng: JPG, PNG, JPEG. Tối đa 2MB</small>
                        </div>

                        <h5 class="fw-bold mb-1">{{ $user->fullname ?? 'Administrator' }}</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-envelope-fill me-1"></i> {{ $user->email ?? 'admin@phoneshop.com' }}</p>
                        
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-shield-check me-1"></i> Quản trị viên</span>
                        
                        <hr class="my-4 text-muted">
                        
                        <div class="d-flex justify-content-between text-start small px-2">
                            <span class="text-muted">Ngày tham gia:</span>
                            <span class="fw-bold">{{ $user->created_at ? date('d/m/Y', strtotime($user->created_at)) : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold py-3 text-primary">
                        <i class="bi bi-gear-fill me-1"></i> Cập Nhật Thông Tin Tài Khoản
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" value="{{ $user->fullname }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Địa chỉ Email </label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="Nhập số điện thoại liên hệ...">
                            </div>
                        </div>

                        <h6 class="fw-bold text-secondary border-bottom pb-2 mb-4"><i class="bi bi-key-fill me-1"></i> Thay đổi mật khẩu (Bỏ trống nếu không đổi)</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mật khẩu mới</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nhập lại mật khẩu mới</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2 fw-bold text-secondary">
                                Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Cập Nhật Cấu Hồng
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('avatarInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatarPreview');
                const textPlaceholder = document.getElementById('avatarText');
                
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                if (textPlaceholder) {
                    textPlaceholder.style.display = 'none';
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection