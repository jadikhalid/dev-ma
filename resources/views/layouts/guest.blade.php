<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('talenma.meta.title') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-900 text-white">
            <div class="relative flex flex-col justify-between p-12 w-full">
                <div class="flex items-center justify-between">
                    <x-brand-logo href="{{ route('home') }}" light size="lg" />
                    <x-locale-switcher />
                </div>
                <div>
                    <h1 class="text-3xl font-bold leading-tight">{{ __('talenma.auth.guest_title') }}</h1>
                    <p class="mt-4 text-indigo-100 max-w-md">{{ __('talenma.auth.guest_desc') }}</p>
                </div>
                <p class="text-sm text-indigo-200">&copy; {{ date('Y') }} On</p>
            </div>
        </div>
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50">
            <div class="lg:hidden w-full max-w-md flex justify-between items-center mb-8">
                <x-brand-logo href="{{ route('home') }}" />
                <x-locale-switcher />
            </div>
            <div class="w-full max-w-md">
                @isset($title)
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold">{{ $title }}</h2>
                        @isset($description)<p class="mt-2 text-sm text-gray-600">{{ $description }}</p>@endisset
                    </div>
                @endisset
                <div class="bg-white rounded-2xl shadow-sm border px-8 py-8">{{ $slot }}</div>
                <p class="mt-6 text-center text-sm text-gray-500">
                    <a href="{{ route('home') }}" class="text-indigo-600 font-medium hover:text-indigo-800">{{ __('talenma.nav.back_home') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
