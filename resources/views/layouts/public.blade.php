<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('talenma.meta.title') }}</title>
    <meta name="description" content="{{ __('talenma.meta.description') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @if (app()->getLocale() === 'ar')
        <link href="https://fonts.bunny.net/css?family=noto-sans-arabic:400,500,600,700&display=swap" rel="stylesheet">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-white {{ app()->getLocale() === 'ar' ? '[font-family:Noto_Sans_Arabic,Figtree,sans-serif]' : '' }}">
    <x-public-nav />
    <main>@yield('content')</main>
    <x-public-footer />
</body>
</html>
