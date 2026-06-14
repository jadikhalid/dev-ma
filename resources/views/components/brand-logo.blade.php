@props(['size' => 'md', 'light' => false])

@php
    $sizes = [
        'sm' => ['box' => 'w-8 h-8 text-xs', 'text' => 'text-base'],
        'md' => ['box' => 'w-9 h-9 text-sm', 'text' => 'text-lg'],
        'lg' => ['box' => 'w-11 h-11 text-base', 'text' => 'text-xl'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $textClass = $light ? 'text-white' : 'text-gray-900';
    $accentClass = $light ? 'text-emerald-300' : 'text-indigo-600';
@endphp

<a {{ $attributes->merge(['href' => '/', 'class' => 'flex items-center gap-2']) }}>
    <span class="flex items-center justify-center {{ $s['box'] }} rounded-lg bg-indigo-600 text-white font-bold shrink-0">T</span>
    <span class="font-semibold {{ $s['text'] }} tracking-tight {{ $textClass }}">Talen<span class="{{ $accentClass }}">MA</span></span>
</a>
