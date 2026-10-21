@props(['companies'])

@if ($companies->isNotEmpty())
<section class="mt-16 lg:mt-[4.5rem]" aria-labelledby="home-recruiting-companies-title" x-data="socialPostsSlider({
    items: @js($companies->values()),
    prevLabel: @js(__('talenma.home.recruiting_companies_prev')),
    nextLabel: @js(__('talenma.home.recruiting_companies_next')),
    gapPx: 24,
})" x-init="init()">
    <div class="flex items-end justify-between gap-6">
        <h2 id="home-recruiting-companies-title" class="text-[2rem] leading-[1.1] tracking-[-0.03em] text-slate-950 sm:text-[2.75rem]">
            <span class="block font-light">{{ __('talenma.home.recruiting_companies_title') }}</span>
            <span class="block font-extrabold">{{ __('talenma.home.recruiting_companies_accent') }}</span>
        </h2>
        <div class="flex shrink-0 items-center gap-2.5">
            <button
                type="button"
                class="flex h-12 w-12 items-center justify-center rounded-full border border-[#E5E7EB] bg-white text-slate-950 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-35"
                @click="prev()"
                :disabled="!canPrev"
                :aria-label="prevLabel"
            >
                <svg class="h-[18px] w-[18px] -scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
            <button
                type="button"
                class="flex h-12 w-12 items-center justify-center rounded-full border-0 bg-[#0D0B2B] text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-35"
                @click="next()"
                :disabled="!canNext"
                :aria-label="nextLabel"
            >
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-ref="viewport" class="mt-8 overflow-hidden">
        <div x-ref="track" class="social-posts-slider-track flex gap-6" :style="trackStyle()">
            <template x-for="item in items" :key="item.name">
                <a
                    :href="item.url"
                    class="relative flex shrink-0 flex-col overflow-hidden rounded-[20px] border border-[#E8E9F0] bg-white text-slate-950 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)] transition-transform duration-300 ease-out hover:z-10 hover:scale-[1.04]"
                    :style="cardStyle()"
                >
                    <div class="relative h-[150px] overflow-hidden">
                        <img :src="item.banner_url" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <span class="absolute left-5 top-[90px] z-10 flex h-[7.5rem] w-[7.5rem] items-center justify-center rounded-2xl border-4 border-white bg-white p-[7px] shadow-sm">
                        <img x-show="item.logo_url" :src="item.logo_url" alt="" class="max-h-full max-w-full object-contain">
                        <span x-show="!item.logo_url" class="text-2xl font-extrabold text-indigo-700" x-text="item.initials"></span>
                    </span>
                    <div class="flex items-center justify-between gap-3 px-5 pb-5 pt-[4.75rem]">
                        <span class="min-w-0 truncate text-[19px] font-semibold leading-tight" x-text="item.name"></span>
                        <span class="inline-flex shrink-0 items-center gap-1.5 text-sm font-medium text-slate-500">
                            {{ __('talenma.home.recruiting_companies_cta') }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6"/>
                            </svg>
                        </span>
                    </div>
                </a>
            </template>
        </div>
    </div>
</section>
@endif
