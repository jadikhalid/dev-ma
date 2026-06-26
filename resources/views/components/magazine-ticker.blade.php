@php
    use App\Models\MagazineBannerItem;

    $bannerItems = MagazineBannerItem::forBanner()->sortByDesc('created_at')->values();
@endphp

@if ($bannerItems->isNotEmpty())
<div
    x-data="magazineTicker"
    x-init="init()"
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
        <div class="flex items-center justify-center gap-3 sm:gap-4 py-1.5 border-b border-gray-100/90 px-4">
            <span class="h-px w-10 sm:w-16 bg-gradient-to-r from-transparent to-gray-200/90"></span>
            <a href="{{ route('magazine.index') }}"
               class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 hover:text-indigo-500 transition-colors duration-300 ease-in-out">
                {{ __('talenma.home.magazine_ticker_label') }}
            </a>
            <span class="h-px w-10 sm:w-16 bg-gradient-to-l from-transparent to-gray-200/90"></span>
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
                :aria-label="@js(__('talenma.home.magazine_ticker_scroll_next'))"
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
                :aria-label="@js(__('talenma.home.magazine_ticker_scroll_prev'))"
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
                <div
                    x-ref="marqueeSetA"
                    class="magazine-marquee-set flex shrink-0 items-center"
                    data-initial-count="{{ $bannerItems->count() }}"
                >
                    @foreach ($bannerItems as $item)
                        <a href="{{ $item->url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group flex items-center gap-3 shrink-0 px-6 sm:px-8 border-r border-gray-100 hover:bg-indigo-50/40 transition-colors duration-300">
                            <div class="flex flex-col justify-center min-w-[12rem] sm:min-w-[16rem] max-w-xs sm:max-w-sm">
                                <span class="text-[10px] sm:text-[11px] text-gray-400 tracking-wide">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                                <span class="mt-0.5 text-sm sm:text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-1">
                                    {{ $item->title }}
                                </span>
                                <span class="mt-0.5 text-xs sm:text-sm text-gray-500 line-clamp-1">
                                    {{ $item->subtitle }}
                                </span>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg shrink-0 overflow-hidden ring-1 ring-gray-200/80 shadow-sm bg-gradient-to-br from-indigo-400 to-indigo-600">
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
