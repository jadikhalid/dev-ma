@props(['company'])

@php
    $tag = ! empty($company['url']) ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($tag === 'a') href="{{ $company['url'] }}" data-company-marquee-link @endif
    {{ $attributes->class('group mx-2 flex h-full w-[20rem] shrink-0 items-start gap-4 rounded-xl border border-gray-100 bg-white px-5 py-4 shadow-sm transition duration-300 sm:mx-2.5 sm:w-[24rem] sm:px-6 sm:py-5 [@media(hover:hover)_and_(pointer:fine)]:hover:-translate-y-0.5 [@media(hover:hover)_and_(pointer:fine)]:hover:border-indigo-200 [@media(hover:hover)_and_(pointer:fine)]:hover:bg-indigo-50/40 [@media(hover:hover)_and_(pointer:fine)]:hover:shadow-md') }}
>
    <div class="min-w-0 flex-1">
        <p class="line-clamp-1 text-sm font-bold text-gray-900 sm:text-base">{{ $company['name'] }}</p>
        @if (! empty($company['sector']))
            <p class="mt-2">
                <span class="inline-flex max-w-full items-center truncate rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-100">{{ $company['sector'] }}</span>
            </p>
        @endif
        @if (! empty($company['excerpt']))
            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-500">{{ $company['excerpt'] }}</p>
        @endif
    </div>
    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white sm:h-16 sm:w-16">
        @if (! empty($company['logo_url']))
            <img src="{{ $company['logo_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
        @else
            <span aria-hidden="true">{{ $company['initials'] }}</span>
        @endif
    </div>
</{{ $tag }}>
