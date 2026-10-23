@props([
    'fullWidth' => false,
])

@php
    $onCompanyShow = request()->routeIs('companies.public.show');
    $showExploreCompanies = $onCompanyShow;

    $talentHeader = auth()->check() && auth()->user()->isTalent();
    $homeHeader = request()->routeIs('home', 'career.how');

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
                @if ($showExploreCompanies)
                    <a
                        href="{{ route('companies.public.index') }}"
                        class="hidden items-center rounded-[5px] bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 lg:inline-flex"
                        data-header-explore-companies
                    >{{ __('talenma.nav.explore_companies') }}</a>
                @endif

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
                @endif
            @endforeach
            @if ($talentHeader)
                <a href="{{ route('dashboard') }}" class="{{ $menuHiringClass }}" data-header-my-space @click="menuOpen = false">{{ __('talenma.nav.my_space') }}</a>
            @endif
            @if ($showExploreCompanies)
                <a href="{{ route('companies.public.index') }}" class="flex items-center rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-indigo-700 sm:px-3.5 sm:py-3 sm:text-[0.95rem] lg:hidden" data-header-explore-companies @click="menuOpen = false">{{ __('talenma.nav.explore_companies') }}</a>
            @endif
        </nav>
    </div>
</header>
