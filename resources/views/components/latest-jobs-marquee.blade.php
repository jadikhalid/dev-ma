@props([
    'jobs',
    'indexUrl',
])

@if ($jobs->isNotEmpty())
    <div class="mt-4 w-full">
        <div class="mb-2 flex items-center justify-between gap-3 px-0.5">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="h-4 w-4 shrink-0 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="truncate text-sm font-semibold text-gray-800">{{ __('talenma.home.latest_jobs_title') }}</span>
            </div>
            <a
                href="{{ $indexUrl }}"
                class="shrink-0 text-xs font-semibold text-indigo-600 transition hover:text-indigo-700"
            >
                {{ __('talenma.home.latest_jobs_view_all') }}
            </a>
        </div>

        <div
            class="group/marquee relative overflow-hidden rounded-xl border border-gray-100 bg-white"
            x-data="magazineTicker({ inline: true })"
            x-init="init()"
            @resize.window.passive="onResize()"
            @mouseenter="onBannerEnter()"
            @mouseleave="onBannerLeave()"
        >
            <div
                x-ref="marqueeViewport"
                class="magazine-marquee-viewport relative min-h-[6.25rem] w-full overflow-hidden"
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
                    :aria-label="@js(__('talenma.home.latest_jobs_scroll_next'))"
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
                    :aria-label="@js(__('talenma.home.latest_jobs_scroll_prev'))"
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
                        data-initial-count="{{ $jobs->count() }}"
                    >
                        @foreach ($jobs as $job)
                            <a
                                href="{{ $job['url'] }}"
                                class="group flex shrink-0 items-center gap-3 border-r border-gray-100 px-6 transition-colors duration-300 hover:bg-indigo-50/40 sm:gap-4 sm:px-8"
                            >
                                <div class="flex min-w-[12rem] max-w-xs flex-col justify-center sm:min-w-[16rem] sm:max-w-sm">
                                    <span class="text-[10px] tracking-wide text-gray-400 sm:text-[11px]">{{ $job['date'] }}</span>
                                    <span class="mt-0.5 line-clamp-1 text-sm font-semibold text-gray-900 transition-colors duration-300 group-hover:text-indigo-600 sm:text-base">
                                        {{ $job['title'] }}
                                    </span>
                                    @if ($job['excerpt'] !== '')
                                        <span class="mt-0.5 line-clamp-1 text-xs text-gray-500 sm:text-sm">
                                            {{ $job['excerpt'] }}
                                        </span>
                                    @endif
                                    <span class="mt-0.5 line-clamp-1 text-xs font-medium text-gray-600 sm:text-sm">
                                        {{ $job['company'] }}
                                    </span>
                                </div>
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-sm ring-1 ring-gray-200/80 sm:h-14 sm:w-14">
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
                        class="magazine-marquee-set flex shrink-0 items-center"
                        aria-hidden="true"
                    ></div>
                </div>
            </div>
        </div>
    </div>
@endif
