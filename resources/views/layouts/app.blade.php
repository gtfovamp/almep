<!DOCTYPE html>
<html lang="{{ $lang ?? 'ru' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Raleway:wght@400&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('styles/global.css') }}" />
    
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>

<style>
    html, body {
        overflow-x: hidden;
        max-width: 100vw;
    }
</style>
