@props(['platformMenuItems', 'bandLinks'])

@php
    use App\Support\PortalHost;

    $onOfferPage = request()->routeIs('company.offer');
    $showOfferCtas = ! auth()->user()?->isCompany();
@endphp

<div class="sm:hidden" x-cloak>
    <div
        x-show="mobileNav"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileNav = false"
        class="fixed inset-0 z-[60] bg-gray-950/40 backdrop-blur-[2px]"
        aria-hidden="true"
    ></div>

    <aside
        id="company-mobile-nav"
        x-show="mobileNav"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[70] flex h-[100dvh] w-[85vw] max-w-sm flex-col bg-white shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('talenma.nav.mobile_menu_title') }}"
        data-company-mobile-nav
    >
        <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-100 px-5">
            <p class="text-base font-bold text-gray-950">{{ __('talenma.nav.mobile_menu_title') }}</p>
            <button
                type="button"
                @click="mobileNav = false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900"
                aria-label="{{ __('talenma.nav.mobile_menu_close') }}"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-4 py-4">
            <a
                href="{{ route('company.offer') }}"
                @class([
                    'mb-2 flex items-center gap-3 rounded-xl border px-4 py-3.5 text-[0.9375rem] font-semibold transition-colors duration-300',
                    'border-indigo-600 bg-indigo-600 text-white' => $onOfferPage,
                    'border-gray-200 text-gray-950 hover:border-indigo-300 hover:bg-indigo-50' => ! $onOfferPage,
                ])
                @if ($onOfferPage) aria-current="page" @endif
                data-company-mobile-home
            >
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                {{ __('talenma.nav.home') }}
            </a>

            <div class="rounded-xl border border-gray-200">
                <button
                    type="button"
                    @click="mobilePlatform = ! mobilePlatform"
                    :aria-expanded="mobilePlatform.toString()"
                    aria-controls="company-mobile-platform"
                    class="flex w-full items-center justify-between rounded-xl px-4 py-3.5 text-left text-[0.9375rem] font-semibold text-gray-950"
                    data-company-mobile-platform-toggle
                >
                    {{ __('talenma.nav.our_platform') }}
                    <svg class="h-5 w-5 shrink-0 text-indigo-600 transition-transform duration-300 ease-in-out" :class="mobilePlatform ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div id="company-mobile-platform" x-show="mobilePlatform" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="px-3 pb-3">
                    <ul class="space-y-1.5" role="list">
                        @foreach ($platformMenuItems as $index => $item)
                            <li>
                                <a
                                    href="{{ route('company.offer') }}#{{ $item['anchor'] }}"
                                    @click="mobileNav = false"
                                    class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2.5 text-sm font-medium text-gray-800 transition hover:bg-indigo-50 hover:text-indigo-700"
                                    data-company-mobile-platform-link="{{ $item['anchor'] }}"
                                >
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                                    </span>
                                    <span>{{ __('talenma.company_offer.includes_'.($index + 1)) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ PortalHost::wwwRootUrl() }}" class="mt-3 inline-flex px-1 text-sm font-medium text-indigo-700 underline underline-offset-2">{{ __('talenma.nav.platform_menu_footer_link') }} &rarr;</a>
                </div>
            </div>

            <ul class="mt-2 space-y-2" role="list">
                @foreach ($bandLinks as $link)
                    <li>
                        <a
                            href="{{ route($link['route']) }}"
                            @class([
                                'flex items-center justify-between rounded-xl border px-4 py-3.5 text-[0.9375rem] font-semibold transition-colors duration-300',
                                'border-indigo-600 bg-indigo-600 text-white' => $link['active'],
                                'border-gray-200 text-gray-950 hover:border-indigo-300 hover:bg-indigo-50' => ! $link['active'],
                            ])
                            @if ($link['active']) aria-current="page" @endif
                        >
                            {{ $link['label'] }}
                            <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 flex items-center justify-between px-1">
                <span class="text-sm font-medium text-gray-600">{{ __('talenma.nav.mobile_menu_language') }}</span>
                <x-locale-switcher />
            </div>
        </nav>

        @if ($showOfferCtas)
            <div class="grid shrink-0 gap-2.5 border-t border-gray-100 p-4">
                <a
                    href="{{ route('company.offer', ['tab' => 'demo']) }}"
                    @if ($onOfferPage) @click.prevent="mobileNav = false; setTimeout(() => $dispatch('company-offer-drawer', 'demo'), 60)" @endif
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 bg-gray-950 px-6 py-3 text-sm font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800"
                    data-company-mobile-demo
                >{{ __('talenma.nav.request_demo') }}</a>
                <a
                    href="{{ route('company.offer', ['tab' => 'trial']) }}"
                    @if ($onOfferPage) @click.prevent="mobileNav = false; setTimeout(() => $dispatch('company-offer-drawer', 'trial'), 60)" @endif
                    class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 px-6 py-3 text-sm font-semibold text-gray-950 transition hover:bg-gray-950 hover:text-white"
                    data-company-mobile-trial
                >{{ __('talenma.company_offer.hero_cta_trial') }}</a>
            </div>
        @endif
    </aside>
</div>
