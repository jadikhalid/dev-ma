@extends('layouts.public')

@php
    $isCompanyFaq = \App\Support\PortalHost::isCompanyHost();
    $faqCopy = $isCompanyFaq
        ? [
            'meta_title' => __('talenma.company_faq.meta_title'),
            'meta_description' => __('talenma.company_faq.meta_description'),
            'badge' => __('talenma.company_faq.badge'),
            'title' => __('talenma.company_faq.title'),
            'intro' => __('talenma.company_faq.intro'),
            'contact' => __('talenma.company_faq.contact'),
            'contact_link' => __('talenma.company_faq.contact_link'),
            'cta_secondary_label' => __('talenma.company_faq.cta_demo'),
            'cta_secondary_url' => route('company.demo'),
            'cta_primary_label' => __('talenma.company_faq.cta_trial'),
            'cta_primary_url' => route('company.trial'),
            'items' => __('talenma.company_faq.items'),
        ]
        : [
            'meta_title' => __('talenma.footer.faq_meta_title'),
            'meta_description' => __('talenma.footer.faq_meta_description'),
            'badge' => __('talenma.footer.faq_badge'),
            'title' => __('talenma.footer.faq'),
            'intro' => __('talenma.footer.faq_intro'),
            'contact' => __('talenma.footer.faq_contact'),
            'contact_link' => __('talenma.footer.faq_contact_link'),
            'cta_secondary_label' => __('talenma.career_pages.how_cta_account'),
            'cta_secondary_url' => route('register'),
            'cta_primary_label' => __('talenma.footer.faq_contact_link'),
            'cta_primary_url' => route('contact'),
            'items' => __('talenma.footer.faq_items'),
        ];

    $faqItems = is_array($faqCopy['items'] ?? null) ? $faqCopy['items'] : [];
@endphp

@section('title', $faqCopy['meta_title'])
@section('meta_description', $faqCopy['meta_description'])

