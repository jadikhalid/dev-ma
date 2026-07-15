@props(['companies'])

@if ($companies->isNotEmpty())
<section id="entreprises" class="scroll-mt-28 border-y border-gray-100 bg-gray-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">{{ __('talenma.home.companies_marquee_eyebrow') }}</p>
            <h2 class="mt-3 text-3xl font-bold text-gray-900">{{ __('talenma.home.companies_marquee_title') }}</h2>
            <p class="mt-3 text-gray-600">{{ __('talenma.home.companies_marquee_subtitle') }}</p>
        </div>

        <div
            class="group/marquee relative mt-10 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm"
            x-data="magazineTicker({ inline: true })"
            x-init="init()"
            @resize.window.passive="onResize()"
            @mouseenter="onBannerEnter()"
            @mouseleave="onBannerLeave()"
        >
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
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-12 bg-gradient-to-r from-white via-white/80 to-transparent sm:w-16"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-12 bg-gradient-to-l from-white via-white/80 to-transparent sm:w-16"></div>

                <button
                    type="button"
                    class="magazine-marquee-nav magazine-marquee-nav--left pointer-events-none hidden opacity-0 group-hover/marquee:pointer-events-auto group-hover/marquee:opacity-100 lg:flex"
                    :class="{ 'magazine-marquee-nav--active': arrowHoldDirection === -1 }"
                    @pointerdown.prevent.stop="onArrowPointerDown(-1, $event)"
                    @pointerup.stop="stopArrowScroll()"
                    @pointercancel.stop="stopArrowScroll()"
                    :aria-label="@js(__('talenma.home.companies_marquee_scroll_next'))"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <button
                    type="button"
                    class="magazine-marquee-nav magazine-marquee-nav--right pointer-events-none hidden opacity-0 group-hover/marquee:pointer-events-auto group-hover/marquee:opacity-100 lg:flex"
                    :class="{ 'magazine-marquee-nav--active': arrowHoldDirection === 1 }"
                    @pointerdown.prevent.stop="onArrowPointerDown(1, $event)"
                    @pointerup.stop="stopArrowScroll()"
                    @pointercancel.stop="stopArrowScroll()"
                    :aria-label="@js(__('talenma.home.companies_marquee_scroll_prev'))"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-ref="marqueeTrack"
                    class="magazine-marquee-track flex w-max select-none items-center py-3"
                    :style="marqueeTrackStyle()"
                >
                    <div x-ref="marqueeLeadSpacer" class="shrink-0" aria-hidden="true"></div>
                    <div
                        x-ref="marqueeSetA"
                        class="magazine-marquee-set flex shrink-0 items-center"
                        data-initial-count="{{ $companies->count() }}"
                    >
                        @foreach ($companies as $company)
                            <div class="flex shrink-0 items-center gap-3 border-r border-gray-100 px-6 sm:gap-4 sm:px-8">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white ring-1 ring-gray-200/80 shadow-sm sm:h-14 sm:w-14">
                                    @if ($company['logo_url'])
                                        <img src="{{ $company['logo_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <span aria-hidden="true">{{ $company['initials'] }}</span>
                                    @endif
                                </div>
                                <div class="flex min-w-[10rem] max-w-xs flex-col justify-center sm:min-w-[14rem] sm:max-w-sm">
                                    <span class="line-clamp-1 text-sm font-semibold text-gray-900 sm:text-base">
                                        {{ $company['name'] }}
                                    </span>
                                    @if ($company['sector'] || $company['country'])
                                        <span class="mt-0.5 line-clamp-1 text-xs text-gray-500 sm:text-sm">
                                            {{ collect([$company['sector'], $company['country']])->filter()->implode(' · ') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
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
    </div>
</section>
@endif
