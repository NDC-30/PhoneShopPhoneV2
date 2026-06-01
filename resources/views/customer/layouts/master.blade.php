<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css"
        rel="stylesheet">

</head>

<body class="bg-gray-50">

    @include('customer.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('customer.layouts.footer')

</body>

</html>