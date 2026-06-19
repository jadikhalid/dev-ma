@props(['size' => 'md', 'variant' => 'light'])

@php
    $sizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
    ];
    $iconSize = $sizes[$size] ?? $sizes['md'];

    $linkClass = $variant === 'dark'
        ? 'border-gray-600 text-gray-400 hover:text-white hover:border-gray-400 hover:bg-gray-800/60'
        : 'border-gray-300 text-gray-500 hover:text-indigo-600 hover:border-indigo-400 hover:bg-indigo-50/50';

    $networks = [
        'x' => [
            'label' => 'X',
            'url' => config('talenma.social.x'),
            'viewBox' => '0 0 24 24',
            'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
        ],
        'instagram' => [
            'label' => 'Instagram',
            'url' => config('talenma.social.instagram'),
            'viewBox' => '0 0 24 24',
            'path' => 'M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4A5.8 5.8 0 0 1 16.2 22H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6m9.65 1.5a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
        ],
        'linkedin' => [
            'label' => 'LinkedIn',
            'url' => config('talenma.social.linkedin'),
            'viewBox' => '0 0 24 24',
            'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM7.114 20.452H3.56V9h3.554v11.452z',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'url' => config('talenma.social.youtube'),
            'viewBox' => '0 0 24 24',
            'path' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
        ],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
    @foreach ($networks as $network)
        <a href="{{ $network['url'] }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="{{ $network['label'] }}"
           class="flex items-center justify-center w-9 h-9 rounded-full border {{ $linkClass }} transition-all duration-300 ease-in-out">
            <svg class="{{ $iconSize }}" viewBox="{{ $network['viewBox'] }}" fill="currentColor" aria-hidden="true">
                <path d="{{ $network['path'] }}"/>
            </svg>
        </a>
    @endforeach
</div>
