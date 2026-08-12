@props([
    'jobs',
    'indexUrl',
])

@if ($jobs->isNotEmpty())
    <section
        class="mt-10 lg:mt-12 w-full rounded-2xl border border-indigo-100/80 bg-gradient-to-br from-indigo-50/90 via-white to-teal-50/40 p-5 sm:p-6 lg:p-7 shadow-sm ring-1 ring-indigo-100/60"
        aria-label="{{ __('talenma.home.latest_jobs_title') }}"
    >
        <div class="mb-5 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-[12px] sm:text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-600">
                    {{ __('talenma.home.latest_jobs_eyebrow') }}
                </p>
                <div class="mt-1.5 flex items-center gap-2.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/25">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <h2 class="truncate text-lg font-bold tracking-tight text-gray-900 sm:text-xl">
                        {{ __('talenma.home.latest_jobs_title') }}
                    </h2>
                </div>
            </div>
            <a
                href="{{ $indexUrl }}"
                class="inline-flex shrink-0 items-center justify-center gap-1.5 self-start rounded-xl border border-indigo-200 bg-white px-3.5 py-2 text-sm font-semibold text-indigo-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-800 sm:text-xs sm:self-auto"
            >
                {{ __('talenma.home.latest_jobs_view_all') }}
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div
            class="group/marquee relative overflow-hidden rounded-2xl border border-white/80 bg-white/90 shadow-sm backdrop-blur-sm"
            x-data="magazineTicker({ inline: true })"
            x-init="init()"
            @resize.window.passive="onResize()"
            @mouseenter="onBannerEnter()"
            @mouseleave="onBannerLeave()"
        >
            <div
                x-ref="marqueeViewport"
                class="magazine-marquee-viewport relative min-h-[7.5rem] w-full overflow-hidden"
                :class="{ 'is-dragging': isDragging }"
                @pointerdown="onPointerDown($event)"
                @pointermove="onPointerMove($event)"
                @pointerup="onPointerUp($event)"
                @pointercancel="onPointerUp($event)"
                @click.capture="onMarqueeClick($event)"
            >
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-14 bg-gradient-to-r from-white via-white/85 to-transparent sm:w-20"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-14 bg-gradient-to-l from-white via-white/85 to-transparent sm:w-20"></div>

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
                            <a
                                href="{{ $job['url'] }}"
                                class="group mx-2 flex shrink-0 items-center gap-4 rounded-xl border border-gray-100 bg-white px-5 py-4 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50/50 hover:shadow-md sm:mx-2.5 sm:gap-5 sm:px-6 sm:py-5"
                            >
                                <div class="flex min-w-[13rem] max-w-xs flex-col justify-center sm:min-w-[17rem] sm:max-w-sm">
                                    <span class="text-[10px] font-medium uppercase tracking-wide text-indigo-500/80 sm:text-[11px]">{{ $job['date'] }}</span>
                                    <span class="mt-1.5 line-clamp-1 text-sm font-bold text-gray-900 transition-colors duration-300 group-hover:text-indigo-700 sm:text-base">
                                        {{ $job['title'] }}
                                    </span>
                                    @if ($job['excerpt'] !== '')
                                        <span class="mt-1 line-clamp-2 text-sm leading-relaxed text-gray-500 sm:text-sm">
                                            {{ $job['excerpt'] }}
                                        </span>
                                    @endif
                                    <span class="mt-2 flex flex-wrap items-center gap-2 text-sm sm:text-sm">
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
    </section>
@endif
