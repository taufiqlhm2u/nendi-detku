<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Primary SEO --}}
    <title>{{ $seo['title'] ?? config('app.name', 'Nendi Detku') }} | Catat Keuangan Pribadi</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Nendi Detku — aplikasi pencatat keuangan pribadi. Catat pemasukan, pengeluaran, dan pantau saldo kamu dengan mudah dan cepat.' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'catat keuangan, money tracker, aplikasi keuangan pribadi, catat pemasukan pengeluaran, nendi detku' }}">
    <meta name="author" content="Nendi Detku">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, WhatsApp, dll) --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seo['title'] ?? 'Nendi Detku — Catat Keuangan Pribadi' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? 'Catat pemasukan, pengeluaran, dan pantau saldo kamu dengan mudah dan cepat.' }}">
    <meta property="og:image" content="{{ $seo['og_image'] ?? asset('images/og-default.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Nendi Detku">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['title'] ?? 'Nendi Detku — Catat Keuangan Pribadi' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'Catat pemasukan, pengeluaran, dan pantau saldo kamu dengan mudah dan cepat.' }}">
    {{-- <meta name="twitter:image" content="{{ $seo['og_image'] ?? asset('images/og-default.png') }}"> --}}

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/apple.png') }}">
    {{-- <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}"> --}}
    <link rel="apple-touch-icon" href="{{ asset('images/apple.png') }}">
    {{-- <link rel="manifest" href="{{ asset('site.webmanifest') }}"> --}}
    <meta name="theme-color" content="#4F46E5">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{ $slot }}
</body>
</html>