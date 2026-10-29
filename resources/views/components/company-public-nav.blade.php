@props([])

@php
    use App\Support\PortalHost;

    $bandLinks = collect([
        ['route' => 'company.offers', 'label' => __('talenma.nav.offers')],
    ])->map(fn (array $link) => $link + ['active' => request()->routeIs($link['route'])]);
    $talentSpaceUrl = PortalHost::wwwRootUrl();
    $showGuestCtas = ! auth()->user()?->isCompany();
    $heroBleed = true;
@endphp

<header
    @class([
        'sticky top-0 z-[55] w-full border-b transition-[background-color,border-color,box-shadow] duration-200',
        'border-[#EEF0F4] bg-white' => ! $heroBleed,
        'border-transparent bg-transparent' => $heroBleed,
    ])
    x-data="{ mobileNav: false, scrolled: {{ $heroBleed ? 'false' : 'true' }} }"
    x-init="
        $watch('mobileNav', (value) => document.documentElement.classList.toggle('overflow-hidden', value));
        @if ($heroBleed)
            scrolled = window.scrollY > 12;
        @endif
    "
    @if ($heroBleed)
        @scroll.window="scrolled = window.scrollY > 12"
        :data-scrolled="scrolled.toString()"
        :class="(scrolled || mobileNav) ? '!border-[#EEF0F4] !bg-white shadow-[0_1px_0_rgba(17,24,39,0.04)]' : ''"
    @endif
    @keydown.escape.window="mobileNav = false"
    data-company-header
