<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @class(['h-full' => $viewportFit])>
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
<body @class([
    'font-sans text-gray-900 antialiased',
    'h-full overflow-hidden' => $viewportFit,
])>
    <div @class([
        'flex',
        'min-h-screen' => ! $viewportFit,
        'h-dvh max-h-dvh overflow-hidden' => $viewportFit,
    ])>
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-900 text-white">
            <div class="relative flex flex-col justify-between p-12 w-full">
                <div class="flex items-center justify-between">
                    <x-brand-logo href="{{ route('home') }}" light size="lg" :badge-border="true" />
                    <x-locale-switcher />
                </div>
                <div>
                    <p class="text-xl font-semibold leading-relaxed max-w-md">{{ __('talenma.auth.guest_intro') }}</p>
                </div>
                <p class="text-sm text-indigo-200">&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
            </div>
        </div>
        <div @class([
            'flex-1 flex flex-col items-center bg-gray-50',
            'justify-center px-6 py-12' => ! $viewportFit,
            'h-full min-h-0 overflow-hidden px-4 py-3 sm:px-6 sm:py-4' => $viewportFit,
        ])>
            <div @class([
                'lg:hidden w-full max-w-md flex justify-between items-center',
                'mb-8' => ! $viewportFit,
                'shrink-0 mb-3' => $viewportFit,
            ])>
                <x-brand-logo href="{{ route('home') }}" />
                <x-locale-switcher />
            </div>
            <div @class([
                'w-full max-w-md',
                'flex flex-col min-h-0 flex-1' => $viewportFit,
            ])>
                @isset($title)
                    <div @class(['mb-8' => ! $viewportFit, 'shrink-0 mb-2 sm:mb-3' => $viewportFit])>
                        <h2 @class(['text-2xl font-bold', 'text-xl sm:text-2xl' => $viewportFit])>{{ $title }}</h2>
                        @isset($description)<p @class(['mt-2 text-sm text-gray-600', 'mt-1 text-xs sm:text-sm line-clamp-2' => $viewportFit])>{{ $description }}</p>@endisset
                    </div>
                @endisset
                <div @class([
                    'bg-white rounded-2xl shadow-sm border px-8 py-8',
                    'flex flex-col min-h-0 flex-1 overflow-hidden px-5 py-4 sm:px-6 sm:py-5' => $viewportFit,
                ])>{{ $slot }}</div>
                <p @class([
                    'mt-6 text-center text-sm text-gray-500',
                    'shrink-0 mt-2 sm:mt-3 text-xs sm:text-sm' => $viewportFit,
                ])>
                    <a href="{{ route('home') }}" class="text-indigo-600 font-medium hover:text-indigo-800">{{ __('talenma.nav.back_home') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
