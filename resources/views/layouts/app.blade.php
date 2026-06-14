<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('talenma.meta.title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @if (app()->getLocale() === 'ar')
        <link href="https://fonts.bunny.net/css?family=noto-sans-arabic:400,500,600,700&display=swap" rel="stylesheet">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased {{ app()->getLocale() === 'ar' ? '[font-family:Noto_Sans_Arabic,Figtree,sans-serif]' : '' }}">
    <div class="min-h-screen bg-gray-50">
        @include('layouts.navigation')
        @isset($header)
            <header class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{{ $header }}</div>
            </header>
        @endisset
        <main>{{ $slot }}</main>
    </div>
</body>
</html>
