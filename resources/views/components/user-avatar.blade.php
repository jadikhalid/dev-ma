@props(['user', 'size' => 'md', 'initialsOnly' => false])

@php
    $sizes = [
        'xs' => 'w-8 h-8 text-xs',
        'sm' => 'w-10 h-10 text-xs',
        'md' => 'w-16 h-16 text-lg',
        'lg' => 'w-24 h-24 text-2xl',
        'xl' => 'w-32 h-32 text-3xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $avatarUrl = (! $initialsOnly) ? $user->avatarUrl() : null;
@endphp

@if ($avatarUrl)
    <img
        src="{{ $avatarUrl }}"
        alt="{{ $user->name }}"
        data-media-avatar
        {{ $attributes->merge(['class' => 'rounded-full object-cover shrink-0 '.$sizeClass]) }}
    >
@else
    <span
        data-media-avatar-fallback
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-bold shrink-0 '.$sizeClass]) }}
        aria-hidden="true"
    >
        {{ $user->initials() }}
    </span>
@endif
