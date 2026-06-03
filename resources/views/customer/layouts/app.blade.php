{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PhoneShop — Cửa hàng điện thoại chính hãng')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    @include('customer.partials.header')

    <main>
        @if(session('success'))
            <div class="container"><div class="alert success" style="margin-top:20px">{{ session('success') }}</div></div>
        @endif
        @if(session('error'))
            <div class="container"><div class="alert error" style="margin-top:20px">{{ session('error') }}</div></div>
        @endif

        @yield('content')
    </main>

    @include('customer.partials.footer')

    <script>
        window.APP = { csrf: document.querySelector('meta[name=csrf-token]').content };
    </script>
    @stack('scripts')
</body>
</html>
