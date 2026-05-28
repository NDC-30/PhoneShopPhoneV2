<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt tài khoản</title>

    <style>

        body{
            font-family: Arial;
            background:#f3f4f6;
            padding:40px;
        }

        .card{
            width:600px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        h2{
            margin-bottom:20px;
        }

        .item{
            margin-bottom:20px;
        }

        label{
            display:block;
            margin-bottom:8px;
            font-weight:bold;
        }

        input{
            width:100%;
            padding:12px;
            border:1px solid #d1d5db;
            border-radius:8px;
        }

        button{
            margin-top:20px;
            padding:12px 20px;
            background:#2563eb;
            color:white;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        button:hover{
            background:#1d4ed8;
        }

        .back{
            display:inline-block;
            margin-top:20px;
            margin-left:10px;
            text-decoration:none;
        }

        .success{
            background:#dcfce7;
            color:#166534;
            padding:12px;
            border-radius:8px;
            margin-bottom:20px;
        }

    </style>
</head>
<body>

<div class="card">

    <h2>Cài đặt tài khoản Admin</h2>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">

        @csrf

        <div class="item">
            <label>Họ tên</label>

            <input type="text"
                   name="fullname"
                   value="{{ Auth::user()->fullname }}">
        </div>

        <div class="item">
            <label>Email</label>

            <input type="email"
                   value="{{ Auth::user()->email }}"
                   disabled>
        </div>

        <div class="item">
            <label>Mật khẩu mới</label>

            <input type="password"
                   name="password"
                   placeholder="Nhập mật khẩu mới">
        </div>

        <div class="item">
            <label>Nhập lại mật khẩu</label>

            <input type="password"
                   name="password_confirmation"
                   placeholder="Nhập lại mật khẩu">
        </div>

        <button type="submit">
            Cập nhật tài khoản
        </button>

        <a href="/admin" class="back">
            Quay lại Dashboard
        </a>

    </form>

</div>

</body>
</html>