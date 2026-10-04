@props(['companies'])

@if ($companies->isNotEmpty())
<section id="entreprises" class="scroll-mt-28 border-y border-gray-100 bg-gray-50 py-16">
    <div class="home-shell">
    <div class="w-full rounded-2xl border border-indigo-100/80 bg-gradient-to-br from-indigo-50/90 via-white to-teal-50/40 p-5 sm:p-6 lg:p-7 shadow-sm ring-1 ring-indigo-100/60">
        <div class="mb-5 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-[12px] sm:text-[11px] font-semibold uppercase tracking-[0.18em] text-indigo-600">
                    {{ __('talenma.home.companies_marquee_eyebrow') }}
                </p>
                <div class="mt-1.5 flex items-center gap-2.5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/25">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
                        </svg>
                    </span>
                    <h2 class="truncate text-lg font-bold tracking-tight text-gray-900 sm:text-xl">
                        {{ __('talenma.home.companies_marquee_title') }}
                    </h2>
                </div>
            </div>
            <a
                href="{{ route('companies.public.index') }}"
                class="inline-flex shrink-0 items-center justify-center gap-1.5 self-start rounded-xl border border-indigo-200 bg-white px-3.5 py-2 text-sm font-semibold text-indigo-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-800 sm:text-xs sm:self-auto"
                data-companies-view-all
            >
                {{ __('talenma.home.companies_marquee_view_all') }}
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div
            class="group/marquee relative overflow-hidden rounded-2xl border border-white/80 bg-white/90 shadow-sm backdrop-blur-sm"
            x-data="magazineTicker({ inline: true })"
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
                            <{{ ! empty($company['url']) ? 'a' : 'div' }}
                                @if (! empty($company['url'])) href="{{ $company['url'] }}" data-company-marquee-link @endif
                                class="group mx-2 flex shrink-0 items-center gap-3 rounded-xl border border-gray-100 bg-white px-5 py-3 shadow-sm transition duration-300 sm:mx-2.5 sm:gap-4 sm:px-6 [@media(hover:hover)_and_(pointer:fine)]:hover:-translate-y-0.5 [@media(hover:hover)_and_(pointer:fine)]:hover:border-indigo-200 [@media(hover:hover)_and_(pointer:fine)]:hover:bg-indigo-50/50 [@media(hover:hover)_and_(pointer:fine)]:hover:shadow-md"
                            >
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white sm:h-14 sm:w-14">
                                    @if ($company['logo_url'])
                                        <img src="{{ $company['logo_url'] }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <span aria-hidden="true">{{ $company['initials'] }}</span>
                                    @endif
                                </div>
                                <div class="flex min-w-[10rem] max-w-xs flex-col justify-center sm:min-w-[14rem] sm:max-w-sm">
                                    <span class="line-clamp-1 text-sm font-bold text-gray-900 transition-colors duration-300 sm:text-base [@media(hover:hover)_and_(pointer:fine)]:group-hover:text-indigo-700">
                                        {{ $company['name'] }}
                                    </span>
                                    @if ($company['sector'] || $company['country'])
                                        <span class="mt-0.5 line-clamp-1 text-xs text-gray-500 sm:text-sm">
                                            {{ collect([$company['sector'], $company['country']])->filter()->implode(' · ') }}
                                        </span>
                                    @endif
                                </div>
                            </{{ ! empty($company['url']) ? 'a' : 'div' }}>
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
    </div>
</section>
@endif
