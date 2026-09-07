@props(['size' => 'md', 'light' => false, 'linked' => true, 'badgeBorder' => false])

@php
    // badgeBorder kept for backward compatibility with existing call sites.
    $heights = [
        'sm' => 'h-10',
        'md' => 'h-11',
        'lg' => 'h-[3.25rem]',
    ];
    $heightClass = $heights[$size] ?? $heights['md'];
    $src = $light
        ? asset('images/brand/logo-light.png')
        : asset('images/brand/logo.png');
    $classes = $attributes->merge(['class' => 'inline-flex items-center shrink-0']);
@endphp

@if ($linked)
    <a {{ $classes->merge(['href' => $attributes->get('href', '/')]) }} aria-label="{{ __('talenma.meta.title') }}">
@else
    <div {{ $classes->merge(['aria-disabled' => 'true']) }}>
@endif
    <img
        src="{{ $src }}"
        alt="{{ __('talenma.meta.title') }}"
        width="372"
        height="104"
        decoding="async"
        class="{{ $heightClass }} w-auto max-w-[min(100%,14.5rem)] sm:max-w-[16.5rem] object-contain object-left select-none"
    >
@if ($linked)
    </a>
@else
    </div>
@endif
