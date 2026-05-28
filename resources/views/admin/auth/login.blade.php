<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
        }

        .login-box{
            width:400px;
            background:#fff;
            padding:40px;
            border-radius:16px;
            box-shadow:0 10px 30px rgba(0,0,0,0.2);
        }

        .login-box h2{
            text-align:center;
            margin-bottom:30px;
            color:#111827;
        }

        .input-group{
            margin-bottom:20px;
        }

        .input-group label{
            display:block;
            margin-bottom:8px;
            color:#374151;
            font-size:14px;
        }

        .input-group input{
            width:100%;
            padding:12px;
            border:1px solid #d1d5db;
            border-radius:8px;
            outline:none;
            transition:0.3s;
        }

        .input-group input:focus{
            border-color:#4f46e5;
        }

        .btn-login{
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            background:#4f46e5;
            color:#fff;
            font-size:16px;
            cursor:pointer;
            transition:0.3s;
        }

        .btn-login:hover{
            background:#4338ca;
        }

        .error{
            background:#fee2e2;
            color:#dc2626;
            padding:10px;
            border-radius:8px;
            margin-bottom:20px;
            font-size:14px;
        }
    </style>
</head>
<body>

<div class="login-box">

    <h2>Admin Login</h2>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/admin/login">
        @csrf

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Nhập email">
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Nhập mật khẩu">
        </div>

        <button type="submit" class="btn-login">
            Đăng nhập
        </button>
    </form>

</div>

</body>
</html>