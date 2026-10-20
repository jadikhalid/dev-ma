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
            <template x-for="(item, index) in items" :key="item.name">
                <a
                    :href="item.url"
                    class="group flex shrink-0 flex-col overflow-hidden rounded-[20px] border border-[#E8E9F0] bg-white text-slate-950 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]"
                    :style="cardStyle()"
                >
                    <div class="relative h-[150px] overflow-hidden">
                        <template x-if="item.banner_url">
                            <img :src="item.banner_url" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        </template>
                        <template x-if="!item.banner_url && index % 3 === 0">
                            <svg class="h-full w-full" viewBox="0 0 360 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                <rect width="360" height="160" fill="#1e1b4b"/>
                                <rect x="28" y="28" width="150" height="104" rx="8" fill="#312e81"/>
                                <text x="48" y="78" fill="#ffffff" font-size="28" font-weight="800" font-family="system-ui,sans-serif">Hello</text>
                                <circle cx="250" cy="78" r="36" fill="#c4b5fd"/>
                                <rect x="214" y="118" width="72" height="42" rx="20" fill="#7c3aed"/>
                            </svg>
                        </template>
                        <template x-if="!item.banner_url && index % 3 === 1">
                            <svg class="h-full w-full" viewBox="0 0 360 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                <rect width="360" height="160" fill="#dbe7f5"/>
                                <rect x="20" y="70" width="70" height="90" fill="#94a3b8"/>
                                <rect x="100" y="40" width="90" height="120" fill="#64748b"/>
                                <rect x="200" y="55" width="60" height="105" fill="#475569"/>
                                <rect x="270" y="30" width="70" height="130" fill="#334155"/>
                                <rect x="40" y="90" width="40" height="70" fill="#cbd5e1"/>
                            </svg>
                        </template>
                        <template x-if="!item.banner_url && index % 3 === 2">
                            <svg class="h-full w-full" viewBox="0 0 360 160" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                                <rect width="360" height="160" fill="#e0f2fe"/>
                                <path d="M0 120 C80 90 140 150 220 100 C280 70 320 90 360 60 L360 160 L0 160 Z" fill="#bae6fd"/>
                                <rect x="70" y="78" width="90" height="28" rx="8" fill="#dc2626" transform="rotate(-18 115 92)"/>
                                <rect x="190" y="62" width="90" height="28" rx="8" fill="#2563eb" transform="rotate(12 235 76)"/>
                            </svg>
                        </template>
                        <span class="absolute bottom-0 left-5 flex h-10 w-24 items-center justify-center rounded-t-[10px] bg-white">
                            <img x-show="item.logo_url" :src="item.logo_url" alt="" class="max-h-[30px] max-w-[4.5rem] object-contain">
                            <span x-show="!item.logo_url" class="text-sm font-extrabold text-indigo-700" x-text="item.initials"></span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-5 pb-5 pt-[18px]">
                        <span class="min-w-0">
                            <span class="block truncate text-[19px] font-semibold leading-tight" x-text="item.name"></span>
                            <span class="mt-1.5 block text-sm text-[#6B7280]">{{ __('talenma.home.recruiting_companies_cta') }}</span>
                        </span>
                        <span class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-full bg-[#0D0B2B] text-white" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
