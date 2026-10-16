@props([
    'jobs',
    'indexUrl',
])

@php
    $jobCards = $jobs->values()->map(function (array $job, int $index) {
        return [
            'url' => $job['url'],
            'date' => $job['date'],
            'title' => $job['title'],
            'excerpt' => $job['excerpt'] ?? '',
            'company' => $job['company'],
            'sector' => $job['sector'] ?? '',
            'logo_url' => $job['logo_url'] ?? null,
            'company_initials' => $job['company_initials'] ?? '',
            'lead' => $index === 0,
        ];
    })->values();
@endphp

@if ($jobs->isNotEmpty())
    <section class="mt-14 w-full lg:mt-16" aria-labelledby="home-offers-title">
        <div class="text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
                {{ __('talenma.home.latest_jobs_eyebrow') }}
            </p>
            <h2 id="home-offers-title" class="mx-auto mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.7rem] lg:leading-[1.15]">
                {{ __('talenma.home.latest_jobs_title') }}
            </h2>
        </div>

        <div class="mt-10 w-full rounded-2xl border border-indigo-100/80 bg-gradient-to-br from-indigo-50/90 via-white to-teal-50/40 p-5 shadow-sm ring-1 ring-indigo-100/60 sm:p-6 lg:p-7">
        <div class="mb-5 flex justify-end sm:mb-6">
            <a
                href="{{ $indexUrl }}"
                class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-white px-3.5 py-2 text-sm font-semibold text-indigo-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-800 sm:text-xs"
            >
                {{ __('talenma.home.latest_jobs_view_all') }}
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Phone only: manual slider (same interaction as « Nos publications ») — no auto-scroll --}}
        <div
            class="relative sm:hidden"
            x-data="socialPostsSlider({
                items: @js($jobCards),
                prevLabel: @js(__('talenma.home.latest_jobs_scroll_prev')),
                nextLabel: @js(__('talenma.home.latest_jobs_scroll_next')),
            })"
            x-init="init()"
        >
            <button
                type="button"
                class="social-posts-slider-nav social-posts-slider-nav--left"
                @click="prev()"
                :disabled="!canPrev"
                :aria-label="prevLabel"
            >
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                </svg>
            </button>

            <button
                type="button"
                class="social-posts-slider-nav social-posts-slider-nav--right"
                @click="next()"
                :disabled="!canNext"
                :aria-label="nextLabel"
            >
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                </svg>
            </button>

                <div x-ref="viewport" class="social-posts-slider-viewport overflow-hidden px-1">
                <div
                    x-ref="track"
                    class="social-posts-slider-track flex gap-5"
                    :style="trackStyle()"
                >
                    <template x-for="(item, index) in items" :key="index">
                        <a
                            :href="item.url"
                            class="group flex shrink-0 flex-col overflow-hidden rounded-2xl border shadow-sm"
                            :class="item.lead ? 'border-amber-200/90 bg-amber-50/80' : 'border-gray-100 bg-white'"
                            :style="cardStyle()"
                        >
                            <div class="flex items-start gap-4 p-5">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[10px] uppercase tracking-wide" :class="item.lead ? 'font-semibold text-amber-700' : 'font-medium text-indigo-500/80'" x-text="item.date"></p>
                                        <span x-show="item.lead" class="news-ticker-badge news-ticker-badge--lead shrink-0">{{ __('talenma.home.news_ticker_new_badge') }}</span>
                                    </div>
                                    <h3 class="mt-1.5 line-clamp-2 text-base font-bold" :class="item.lead ? 'text-gray-950' : 'text-gray-900'" x-text="item.title"></h3>
                                    <p
                                        class="mt-1 text-sm leading-relaxed text-gray-500 line-clamp-2"
                                        x-show="item.excerpt"
                                        x-text="item.excerpt"
                                    ></p>
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-flex max-w-full items-center truncate rounded-md bg-teal-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white ring-1 ring-teal-800/40"
                                            x-text="item.company"
                                        ></span>
                                        <span
                                            class="inline-flex max-w-full items-center truncate rounded-md bg-amber-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white ring-1 ring-amber-800/40"
                                            x-show="item.sector"
                                            x-text="item.sector"
                                        ></span>
                                    </div>
                                </div>
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white">
                                    <template x-if="item.logo_url">
                                        <img :src="item.logo_url" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" @load="measure()">
                                    </template>
                                    <span x-show="!item.logo_url" aria-hidden="true" x-text="item.company_initials"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap items-center justify-center gap-1.5" role="tablist" aria-label="{{ __('talenma.home.latest_jobs_title') }}">
                <template x-for="(item, index) in items" :key="'dot-' + index">
                    <button
                        type="button"
                        role="tab"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :class="startIndex === index ? 'w-6 bg-slate-800' : 'w-1.5 bg-slate-300'"
                        :aria-selected="startIndex === index"
                        :aria-label="@js(__('talenma.home.latest_jobs_dot')).replace(':n', index + 1)"
                        @click="goTo(index)"
                    ></button>
                </template>
            </div>
        </div>

        {{-- Tablet / desktop: same manual jumps as the news ticker --}}
        <div
            class="relative hidden overflow-hidden rounded-2xl border border-white/80 bg-white/90 pb-2 shadow-sm backdrop-blur-sm sm:block"
            x-data="magazineTicker({ inline: true, manual: true, dots: {{ $jobs->count() }} })"
            @resize.window.passive="onResize()"
        >
            <div class="relative">
                <button
                    type="button"
                    class="magazine-marquee-nav magazine-marquee-nav--left"
                    @click.stop="stepDot(-1)"
                    :aria-label="@js(__('talenma.home.latest_jobs_scroll_prev'))"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <button
                    type="button"
                    class="magazine-marquee-nav magazine-marquee-nav--right"
                    @click.stop="stepDot(1)"
                    :aria-label="@js(__('talenma.home.latest_jobs_scroll_next'))"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-ref="marqueeViewport"
                    class="magazine-marquee-viewport relative mx-14 min-h-[7.5rem] overflow-hidden"
                >
                    <div
                        x-ref="marqueeTrack"
                        class="magazine-marquee-track flex w-max select-none items-stretch py-4 sm:py-5"
                        :style="marqueeTrackStyle()"
                    >
                        <div x-ref="marqueeLeadSpacer" class="shrink-0" aria-hidden="true"></div>
                        <div
                            x-ref="marqueeSetA"
                            class="magazine-marquee-set flex shrink-0 items-stretch"
                            data-initial-count="{{ $jobs->count() }}"
                        >
                            @foreach ($jobs as $job)
                                @php $isLeadJob = $loop->first; @endphp
                                <a
                                    href="{{ $job['url'] }}"
                                    @class([
                                        'group mx-2 flex w-[19.2rem] shrink-0 items-center gap-4 rounded-xl border px-5 py-4 shadow-sm transition duration-300 sm:mx-2.5 md:w-[25.6rem] lg:w-[32rem] xl:w-[38.4rem] sm:gap-5 sm:px-6 sm:py-5 [@media(hover:hover)_and_(pointer:fine)]:hover:-translate-y-0.5 [@media(hover:hover)_and_(pointer:fine)]:hover:shadow-md',
                                        'border-amber-200/90 bg-amber-50/80 [@media(hover:hover)_and_(pointer:fine)]:hover:bg-amber-50' => $isLeadJob,
                                        'border-gray-100 bg-white [@media(hover:hover)_and_(pointer:fine)]:hover:border-indigo-200 [@media(hover:hover)_and_(pointer:fine)]:hover:bg-indigo-50/50' => ! $isLeadJob,
                                    ])
                                >
                                    <div class="flex min-w-0 flex-1 flex-col justify-center">
                                        <span class="flex items-center gap-1.5">
                                            <span @class([
                                                'text-[10px] uppercase tracking-wide sm:text-[11px]',
                                                'font-semibold text-amber-700' => $isLeadJob,
                                                'font-medium text-indigo-500/80' => ! $isLeadJob,
                                            ])>{{ $job['date'] }}</span>
                                            @if ($isLeadJob)
                                                <span class="news-ticker-badge news-ticker-badge--lead shrink-0">{{ __('talenma.home.news_ticker_new_badge') }}</span>
                                            @endif
                                        </span>
                                        <span @class([
                                            'mt-1.5 line-clamp-1 text-sm font-bold transition-colors duration-300 sm:text-base',
                                            'text-gray-950 [@media(hover:hover)_and_(pointer:fine)]:group-hover:text-amber-800' => $isLeadJob,
                                            'text-gray-900 [@media(hover:hover)_and_(pointer:fine)]:group-hover:text-indigo-700' => ! $isLeadJob,
                                        ])>
                                            {{ $job['title'] }}
                                        </span>
                                        @if ($job['excerpt'] !== '')
                                            <span class="mt-1 line-clamp-2 text-sm leading-relaxed text-gray-500">
                                                {{ $job['excerpt'] }}
                                            </span>
                                        @endif
                                        <span class="mt-2 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex max-w-full items-center truncate rounded-md bg-teal-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white ring-1 ring-teal-800/40 sm:text-[11px]">{{ $job['company'] }}</span>
                                            @if (! empty($job['sector']))
                                                <span class="inline-flex max-w-full items-center truncate rounded-md bg-amber-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white ring-1 ring-amber-800/40 sm:text-[11px]">
                                                    {{ $job['sector'] }}
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white sm:h-16 sm:w-16">
                                        @if ($job['logo_url'])
                                            <img src="{{ $job['logo_url'] }}" alt="" class="h-full w-full object-cover" loading="eager" decoding="async">
                                        @else
                                            <span aria-hidden="true">{{ $job['company_initials'] }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div
                            x-ref="marqueeSetB"
                            class="magazine-marquee-set flex shrink-0 items-stretch"
                            aria-hidden="true"
                        ></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-center gap-1.5 pb-1" role="tablist" aria-label="{{ __('talenma.home.latest_jobs_title') }}">
                <template x-for="index in dotCount" :key="index">
                    <button
                        type="button"
                        role="tab"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :class="activeDot === index - 1 ? 'w-6 bg-slate-800' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"
                        :aria-selected="activeDot === index - 1"
                        :aria-label="@js(__('talenma.home.latest_jobs_dot')).replace(':n', index)"
                        @click="goToDot(index - 1)"
                    ></button>
                </template>
            </div>
        </div>
        </div>
    </section>
@endif
