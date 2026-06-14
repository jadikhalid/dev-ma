@php
    $locales = [
        'fr' => ['label' => 'FR', 'name' => 'Français'],
        'en' => ['label' => 'EN', 'name' => 'English'],
        'ar' => ['label' => 'AR', 'name' => 'العربية'],
    ];
    $current = app()->getLocale();
@endphp

<div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-white p-0.5 text-xs font-semibold">
    @foreach ($locales as $code => $locale)
        <a href="{{ route('locale.switch', $code) }}"
           class="px-2.5 py-1 rounded-md transition {{ $current === $code ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}"
           title="{{ $locale['name'] }}">
            {{ $locale['label'] }}
        </a>
    @endforeach
</div>
