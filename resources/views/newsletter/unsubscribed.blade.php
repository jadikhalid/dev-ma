<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('talenma.newsletter.unsubscribe_title') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full rounded-2xl border bg-white p-8 text-center shadow-sm">
        <h1 class="text-xl font-bold text-slate-900">{{ __('talenma.newsletter.unsubscribe_title') }}</h1>
        <p class="mt-3 text-sm text-slate-600">
            {{ $found ? __('talenma.newsletter.unsubscribe_success') : __('talenma.newsletter.unsubscribe_missing') }}
        </p>
        <a href="{{ route('home') }}" class="mt-6 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900">
            ← {{ __('talenma.newsletter.back_home') }}
        </a>
    </div>
</body>
</html>
