@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'w-10 h-10 text-xs',
        'md' => 'w-16 h-16 text-lg',
        'lg' => 'w-24 h-24 text-2xl',
        'xl' => 'w-32 h-32 text-3xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatarUrl())
    <img
        src="{{ $user->avatarUrl() }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => 'rounded-full object-cover shrink-0 '.$sizeClass]) }}
    >
@else
    <span
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold shrink-0 '.$sizeClass]) }}
        aria-hidden="true"
    >
        {{ $user->initials() }}
    </span>
@endif
