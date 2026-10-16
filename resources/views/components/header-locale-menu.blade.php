@props([
    'align' => 'right',
    'tone' => 'dark',
])

@php
    $locales = [
        'fr' => ['label' => 'FR', 'name' => 'Français'],
        'en' => ['label' => 'EN', 'name' => 'English'],
    ];
    $current = app()->getLocale();
    $currentLabel = $locales[$current]['label'] ?? strtoupper($current);
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @header-menu-open.window="open = false" data-header-locale-switcher>
    <button
        type="button"
        @class([
            'inline-flex items-center gap-1 rounded-[5px] py-2 text-sm font-semibold sm:gap-1.5 sm:px-2',
            'px-0 text-white hover:bg-white/10' => $tone === 'light',
            'px-1 text-gray-800 hover:bg-gray-50' => $tone !== 'light',
        ])
        @click="open = ! open; if (open) $dispatch('locale-menu-open')"
        :aria-expanded="open.toString()"
        aria-haspopup="menu"
        aria-label="{{ __('talenma.nav.locale_menu') }}"
    >
        <svg @class(['h-4 w-4', 'text-white' => $tone === 'light', 'text-gray-500' => $tone !== 'light']) fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.2 2.4 3.3 5.1 3.3 9S14.2 18.6 12 21c-2.2-2.4-3.3-5.1-3.3-9S9.8 5.4 12 3Z"/>
        </svg>
        <span>{{ $currentLabel }}</span>
        <svg
            @class([
                'h-3.5 w-3.5 shrink-0 transition-transform duration-200',
                'text-white' => $tone === 'light',
                'text-gray-400' => $tone !== 'light',
            ])
            :class="open ? 'rotate-180' : 'rotate-0'"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        @class([
            'absolute z-50 mt-1 w-44 overflow-hidden rounded-xl border border-gray-100 bg-white py-1 shadow-lg',
            'left-0' => $align === 'left',
            'right-0' => $align !== 'left',
        ])
        role="menu"
    >
        @foreach ($locales as $code => $locale)
            <a
                href="{{ route('locale.switch', $code) }}"
                role="menuitem"
                @if ($current === $code) aria-current="true" @endif
                class="flex items-center justify-between gap-3 px-3 py-2 text-sm {{ $current === $code ? 'font-semibold text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}"
                @click="open = false"
            >
                <span>{{ $locale['name'] }}</span>
                @if ($current === $code)
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true" data-locale-check>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
