@props(['profile', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'w-8 h-8 text-xs',
        'sm' => 'w-10 h-10 text-xs',
        'md' => 'w-16 h-16 text-lg',
        'lg' => 'w-24 h-24 text-2xl',
        'xl' => 'w-32 h-32 text-3xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $label = $profile?->displayName() ?: __('talenma.company.name');
    $logoUrl = $profile?->logoUrl();
@endphp

@if ($logoUrl)
    <img
        src="{{ $logoUrl }}"
        alt="{{ $label }}"
        data-media-company-logo
        {{ $attributes->class(['object-cover shrink-0 rounded-full', $sizeClass]) }}
    >
@else
    <span
        data-media-company-logo-fallback
        {{ $attributes->class(['inline-flex items-center justify-center bg-emerald-100 text-emerald-700 font-bold shrink-0 rounded-full', $sizeClass]) }}
        aria-hidden="true"
    >
        {{ $profile?->initials() ?? '—' }}
    </span>
@endif
