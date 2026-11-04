@props([
    'fullWidth' => false,
])

@php
    $talentHeader = auth()->check() && auth()->user()->isTalent();
    $homeHeader = request()->routeIs('home', 'career.how', 'companies.public.index', 'blog.index');

    $navLinks = [
        [
            'href' => route('career.offers'),
            'label' => __('talenma.nav.offers_link'),
            'active' => request()->routeIs('career.offers'),
        ],
        [
            'href' => route('career.tools'),
            'label' => __('talenma.nav.career_tools'),
            'active' => request()->routeIs('career.tools'),
        ],
        [
            'href' => route('career.how'),
            'label' => __('talenma.nav.how_it_works'),
            'active' => request()->routeIs('career.how'),
        ],
        [
            'href' => route('blog.index'),
            'label' => __('talenma.nav.career_advice'),
            'active' => request()->routeIs('blog.*'),
            'disabled' => $talentHeader && request()->routeIs('blog.index'),
        ],
        [
            'href' => route('company.offer'),
            'label' => __('talenma.nav.hiring'),
            'active' => request()->routeIs('company.offer'),
            'hiring' => true,
            'blank' => true,
        ],
    ];

    if ($talentHeader) {
        $navLinks = array_values(array_filter(
            $navLinks,
            fn (array $link): bool => ($link['href'] ?? null) === route('blog.index'),
        ));
    }

    $navLinkClass = 'inline-flex items-center px-2.5 py-2 text-sm font-medium leading-5 text-slate-700 transition hover:text-indigo-700';
    $navLinkDisabledClass = 'inline-flex cursor-not-allowed items-center px-2.5 py-2 text-sm font-medium leading-5 text-slate-400';
    $navLinkActiveClass = 'inline-flex items-center px-2.5 py-2 text-sm font-semibold leading-5 text-indigo-700';
    $navHiringClass = 'inline-flex items-center rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold leading-5 text-white shadow-sm transition hover:bg-slate-800';
    $menuLinkClass = 'flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-indigo-700 sm:px-3.5 sm:py-3 sm:text-[0.95rem]';
    $menuLinkDisabledClass = 'flex w-full cursor-not-allowed items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400 sm:px-3.5 sm:py-3 sm:text-[0.95rem]';
    $menuLinkActiveClass = 'flex w-full items-center gap-2.5 rounded-xl bg-indigo-50 px-3 py-2.5 text-sm font-semibold text-indigo-700 sm:px-3.5 sm:py-3 sm:text-[0.95rem]';
    $menuHiringClass = 'flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50 sm:mt-1 sm:px-3.5 sm:py-3 sm:text-[0.95rem]';
    $companiesIndexUrl = route('companies.public.index');
    $onCompaniesIndex = request()->routeIs('companies.public.index');
    $showCompaniesExplore = ! $talentHeader;
@endphp

<header
    @class([
        'sticky top-0 z-50 w-full border-b border-white/10 bg-indigo-600/90 backdrop-blur-md',
        'sm:border-transparent sm:bg-transparent sm:backdrop-blur-none' => $homeHeader,
        'sm:border-gray-100 sm:bg-white/95' => ! $homeHeader,
    ])
    x-data="{ menuOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 12"
    :data-scrolled="scrolled.toString()"
    :class="scrolled ? 'sm:!border-gray-100 sm:!bg-white/95 sm:!backdrop-blur-md' : ''"
    @keydown.escape.window="menuOpen = false"
    @click.outside="menuOpen = false"
    @locale-menu-open.window="menuOpen = false"
    data-public-header
