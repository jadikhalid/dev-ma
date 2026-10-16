@props(['companies'])

@php
    $companyCards = $companies->values()->map(fn (array $company) => [
        'url' => $company['url'] ?? null,
        'name' => $company['name'],
        'sector' => $company['sector'] ?? '',
        'excerpt' => $company['excerpt'] ?? '',
        'logo_url' => $company['logo_url'] ?? null,
        'initials' => $company['initials'] ?? '',
    ])->values();
@endphp

@if ($companies->isNotEmpty())
<section id="entreprises" class="scroll-mt-28 bg-white py-16 lg:py-20" aria-labelledby="home-companies-title">
    <div class="home-shell">
        <div class="text-center">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
                {{ __('talenma.home.companies_marquee_lead') }}
            </p>
            <h2 id="home-companies-title" class="mx-auto mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.7rem] lg:leading-[1.15]">
                {{ __('talenma.home.companies_marquee_accent') }}
            </h2>
        </div>

        <div class="mt-10 w-full rounded-2xl border border-indigo-100/80 bg-gradient-to-br from-indigo-50/90 via-white to-teal-50/40 p-5 shadow-sm ring-1 ring-indigo-100/60 sm:p-6 lg:p-7">
            <div
                class="relative sm:hidden"
                x-data="socialPostsSlider({
                    items: @js($companyCards),
                    prevLabel: @js(__('talenma.home.companies_marquee_scroll_prev')),
                    nextLabel: @js(__('talenma.home.companies_marquee_scroll_next')),
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
                    <div x-ref="track" class="social-posts-slider-track flex gap-5" :style="trackStyle()">
                        <template x-for="(item, index) in items" :key="index">
                            <a
                                :href="item.url || undefined"
                                class="group flex shrink-0 items-start gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                                :style="cardStyle()"
                            >
                                <div class="min-w-0 flex-1">
                                    <h3 class="line-clamp-1 text-base font-bold text-gray-900" x-text="item.name"></h3>
                                    <p class="mt-2" x-show="item.sector">
                                        <span class="inline-flex max-w-full items-center truncate rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 ring-1 ring-indigo-100" x-text="item.sector"></span>
                                    </p>
                                    <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-gray-500" x-show="item.excerpt" x-text="item.excerpt"></p>
                                </div>
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 text-sm font-bold text-white shadow-md shadow-indigo-600/20 ring-2 ring-white">
                                    <template x-if="item.logo_url">
                                        <img :src="item.logo_url" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async" @load="measure()">
                                    </template>
                                    <span x-show="!item.logo_url" aria-hidden="true" x-text="item.initials"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-center gap-1.5" role="tablist" aria-label="{{ __('talenma.home.companies_marquee_accent') }}">
                    <template x-for="(item, index) in items" :key="'company-dot-' + index">
                        <button
                            type="button"
                            role="tab"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="startIndex === index ? 'w-6 bg-slate-800' : 'w-1.5 bg-slate-300'"
                            :aria-selected="startIndex === index"
                            :aria-label="@js(__('talenma.home.companies_marquee_dot')).replace(':n', index + 1)"
                            @click="goTo(index)"
                        ></button>
                    </template>
                </div>
            </div>

            <div
                class="relative hidden overflow-hidden rounded-2xl border border-white/80 bg-white/90 pb-2 shadow-sm backdrop-blur-sm sm:block"
                x-data="magazineTicker({ inline: true, manual: true, dots: {{ $companies->count() }} })"
                @resize.window.passive="onResize()"
            >
                <div class="relative">
                    <button
                        type="button"
                        class="magazine-marquee-nav magazine-marquee-nav--left"
                        @click.stop="stepDot(-1)"
                        :aria-label="@js(__('talenma.home.companies_marquee_scroll_prev'))"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="magazine-marquee-nav magazine-marquee-nav--right"
                        @click.stop="stepDot(1)"
                        :aria-label="@js(__('talenma.home.companies_marquee_scroll_next'))"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-ref="marqueeViewport" class="magazine-marquee-viewport relative mx-14 min-h-[7.5rem] overflow-hidden">
                        <div
                            x-ref="marqueeTrack"
                            class="magazine-marquee-track flex w-max select-none items-stretch py-4 sm:py-5"
                            :style="marqueeTrackStyle()"
                        >
                            <div x-ref="marqueeLeadSpacer" class="shrink-0" aria-hidden="true"></div>
                            <div
                                x-ref="marqueeSetA"
                                class="magazine-marquee-set flex shrink-0 items-stretch"
                                data-initial-count="{{ $companies->count() }}"
                            >
                                @foreach ($companies as $company)
                                    <x-company-recruiting-card :company="$company" />
                                @endforeach
                            </div>
                            <div x-ref="marqueeSetB" class="magazine-marquee-set flex shrink-0 items-stretch" aria-hidden="true"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-1.5 pb-1" role="tablist" aria-label="{{ __('talenma.home.companies_marquee_accent') }}">
                    <template x-for="index in dotCount" :key="index">
                        <button
                            type="button"
                            role="tab"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="activeDot === index - 1 ? 'w-6 bg-slate-800' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"
                            :aria-selected="activeDot === index - 1"
                            :aria-label="@js(__('talenma.home.companies_marquee_dot')).replace(':n', index)"
                            @click="goToDot(index - 1)"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
