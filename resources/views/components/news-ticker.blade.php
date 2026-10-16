@php
    use App\Models\SocialFeedItem;

    $newsItems = SocialFeedItem::forNewsTicker()->sortByDesc('created_at')->values();
    $latestNewsDay = $newsItems->first()?->created_at?->startOfDay();
@endphp

@if ($newsItems->isNotEmpty())
<div
    class="hidden sm:block"
    x-data="magazineTicker({ manual: true, dots: {{ $newsItems->count() }} })"
    @scroll.window.passive="update()"
    @resize.window.passive="onResize()"
>
    <div class="h-[13.9375rem]" aria-hidden="true"></div>

    <section
        x-ref="banner"
        x-bind:style="bannerStyle()"
        x-bind:class="{ 'invisible': opacity < 0.01 }"
        class="group/marquee fixed top-16 inset-x-0 z-40 w-full pt-[2.1875rem] bg-white will-change-[opacity,transform]"
        @mouseenter="onBannerEnter()"
        @mouseleave="onBannerLeave()"
    >
        <div class="home-align-wide mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div>
            <div class="flex items-center justify-center gap-3 sm:gap-4 py-1 px-4 2xl:px-0">
                <span class="h-px w-10 sm:w-14 bg-gradient-to-r from-transparent to-amber-400/90"></span>
                <p class="inline-flex items-center text-sm font-bold tracking-[0.04em] text-slate-900">
                    {{ __('talenma.home.news_ticker_trends_prefix') }}
                </p>
                <span class="h-px w-10 sm:w-14 bg-gradient-to-l from-transparent to-amber-400/90"></span>
            </div>

            <div class="relative h-[8.875rem]">
            <div class="absolute inset-y-0 left-0 w-12 sm:w-16 bg-gradient-to-r from-white via-white/80 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-12 sm:w-16 bg-gradient-to-l from-white via-white/80 to-transparent z-10 pointer-events-none"></div>

            <button
                type="button"
                class="magazine-marquee-nav magazine-marquee-nav--left"
                @click.stop="stepDot(-1)"
                :aria-label="@js(__('talenma.home.news_ticker_scroll_prev'))"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                </svg>
            </button>

            <button
                type="button"
                class="magazine-marquee-nav magazine-marquee-nav--right"
                @click.stop="stepDot(1)"
                :aria-label="@js(__('talenma.home.news_ticker_scroll_next'))"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                </svg>
            </button>

            <div
                x-ref="marqueeViewport"
                class="magazine-marquee-viewport mx-20 h-full overflow-hidden"
                :class="{ 'is-dragging': isDragging }"
                @pointerdown="onPointerDown($event)"
                @pointermove="onPointerMove($event)"
                @pointerup="onPointerUp($event)"
                @pointercancel="onPointerUp($event)"
                @click.capture="onMarqueeClick($event)"
            >
            <div
                x-ref="marqueeTrack"
                class="magazine-marquee-track flex w-max items-center py-3 select-none"
                :style="marqueeTrackStyle()"
            >
                <div x-ref="marqueeLeadSpacer" class="shrink-0" aria-hidden="true"></div>
                <div
                    x-ref="marqueeSetA"
                    class="magazine-marquee-set flex shrink-0 items-center"
                    data-initial-count="{{ $newsItems->count() }}"
                >
                    @foreach ($newsItems as $item)
                        @php
                            $isLatestDay = $latestNewsDay && $item->created_at->isSameDay($latestNewsDay);
                            $isLeadNews = $loop->first;
                        @endphp
                        <a href="{{ $item->url }}"
                           @if (! $item->isInternalBlogPost())
                               target="_blank"
                               rel="noopener noreferrer"
                           @endif
                           class="group flex h-full shrink-0 items-center px-2.5">
                            <div @class([
                                'flex h-[7.375rem] w-[32rem] items-center gap-3.5 overflow-hidden rounded-2xl border px-4 shadow-sm transition duration-300',
                                'border-amber-200/90 bg-amber-50/80 hover:bg-amber-50' => $isLeadNews,
                                'border-indigo-100 bg-white hover:border-indigo-200 hover:bg-indigo-50/50' => $isLatestDay && ! $isLeadNews,
                                'border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50' => ! $isLatestDay,
                            ])>
                                <div class="h-[5.625rem] w-[5.625rem] shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm">
                                    @if ($item->thumbnailUrl())
                                        <img src="{{ $item->thumbnailUrl() }}" alt="" class="h-full w-full object-cover" loading="eager" decoding="async">
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex h-5 items-center gap-1.5 overflow-hidden">
                                        <span @class([
                                            'truncate text-xs tracking-wide',
                                            'font-semibold text-amber-700' => $isLeadNews,
                                            'font-medium text-indigo-600' => $isLatestDay && ! $isLeadNews,
                                            'text-slate-500' => ! $isLatestDay,
                                        ])>{{ $item->created_at->translatedFormat('d M Y') }}</span>
                                        @if ($isLeadNews)
                                            <span class="news-ticker-badge news-ticker-badge--lead shrink-0">{{ __('talenma.home.news_ticker_new_badge') }}</span>
                                        @elseif ($isLatestDay && $item->created_at->isToday())
                                            <span class="news-ticker-badge news-ticker-badge--recent shrink-0">{{ __('talenma.home.news_ticker_today_badge') }}</span>
                                        @endif
                                    </div>
                                    <span @class([
                                        'mt-1 block truncate text-base font-semibold leading-snug transition-colors duration-300',
                                        'text-gray-950 group-hover:text-amber-800' => $isLeadNews,
                                        'text-gray-900 group-hover:text-indigo-700' => ! $isLeadNews,
                                    ])>
                                        {{ $item->title }}
                                    </span>
                                    <span @class([
                                        'news-ticker-subtitle mt-1 text-sm leading-snug',
                                        'text-gray-600' => $isLatestDay,
                                        'text-slate-500' => ! $isLatestDay,
                                    ])>
                                        {{ $item->subtitle }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div
                    x-ref="marqueeSetB"
                    class="magazine-marquee-set flex shrink-0 items-center"
                    aria-hidden="true"
                ></div>
            </div>
        </div>
            </div>
            <div class="flex items-center justify-center gap-1.5 py-2" role="tablist" aria-label="{{ __('talenma.home.news_ticker_trends_prefix') }}">
                <template x-for="index in dotCount" :key="index">
                    <button
                        type="button"
                        role="tab"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :class="activeDot === index - 1 ? 'w-6 bg-slate-800' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"
                        :aria-selected="activeDot === index - 1"
                        :aria-label="@js(__('talenma.home.news_ticker_dot')).replace(':n', index)"
                        @click="goToDot(index - 1)"
                    ></button>
                </template>
            </div>
            </div>
        </div>
    </section>
</div>
@endif
