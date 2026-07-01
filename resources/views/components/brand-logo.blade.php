@props(['size' => 'md', 'light' => false, 'linked' => true, 'badgeBorder' => false])

@php
    $sizes = [
        'sm' => ['box' => 'w-8 h-8 text-xs', 'text' => 'text-base'],
        'md' => ['box' => 'w-9 h-9 text-sm', 'text' => 'text-lg'],
        'lg' => ['box' => 'w-11 h-11 text-base', 'text' => 'text-xl'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $textClass = $light ? 'text-white' : 'text-gray-900';
    $accentClass = $light ? 'text-white' : 'text-indigo-600';
    $badgeClass = 'flex items-center justify-center '.$s['box'].' rounded-lg bg-indigo-600 text-white font-bold shrink-0'.($badgeBorder ? ' border border-white' : '');
    $classes = $attributes->merge(['class' => 'flex items-center gap-2']);
@endphp

@if ($linked)
    <a {{ $classes->merge(['href' => $attributes->get('href', '/')]) }}>
@else
    <div {{ $classes }}>
@endif
    <span class="{{ $badgeClass }}">MA</span>
    <span class="font-semibold {{ $s['text'] }} tracking-tight {{ $textClass }}">Talents du <span class="{{ $accentClass }}">Maroc</span></span>
@if ($linked)
    </a>
@else
    </div>
@endif
