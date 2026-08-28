@php
    use App\Models\SocialFeedItem;

    $newsItems = SocialFeedItem::forNewsTicker()->sortByDesc('created_at')->values();
    $latestNewsDay = $newsItems->first()?->created_at?->startOfDay();
@endphp

@if ($newsItems->isNotEmpty())
<div
    class="hidden sm:block"
    x-data="magazineTicker"
    @scroll.window.passive="update()"
    @resize.window.passive="onResize()"
>
    <div class="h-[8.25rem]" aria-hidden="true"></div>

    <section
        x-ref="banner"
        x-bind:style="bannerStyle()"
        x-bind:class="{ 'invisible': opacity < 0.01 }"
        class="group/marquee fixed top-16 inset-x-0 z-40 w-full pt-4 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm will-change-[opacity,transform]"
        @mouseenter="onBannerEnter()"
        @mouseleave="onBannerLeave()"
    >
        <div class="flex items-center justify-center gap-3 sm:gap-4 py-1 border-b border-gray-100/90 px-4">
            <span class="h-px w-8 sm:w-12 bg-gradient-to-r from-transparent to-amber-200/70"></span>
            <p class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                <span class="font-bold">{{ __('talenma.home.news_ticker_trends_prefix') }}</span>
                <svg class="h-3.5 w-3.5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>
                </svg>
            </p>
            <span class="h-px w-8 sm:w-12 bg-gradient-to-l from-transparent to-amber-200/70"></span>
        </div>

        <div
            x-ref="marqueeViewport"
            class="magazine-marquee-viewport relative min-h-[5.5rem] w-full overflow-hidden"
            :class="{ 'is-dragging': isDragging }"
            @pointerdown="onPointerDown($event)"
            @pointermove="onPointerMove($event)"
            @pointerup="onPointerUp($event)"
            @pointercancel="onPointerUp($event)"
            @click.capture="onMarqueeClick($event)"
        >
            <div class="absolute inset-y-0 left-0 w-12 sm:w-16 bg-gradient-to-r from-white via-white/80 to-transparent z-10 pointer-events-none"></div>
            <div class="absolute inset-y-0 right-0 w-12 sm:w-16 bg-gradient-to-l from-white via-white/80 to-transparent z-10 pointer-events-none"></div>

            <button
                type="button"
                class="magazine-marquee-nav magazine-marquee-nav--left opacity-0 pointer-events-none group-hover/marquee:opacity-100 group-hover/marquee:pointer-events-auto hidden lg:flex"
                :class="{ 'magazine-marquee-nav--active': arrowHoldDirection === -1 }"
                @pointerdown.prevent.stop="onArrowPointerDown(-1, $event)"
                @pointerup.stop="stopArrowScroll()"
                @pointercancel.stop="stopArrowScroll()"
                :aria-label="@js(__('talenma.home.news_ticker_scroll_next'))"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                </svg>
            </button>

            <button
                type="button"
                class="magazine-marquee-nav magazine-marquee-nav--right opacity-0 pointer-events-none group-hover/marquee:opacity-100 group-hover/marquee:pointer-events-auto hidden lg:flex"
                :class="{ 'magazine-marquee-nav--active': arrowHoldDirection === 1 }"
                @pointerdown.prevent.stop="onArrowPointerDown(1, $event)"
                @pointerup.stop="stopArrowScroll()"
                @pointercancel.stop="stopArrowScroll()"
                :aria-label="@js(__('talenma.home.news_ticker_scroll_prev'))"
            >
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                </svg>
            </button>

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
                           target="_blank"
                           rel="noopener noreferrer"
                           @class([
                               'group flex items-center gap-3 shrink-0 px-6 sm:px-8 border-r transition-colors duration-300',
                               'news-ticker-item--lead border-amber-200/80 bg-amber-50/50 hover:bg-amber-50/80' => $isLeadNews,
                               'news-ticker-item--recent border-indigo-100/80 hover:bg-indigo-50/30' => $isLatestDay && ! $isLeadNews,
                               'news-ticker-item--archive border-gray-100 hover:bg-gray-50/60 opacity-75 hover:opacity-100' => ! $isLatestDay,
                           ])>
                            <div class="flex flex-col justify-center min-w-[12rem] sm:min-w-[16rem] max-w-xs sm:max-w-sm">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span @class([
                                        'text-[10px] sm:text-[11px] tracking-wide',
                                        'font-semibold text-amber-700' => $isLeadNews,
                                        'font-medium text-indigo-600' => $isLatestDay && ! $isLeadNews,
                                        'text-gray-400' => ! $isLatestDay,
                                    ])>{{ $item->created_at->translatedFormat('d M Y') }}</span>
                                    @if ($isLeadNews)
                                        <span class="news-ticker-badge news-ticker-badge--lead">{{ __('talenma.home.news_ticker_new_badge') }}</span>
                                    @elseif ($isLatestDay && $item->created_at->isToday())
                                        <span class="news-ticker-badge news-ticker-badge--recent">{{ __('talenma.home.news_ticker_today_badge') }}</span>
                                    @endif
                                </div>
                                <span @class([
                                    'mt-0.5 text-sm sm:text-base font-semibold transition-colors duration-300 line-clamp-1',
                                    'text-gray-950 group-hover:text-amber-800' => $isLeadNews,
                                    'text-gray-900 group-hover:text-indigo-600' => $isLatestDay && ! $isLeadNews,
                                    'text-gray-600 group-hover:text-gray-800' => ! $isLatestDay,
                                ])>
                                    {{ $item->title }}
                                </span>
                                <span @class([
                                    'mt-0.5 text-xs sm:text-sm line-clamp-1',
                                    'text-gray-600' => $isLatestDay,
                                    'text-gray-400' => ! $isLatestDay,
                                ])>
                                    {{ $item->subtitle }}
                                </span>
                            </div>
                            <div @class([
                                'w-12 h-12 sm:w-14 sm:h-14 rounded-lg shrink-0 overflow-hidden shadow-sm bg-gradient-to-br from-indigo-400 to-indigo-600',
                                'grayscale-[35%]' => ! $isLatestDay,
                            ])>
                                @if ($item->thumbnailUrl())
                                    <img src="{{ $item->thumbnailUrl() }}" alt="" class="w-full h-full object-cover" loading="eager" decoding="async">
                                @endif
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
    </section>
</div>
@endif
