@props([
    'url',
    'title' => '',
    'label' => null,
    'inline' => false,
])

@php
    $encodedUrl = rawurlencode($url);
    $encodedText = rawurlencode(trim($title));
    $networks = [
        'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url='.$encodedUrl,
        'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u='.$encodedUrl,
        'X' => 'https://twitter.com/intent/tweet?url='.$encodedUrl.($encodedText !== '' ? '&text='.$encodedText : ''),
        'WhatsApp' => 'https://wa.me/?text='.rawurlencode(trim($title.' '.$url)),
    ];
    $buttonClass = 'shrink-0 inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100';
@endphp

<div
    {{ $attributes->class(['flex flex-col gap-1.5', 'sm:flex-row sm:items-center sm:gap-2' => $inline]) }}
    x-data="{ copied: false }"
    data-share-link
>
    <span class="shrink-0 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
        {{ $label ?? __('talenma.jobs.public_share_url_label') }}
    </span>
    <div class="min-w-0 flex flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
        <a
            href="{{ $url }}"
            target="_blank"
            rel="noopener noreferrer"
            class="min-w-0 truncate text-xs font-medium text-indigo-700 hover:text-indigo-900"
            title="{{ $url }}"
        >{{ $url }}</a>
        <button
            type="button"
            class="{{ $buttonClass }}"
            @click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 1600) })"
        >
            <span x-show="!copied">{{ __('talenma.jobs.public_share_url_copy') }}</span>
            <span x-cloak x-show="copied">{{ __('talenma.jobs.public_share_url_copied') }}</span>
        </button>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($networks as $network => $shareUrl)
            <a
                href="{{ $shareUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="{{ $buttonClass }}"
                title="{{ __('talenma.promo.share_on', ['network' => $network]) }}"
            >{{ $network }}</a>
        @endforeach
    </div>
</div>