>
    <div @class([
        'mx-auto w-full',
        'home-align-wide px-4 sm:px-6 lg:px-10 xl:px-12 2xl:px-10' => $fullWidth,
        'max-w-7xl px-4 sm:px-6 lg:px-8' => ! $fullWidth,
    ])>
        <div class="flex h-20 items-center gap-2 sm:h-[4.25rem] sm:gap-3">
            <div class="flex shrink-0 items-center">
                <div class="flex items-center gap-4 sm:hidden">
                    <div class="flex items-center" data-phone-logo>
                        <x-brand-logo href="{{ route('home') }}" size="md" phone />
                    </div>
                    <span @class(['h-8 w-px shrink-0', 'bg-slate-300' => $homeHeader, 'bg-white' => ! $homeHeader]) data-phone-locale-rule aria-hidden="true"></span>
                    <x-header-locale-menu align="left" :tone="$homeHeader ? 'dark' : 'light'" />
                </div>
                <div class="hidden sm:block">
                    <x-brand-logo href="{{ route('home') }}" size="sm" />
                </div>
            </div>

            <nav @class([
                'hidden items-center gap-2 xl:flex',
                'ml-4 xl:ml-8' => ! $talentHeader,
                'min-w-0 xl:flex-1 xl:justify-end' => $talentHeader,
            ]) data-header-nav-group aria-label="{{ __('talenma.nav.mobile_menu_title') }}">
                @foreach ($navLinks as $link)
                    @if (! empty($link['disabled']))
                        <span class="{{ $navLinkDisabledClass }}" aria-disabled="true" data-header-nav-disabled>{{ $link['label'] }}</span>
                    @else
                        <a
                            href="{{ $link['href'] }}"
                            @class([
                                $navHiringClass => ! empty($link['hiring']),
                                $navLinkActiveClass => empty($link['hiring']) && $link['active'],
                                $navLinkClass => empty($link['hiring']) && ! $link['active'],
                            ])
                            @if (! empty($link['hiring'])) data-header-hiring @endif
                            @if (! empty($link['blank'])) target="_blank" rel="noopener noreferrer" @endif
                            @if ($link['active']) aria-current="page" @endif
                        >{{ $link['label'] }}</a>
                        @if (! empty($link['hiring']) && $showCompaniesExplore)
                            <div
                                class="relative"
                                x-data="{ open: false }"
                                @mouseenter="open = true"
                                @mouseleave="open = false"
                                @focusin="open = true"
                                @focusout="open = false"
                                @keydown.escape.window="open = false"
                                data-header-explore-companies
                                @if ($onCompaniesIndex) data-header-explore-companies-disabled @endif
                            >
                                @if ($onCompaniesIndex)
                                    <span
                                        class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-full bg-slate-200 text-slate-400 shadow-none"
                                        aria-disabled="true"
                                        aria-current="page"
                                        aria-label="{{ __('talenma.nav.explore_companies') }}"
                                        data-header-explore-companies-icon
                                    >
                                        <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l.75 18H3.75L4.5 3Zm4.5 5.25h1.5m-1.5 3.75h1.5m3-3.75h1.5m-1.5 3.75h1.5" />
                                        </svg>
                                    </span>
                                @else
                                    <a
                                        href="{{ $companiesIndexUrl }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-950 text-white shadow-sm transition hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/70 focus-visible:ring-offset-2"
                                        aria-label="{{ __('talenma.nav.explore_companies') }}"
                                        data-header-explore-companies-icon
                                    >
                                        <svg class="h-[1.15rem] w-[1.15rem]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l.75 18H3.75L4.5 3Zm4.5 5.25h1.5m-1.5 3.75h1.5m3-3.75h1.5m-1.5 3.75h1.5" />
                                        </svg>
                                    </a>
                                @endif
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                    class="absolute left-1/2 top-full z-50 w-64 -translate-x-1/2 pt-2"
                                    style="display: none;"
                                >
                                    @if ($onCompaniesIndex)
                                        <span
                                            class="flex cursor-not-allowed items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 opacity-70 shadow-sm ring-1 ring-slate-100"
                                            aria-disabled="true"
                                            aria-current="page"
                                            data-header-explore-companies-card
                                        >
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-200 text-slate-400">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12v6.75A2.25 2.25 0 0 1 12.75 12H6.75A2.25 2.25 0 0 1 4.5 9.75V3Z" />
                                                </svg>
                                            </span>
                                            <span class="min-w-0 pt-0.5">
                                                <span class="block text-sm font-semibold text-slate-400">{{ __('talenma.nav.explore_companies') }}</span>
                                                <span class="mt-0.5 block text-xs leading-snug text-slate-400">{{ __('talenma.nav.explore_companies_hint') }}</span>
                                            </span>
                                        </span>
                                    @else
                                        <a
                                            href="{{ $companiesIndexUrl }}"
                                            class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-white p-3 shadow-lg ring-1 ring-black/5 transition hover:-translate-y-px hover:shadow-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/70"
                                            data-header-explore-companies-card
                                        >
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12v6.75A2.25 2.25 0 0 1 12.75 12H6.75A2.25 2.25 0 0 1 4.5 9.75V3Z" />
                                                </svg>
                                            </span>
                                            <span class="min-w-0 pt-0.5">
                                                <span class="block text-sm font-semibold text-slate-900">{{ __('talenma.nav.explore_companies') }}</span>
                                                <span class="mt-0.5 block text-xs leading-snug text-slate-500">{{ __('talenma.nav.explore_companies_hint') }}</span>
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
                @if ($talentHeader)
                    <span class="mx-3 h-5 w-px shrink-0 bg-gray-200" data-header-my-space-rule aria-hidden="true"></span>
                    <a href="{{ route('dashboard') }}" class="{{ $navHiringClass }}" data-header-my-space>{{ __('talenma.nav.my_space') }}</a>
                @endif
            </nav>

            <div @class([
                'flex shrink-0 items-center gap-1.5 sm:gap-3',
                'ml-auto',
                'xl:ml-11' => $talentHeader,
            ])>
                <button
                    type="button"
                    @class([
                        'relative inline-flex h-11 w-11 items-center justify-center rounded-xl transition sm:hidden',
                        'text-slate-900 hover:bg-slate-100' => $homeHeader,
                        'text-white/95 hover:bg-white/15' => ! $homeHeader,
                    ])
                    data-phone-menu
                    @click="menuOpen = ! menuOpen; if (menuOpen) $dispatch('header-menu-open')"
                    :aria-expanded="menuOpen.toString()"
                    aria-controls="public-header-menu"
                    :aria-label="menuOpen ? @js(__('talenma.nav.mobile_menu_close')) : @js(__('talenma.nav.mobile_menu_open'))"
                >
                    <svg x-show="!menuOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <svg x-show="menuOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
                @auth
                    @php $authUser = Auth::user(); @endphp
                    <span class="hidden rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap lg:inline-flex {{ $authUser->roleBadgeClasses() }}">
                        {{ $authUser->roleLabel() }}
                    </span>
                    <x-dropdown align="right" width="48" :open-on-hover="true">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-[5px] px-2 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                aria-label="{{ $authUser->headerDisplayName() }}"
                                aria-haspopup="menu"
                                data-header-display-aria
                            >
                                @if ($authUser->isCompany() && ! $authUser->isPendingApproval())
                                    <x-company-logo
                                        :profile="$authUser->companyOrganization() ?? $authUser->companyProfile"
                                        size="xs"
                                        class="ring-1 ring-gray-200"
                                    />
                                @else
                                    <x-user-avatar :user="$authUser" size="xs" class="ring-1 ring-gray-200" />
                                @endif
                                <span class="hidden max-w-[10rem] truncate xl:inline" data-header-display-name>{{ $authUser->headerDisplayName() }}</span>
                                <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" data-header-account-chevron><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">{{ $authUser->dashboardNavLabel() }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" @class([
                        'inline-flex items-center rounded-xl px-2.5 py-2.5 text-base font-semibold transition sm:hidden',
                        'text-slate-900 hover:text-indigo-700' => $homeHeader,
                        'border border-white/30 bg-white/15 text-white/95 hover:bg-white/25' => ! $homeHeader,
                    ]) data-phone-login>{{ __('talenma.nav.login') }}</a>
                    <a href="{{ route('login') }}" class="hidden px-2 py-2 text-sm font-medium text-gray-800 hover:text-indigo-700 sm:inline-flex">{{ __('talenma.nav.login') }}</a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/95 text-indigo-700 shadow-sm transition hover:bg-white sm:hidden"
                        data-phone-register
                        aria-label="{{ __('talenma.nav.register') }}"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM3 20a6 6 0 0 1 12 0v1H3v-1z"/>
                        </svg>
                    </a>
                    <a href="{{ route('register') }}" class="hidden items-center rounded-full bg-gradient-to-r from-indigo-600 to-violet-500 px-4 py-2 text-sm font-semibold leading-5 text-white shadow-sm transition hover:from-indigo-700 hover:to-violet-600 sm:inline-flex">{{ __('talenma.nav.register') }}</a>
                @endauth

                <div class="hidden sm:block">
                    <x-header-locale-menu />
                </div>

                <button
                    type="button"
                    class="hidden h-10 w-10 items-center justify-center rounded-[5px] text-gray-700 hover:bg-gray-50 sm:inline-flex xl:hidden"
                    data-phone-menu
                    @click="menuOpen = ! menuOpen; if (menuOpen) $dispatch('header-menu-open')"
                    :aria-expanded="menuOpen.toString()"
                    aria-controls="public-header-menu"
                    :aria-label="menuOpen ? @js(__('talenma.nav.mobile_menu_close')) : @js(__('talenma.nav.mobile_menu_open'))"
                >
                    <svg x-show="!menuOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <svg x-show="menuOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        id="public-header-menu"
        x-show="menuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300 sm:duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed left-1/2 top-20 z-[60] w-[min(16rem,calc(100vw-2rem))] -translate-x-1/2 overflow-hidden rounded-2xl border border-gray-100 bg-white p-1.5 shadow-xl xl:hidden sm:absolute sm:left-auto sm:right-6 sm:top-[calc(100%+0.6rem)] sm:w-72 sm:translate-x-0 sm:p-2"
    >
        <nav class="flex flex-col sm:gap-0.5" aria-label="{{ __('talenma.nav.mobile_menu_title') }}">
            @foreach ($navLinks as $link)
                @if (! empty($link['disabled']))
                    <span class="{{ $menuLinkDisabledClass }}" aria-disabled="true">{{ $link['label'] }}</span>
                @else
                    <a
                        href="{{ $link['href'] }}"
                        @class([
                            $menuHiringClass => ! empty($link['hiring']),
                            $menuLinkActiveClass => empty($link['hiring']) && $link['active'],
                            $menuLinkClass => empty($link['hiring']) && ! $link['active'],
                        ])
                        @if (! empty($link['hiring'])) data-header-hiring @endif
                        @if (! empty($link['blank'])) target="_blank" rel="noopener noreferrer" @endif
                        @click="menuOpen = false"
                    >{{ $link['label'] }}</a>
                    @if (! empty($link['hiring']) && $showCompaniesExplore)
                        @if ($onCompaniesIndex)
                            <span
                                class="{{ $menuLinkDisabledClass }}"
                                aria-disabled="true"
                                aria-current="page"
                                data-header-explore-companies
                                data-header-explore-companies-disabled
                            >
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12v6.75A2.25 2.25 0 0 1 12.75 12H6.75A2.25 2.25 0 0 1 4.5 9.75V3Z" />
                                </svg>
                                <span>{{ __('talenma.nav.explore_companies') }}</span>
                            </span>
                        @else
                            <a
                                href="{{ $companiesIndexUrl }}"
                                class="{{ $menuLinkClass }}"
                                data-header-explore-companies
                                @click="menuOpen = false"
                            >
                                <svg class="h-4 w-4 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12v6.75A2.25 2.25 0 0 1 12.75 12H6.75A2.25 2.25 0 0 1 4.5 9.75V3Z" />
                                </svg>
                                <span>{{ __('talenma.nav.explore_companies') }}</span>
                            </a>
                        @endif
                    @endif
                @endif
            @endforeach
            @if ($talentHeader)
                <a href="{{ route('dashboard') }}" class="{{ $menuHiringClass }}" data-header-my-space @click="menuOpen = false">{{ __('talenma.nav.my_space') }}</a>
            @endif
        </nav>
    </div>
</header>
