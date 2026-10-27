<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#020617">
    <title>{{ $title ?? 'IT Inventory Management' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fonts
</head>
<body class="min-h-screen bg-white text-slate-900 font-sans antialiased">
    {{ $slot }}
</body>
</html>