@push('head')
@php
    $faqSchemaEntities = collect($faqItems)->map(function (array $item) {
        $parts = array_filter([
            $item['a'] ?? null,
            ! empty($item['list']) && is_array($item['list'])
                ? implode(' ', array_map(fn ($line) => '• '.$line, $item['list']))
                : null,
            $item['a_after'] ?? null,
        ]);

        return [
            '@type' => 'Question',
            'name' => $item['q'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => trim(implode(' ', $parts)),
            ],
        ];
    })->values()->all();
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqSchemaEntities,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
<div @class(['relative' => ! $isCompanyFaq])>
    <section
        @class([
            'relative overflow-hidden',
            '-mt-20 pt-20 sm:-mt-[4.25rem] sm:pt-[4.25rem]' => ! $isCompanyFaq,
        ])
        @if ($isCompanyFaq) data-faq-hero @endif
    >
        @unless ($isCompanyFaq)
            <div class="how-hero-frame" aria-hidden="true">
                <div class="how-wash how-wash-lilac -left-24 -top-40 h-[28rem] w-[28rem] opacity-50 sm:-left-32 sm:-top-72 sm:h-[760px] sm:w-[760px]"></div>
                <div class="how-wash how-wash-sky -right-24 -top-24 h-[22rem] w-[22rem] opacity-30 sm:-top-40 sm:right-0 sm:h-[700px] sm:w-[700px]"></div>
            </div>
        @endunless

        <div class="home-shell relative grid gap-10 py-14 sm:gap-12 sm:py-20 lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)] lg:gap-16 lg:py-24">
            <div class="flex flex-col gap-3.5">
                <span class="text-[13px] font-bold tracking-[0.12em] text-[#5546F0]">{{ $faqCopy['badge'] }}</span>
                <h1 class="text-3xl font-extrabold leading-[1.08] tracking-[-0.03em] text-gray-950 sm:text-[2.625rem]">{{ $faqCopy['title'] }}</h1>
                <p class="text-base text-gray-600">{{ $faqCopy['intro'] }}</p>
                <p class="text-base text-gray-600">
                    {{ $faqCopy['contact'] }}
                    <a href="{{ route('contact') }}" class="font-semibold text-[#5546F0] hover:text-indigo-800">{{ $faqCopy['contact_link'] }}</a>
                </p>
            </div>

            <div class="flex flex-col gap-3" x-data="{ open: 0 }">
                @foreach ($faqItems as $index => $item)
                    <div
                        class="rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-5 shadow-none transition-shadow duration-300 ease-out sm:px-6"
                        :class="open === {{ $index }} ? 'shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]' : ''"
                    >
                        <button
                            type="button"
                            id="faq-question-{{ $index }}"
                            class="flex w-full items-center justify-between gap-4 text-left text-[17px] font-semibold text-gray-950"
                            @click="open = open === {{ $index }} ? null : {{ $index }}"
                            :aria-expanded="(open === {{ $index }}).toString()"
                            aria-controls="faq-answer-{{ $index }}"
                        >
                            <span>{{ $item['q'] }}</span>
                            <span
                                class="inline-flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full text-xl transition-colors duration-300 ease-out"
                                :class="open === {{ $index }} ? 'bg-[#0D0B2B] text-white' : 'bg-gray-100 text-gray-950'"
                                aria-hidden="true"
                                x-text="open === {{ $index }} ? '−' : '+'"
                            ></span>
                        </button>

                        <div
                            class="grid transition-[grid-template-rows] duration-300 ease-out"
                            :class="open === {{ $index }} ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
                        >
                            <div class="min-h-0 overflow-hidden">
                                <div
                                    id="faq-answer-{{ $index }}"
                                    class="mt-3 space-y-3 text-base leading-relaxed text-gray-600 transition-opacity duration-300 ease-out"
                                    role="region"
                                    aria-labelledby="faq-question-{{ $index }}"
                                    :class="open === {{ $index }} ? 'opacity-100' : 'opacity-0'"
                                    :aria-hidden="(open !== {{ $index }}).toString()"
                                >
                                    @if (filled($item['a'] ?? null))
                                        <p>{{ $item['a'] }}</p>
                                    @endif

                                    @if (! empty($item['list']) && is_array($item['list']))
                                        <ul class="list-disc space-y-2 pl-5">
                                            @foreach ($item['list'] as $line)
                                                <li>{{ $line }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if (filled($item['a_after'] ?? null))
                                        <p>{{ $item['a_after'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="home-shell pb-14 sm:pb-20 lg:pb-24">
        <div class="relative flex flex-col items-start justify-between gap-8 overflow-hidden rounded-[36px] px-6 py-12 sm:px-12 sm:py-14 lg:flex-row lg:items-center lg:px-16" style="background:linear-gradient(120deg,#2563EB 0%,#5546F0 45%,#8B5CF6 100%)">
            <div aria-hidden="true" class="pointer-events-none absolute -right-10 -top-40 h-[28rem] w-[28rem] rounded-full bg-[radial-gradient(circle,rgba(235,186,110,1)_0%,rgba(255,255,255,0)_68%)] opacity-40"></div>
            <div class="relative max-w-xl">
                <h2 class="m-0 text-3xl font-extrabold leading-tight tracking-[-0.03em] text-white sm:text-[2.5rem]">
                    {{ $faqCopy['contact'] }}
                </h2>
                <p class="mt-3 m-0 text-base text-[#E0E7FF]">{{ $faqCopy['intro'] }}</p>
            </div>
            <div class="relative flex flex-wrap gap-3">
                <a href="{{ $faqCopy['cta_primary_url'] }}" class="inline-flex items-center justify-center rounded-[14px] bg-white px-6 py-4 text-base font-bold text-[#0D0B2B] hover:bg-indigo-50">
                    {{ $faqCopy['cta_primary_label'] }}
                </a>
                <a href="{{ $faqCopy['cta_secondary_url'] }}" class="inline-flex items-center justify-center rounded-[14px] border border-white/50 px-6 py-4 text-base font-semibold text-white hover:bg-white/10">
                    {{ $faqCopy['cta_secondary_label'] }}
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
