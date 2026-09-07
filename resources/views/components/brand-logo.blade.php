@props([
    'size' => 'md',
    'light' => false,
    'white' => false,
    'classic' => false,
    'linked' => true,
    'badgeBorder' => false,
])

@php
    $classes = $attributes->merge(['class' => 'inline-flex items-center shrink-0']);
@endphp

@if ($classic)
    @php
        $sizes = [
            'sm' => ['box' => 'w-8 h-8 text-xs', 'text' => 'text-base'],
            'md' => ['box' => 'w-9 h-9 text-sm', 'text' => 'text-lg'],
            'lg' => ['box' => 'w-11 h-11 text-base', 'text' => 'text-xl'],
        ];
        $s = $sizes[$size] ?? $sizes['md'];
        $textClass = $light ? 'text-white' : 'text-gray-900';
        $accentClass = $light ? 'text-white' : 'text-indigo-600';
        // Phone headers are indigo: keep a readable badge without relying on Tailwind `sm`.
        $badgeClass = $light
            ? 'flex items-center justify-center '.$s['box'].' rounded-lg font-bold shrink-0 bg-white text-indigo-600 ring-1 ring-white/50'
            : 'flex items-center justify-center '.$s['box'].' rounded-lg font-bold shrink-0 bg-indigo-600 text-white'
                .($badgeBorder ? ' ring-1 ring-indigo-200' : '');
        $classicClasses = $attributes->merge(['class' => 'flex items-center gap-2']);
    @endphp

    @if ($linked)
        <a {{ $classicClasses->merge(['href' => $attributes->get('href', '/')]) }}>
    @else
        <div {{ $classicClasses->merge(['aria-disabled' => 'true']) }}>
    @endif
        <span class="{{ $badgeClass }}">MA</span>
        <span class="brand-logo-classic-text font-semibold {{ $s['text'] }} tracking-tight {{ $textClass }}">Talents du <span class="{{ $accentClass }}">Maroc</span></span>
    @if ($linked)
        </a>
    @else
        </div>
    @endif
@else
    @php
        $heights = [
            'sm' => 'h-10',
            'md' => 'h-11',
            'lg' => 'h-[3.25rem]',
        ];
        $heightClass = $heights[$size] ?? $heights['md'];
        $src = match (true) {
            $white => asset('images/brand/logo-white.png'),
            $light => asset('images/brand/logo-light.png'),
            default => asset('images/brand/logo.png'),
        };
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
@endif