>
    <div
        class="mx-auto flex h-[72px] w-full max-w-7xl items-center justify-between gap-4 px-4 sm:h-[84px] sm:px-6 lg:px-8"
        data-company-header-bar
        data-company-header-band
    >
        <div class="flex min-w-0 items-center gap-5 lg:gap-8">
            <a
                href="{{ route('company.offer') }}"
                class="inline-flex shrink-0 items-center gap-3"
                aria-label="{{ __('talenma.meta.title') }} — {{ __('talenma.nav.companies_badge') }}"
            >
                <x-brand-logo :linked="false" size="sm" />
                <span class="rounded-lg bg-[#EEF0FF] px-2 py-1 text-[11px] font-bold tracking-[0.08em] text-[#5546F0]">
                    {{ __('talenma.nav.companies_badge') }}
                </span>
            </a>

            <nav class="hidden items-center gap-6 whitespace-nowrap text-[15px] font-medium lg:flex" aria-label="{{ __('talenma.nav.mobile_menu_title') }}">
                @foreach ($bandLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        @class([
                            'py-1.5 transition',
                            'font-bold text-[#5546F0] shadow-[inset_0_-2px_0_#5546F0]' => $link['active'],
                            'text-gray-900 hover:text-[#5546F0]' => ! $link['active'],
                        ])
                        @if ($link['active']) aria-current="page" @endif
                        data-company-header-link="{{ $link['route'] }}"
                    >{{ $link['label'] }}</a>
                @endforeach
                <a
                    href="{{ $talentSpaceUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="py-1.5 text-gray-900 transition hover:text-[#5546F0]"
                    data-company-header-talent-space
                >{{ __('talenma.nav.talent_space') }}</a>
            </nav>
        </div>

        <div class="flex items-center gap-2 sm:gap-2.5">
            @auth
                @php $authUser = Auth::user(); @endphp
                @if ($authUser->isCompany())
                    <div class="flex min-w-0 items-center gap-2" data-company-portal-identity>
                        <span class="hidden max-w-[12rem] truncate rounded-full px-2.5 py-1 text-xs font-medium sm:inline-flex {{ $authUser->roleBadgeClasses() }}">
                            {{ $authUser->roleLabel() }}
                        </span>
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center gap-2 rounded-xl px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50" aria-label="{{ $authUser->headerDisplayName() }}">
                                    <x-company-logo :profile="$authUser->companyOrganization()" size="xs" class="ring-1 ring-gray-200" />
                                    <span class="hidden max-w-[10rem] truncate font-medium lg:inline">{{ $authUser->headerDisplayName() }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                                        {{ $authUser->dashboardNavLabel() }}
                                    </span>
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                                            {{ __('talenma.nav.logout') }}
                                        </span>
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @elseif ($authUser->isStaff())
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center rounded-xl bg-[#5546F0] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#4338CA]"
                    >{{ $authUser->dashboardNavLabel() }}</a>
                @else
                    <a
                        href="{{ PortalHost::wwwRootUrl() }}"
                        class="hidden items-center rounded-xl border border-gray-200 px-3.5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 sm:inline-flex"
                    >{{ __('talenma.nav.public_site') }}</a>
                @endif
            @endauth

            @if ($showGuestCtas)
                @if (request()->routeIs('company.demo'))
                    <button
                        type="button"
                        disabled
                        class="hidden cursor-not-allowed items-center justify-center whitespace-nowrap rounded-xl border-[1.5px] border-gray-300 px-4 py-3 text-[15px] font-semibold text-gray-400 sm:inline-flex"
                        data-company-header-demo
                    >{{ __('talenma.nav.request_demo') }}</button>
                @else
                    <a
                        href="{{ route('company.demo') }}"
                        class="hidden items-center justify-center whitespace-nowrap rounded-xl border-[1.5px] border-gray-900 px-4 py-3 text-[15px] font-semibold text-gray-900 transition hover:bg-gray-50 sm:inline-flex"
                        data-company-header-demo
                    >{{ __('talenma.nav.request_demo') }}</a>
                @endif

                @if (request()->routeIs('company.trial'))
                    <button
                        type="button"
                        disabled
                        class="hidden cursor-not-allowed items-center justify-center whitespace-nowrap rounded-xl bg-gray-200 px-[18px] py-3 text-[15px] font-semibold text-gray-400 sm:inline-flex"
                        data-company-header-trial
                    >{{ __('talenma.company_offer.hero_cta_trial') }}</button>
                @else
                    <a
                        href="{{ route('company.trial') }}"
                        class="hidden items-center justify-center whitespace-nowrap rounded-xl bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)] px-[18px] py-3 text-[15px] font-semibold text-white shadow-[0_12px_24px_-12px_rgba(79,70,229,0.8)] transition hover:brightness-105 sm:inline-flex"
                        data-company-header-trial
                    >{{ __('talenma.company_offer.hero_cta_trial') }}</a>
                @endif
            @endif

            @guest
                @php
                    $loginPanelOpen = request()->boolean('login') || filled(old('login_panel')) || filled(session('status'));
                @endphp
                <div
                    class="relative"
                    x-data="{ open: @js($loginPanelOpen) }"
                    x-init="
                        const autofocus = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches && $nextTick(() => $refs.loginEmail.focus());
                        $watch('open', (value) => { if (value) autofocus() });
                        if (open) autofocus();
                    "
                    @keydown.escape.window="open = false"
                    @click.outside="open = false"
                    data-company-login-panel
                >
                    <button
                        type="button"
                        @click="open = ! open"
                        :aria-expanded="open.toString()"
                        aria-controls="company-login-panel"
                        class="inline-flex items-center px-2 py-3 text-[15px] font-medium text-gray-900 transition hover:text-[#5546F0]"
                    >
                        {{ __('talenma.nav.company_login') }}
                    </button>

                    <div
                        id="company-login-panel"
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-3 scale-y-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-y-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-y-100"
                        x-transition:leave-end="opacity-0 -translate-y-3 scale-y-95"
                        class="fixed inset-x-4 top-[72px] z-50 mt-2 origin-top rounded-2xl border border-gray-200 bg-white p-5 shadow-xl sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:w-96"
                        role="dialog"
                        aria-label="{{ __('talenma.auth.login_title') }}"
                    >
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <form method="POST" action="{{ route('login') }}">@csrf
                            <input type="hidden" name="login_panel" value="1">
                            <div>
                                <x-input-label for="company-login-email" :value="__('talenma.auth.email')" />
                                <x-text-input
                                    id="company-login-email"
                                    name="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    :value="old('login_panel') ? old('email') : ''"
                                    required
                                    autocomplete="username"
                                    inputmode="email"
                                    x-ref="loginEmail"
                                />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="company-login-password" :value="__('talenma.auth.password')" />
                                <x-text-input id="company-login-password" name="password" type="password" class="mt-1 block w-full" required autocomplete="current-password" />
                            </div>
                            <label class="mt-4 flex items-center">
                                <input type="checkbox" name="remember" class="size-4 rounded text-indigo-600" autocomplete="off">
                                <span class="ms-2.5 text-sm text-gray-600">{{ __('talenma.auth.remember') }}</span>
                            </label>
                            <div class="mt-5 flex items-center justify-between gap-3">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('talenma.auth.forgot') }}</a>
                                @endif
                                <x-primary-button class="justify-center">{{ __('talenma.auth.login_btn') }}</x-primary-button>
                            </div>
                            <p class="mt-4 text-center text-sm text-gray-600" data-company-login-trial-link>
                                {{ __('talenma.auth.no_company_account') }}
                                <a
                                    href="{{ route('company.trial') }}"
                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                >{{ __('talenma.company_offer.cta_trial') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            @endguest

            <div class="hidden sm:block">
                <x-header-locale-menu />
            </div>

            <button
                type="button"
                @click="mobileNav = true"
                :aria-expanded="mobileNav.toString()"
                aria-controls="company-mobile-nav"
                aria-label="{{ __('talenma.nav.mobile_menu_open') }}"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 text-gray-900 transition hover:bg-gray-50 lg:hidden"
                data-company-mobile-nav-toggle
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>

    <x-company-mobile-nav :band-links="$bandLinks" :talent-space-url="$talentSpaceUrl" />
</header>
