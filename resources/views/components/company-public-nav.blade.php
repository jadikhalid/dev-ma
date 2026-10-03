@props([])

@php
    use App\Support\PortalHost;

    $platformMenuItems = [
        ['anchor' => 'platform-catalogue', 'tone' => 'bg-amber-300', 'icon' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z'],
        ['anchor' => 'platform-applications', 'tone' => 'bg-emerald-300', 'icon' => 'M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5'],
        ['anchor' => 'platform-sourcing', 'tone' => 'bg-rose-300', 'icon' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z'],
        ['anchor' => 'platform-jobs', 'tone' => 'bg-sky-300', 'icon' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z'],
    ];

    $bandLinks = collect([
        ['route' => 'company.offers', 'label' => __('talenma.nav.offers')],
        ['route' => 'company.case-studies', 'label' => __('talenma.nav.case_studies')],
        ['route' => 'company.about', 'label' => __('talenma.nav.about')],
    ])->map(fn (array $link) => $link + ['active' => request()->routeIs($link['route'])]);
    $bandLinkActive = $bandLinks->contains('active', true);
@endphp

<header
    class="sticky top-0 z-[55] h-16 w-full sm:h-28"
    x-data="{
        scrolled: false,
        mobileNav: false,
        mobilePlatform: false,
        sync() {
            if (window.matchMedia('(max-width: 639px)').matches) {
                this.scrolled = false;
                return;
            }
            this.mobileNav = false;
            const y = window.scrollY;
            if (! this.scrolled && y > 110) this.scrolled = true;
            else if (this.scrolled && y < 90) this.scrolled = false;
        },
    }"
    x-init="sync(); $watch('mobileNav', (value) => document.documentElement.classList.toggle('overflow-hidden', value))"
    @scroll.window.passive="sync()"
    @resize.window.debounce.150ms="sync()"
    @keydown.escape.window="mobileNav = false"
    :class="scrolled && 'pointer-events-none'"
    data-company-header
>
    <div
        class="h-full border-b border-indigo-100/80 bg-white transition duration-500 ease-in-out"
        :class="scrolled ? '-translate-y-full opacity-0' : 'opacity-100'"
        data-company-header-bar
    >
    <div class="home-align-wide mx-auto flex h-16 w-full items-center sm:h-28 justify-between gap-3 px-4 sm:px-6 lg:px-10 xl:px-12 2xl:px-10">
        <div class="flex shrink-0 items-center">
            <x-brand-logo href="{{ route('company.offer') }}" size="sm" />
        </div>

        <div class="flex items-center gap-2.5 sm:gap-3">
            <div class="hidden sm:block">
                <x-locale-switcher />
            </div>

            @auth
                @php $authUser = Auth::user(); @endphp
                @if ($authUser->isCompany())
                    <div class="flex min-w-0 items-center gap-2" data-company-portal-identity>
                        <span class="hidden sm:inline-flex max-w-[14rem] truncate text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap {{ $authUser->roleBadgeClasses() }}">
                            {{ $authUser->roleLabel() }}
                        </span>
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-50" aria-label="{{ $authUser->headerDisplayName() }}">
                                    <x-company-logo :profile="$authUser->companyOrganization()" size="xs" class="ring-1 ring-gray-200" />
                                    <span class="hidden sm:inline max-w-[14rem] truncate font-medium">{{ $authUser->headerDisplayName() }}</span>
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
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >{{ $authUser->dashboardNavLabel() }}</a>
                @else
                    <a
                        href="{{ PortalHost::wwwRootUrl() }}"
                        class="inline-flex items-center rounded-lg border border-gray-200 px-3.5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >{{ __('talenma.nav.public_site') }}</a>
                @endif
            @else
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
                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                    >
                        {{ __('talenma.nav.company_login') }}
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
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
                        class="fixed inset-x-4 top-16 z-50 mt-2 origin-top rounded-2xl border border-gray-200 bg-white p-5 shadow-xl sm:absolute sm:inset-x-auto sm:right-0 sm:top-full sm:w-96"
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
                        </form>
                    </div>
                </div>
            @endauth

            <button
                type="button"
                @click="mobileNav = true"
                :aria-expanded="mobileNav.toString()"
                aria-controls="company-mobile-nav"
                aria-label="{{ __('talenma.nav.mobile_menu_open') }}"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 text-indigo-700 transition-colors duration-300 hover:bg-indigo-50 sm:hidden"
                data-company-mobile-nav-toggle
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </div>
    </div>

    <x-company-mobile-nav :platform-menu-items="$platformMenuItems" :band-links="$bandLinks" />

    <div
        class="pointer-events-auto absolute left-1/2 z-10 hidden h-16 w-[65vw] -translate-x-1/2 -translate-y-1/2 items-center justify-between gap-2 rounded-[10px] border border-indigo-100 bg-white/95 px-3.5 shadow-lg shadow-indigo-500/10 backdrop-blur-md transition-[top] duration-500 ease-in-out sm:flex"
        :class="scrolled ? 'top-[52px]' : 'top-full'"
        x-data="{
            menu: false,
            more: false,
            timer: null,
            show() { clearTimeout(this.timer); this.menu = true; this.more = false },
            hide() { clearTimeout(this.timer); this.timer = setTimeout(() => this.menu = false, 150) },
        }"
        @keydown.escape.window="menu = false; more = false"
        @click.outside="menu = false"
        data-company-header-band
    >
        <div class="flex min-w-0 items-center gap-1.5 sm:gap-2">
        <div class="flex items-center" @mouseenter="show()" @mouseleave="hide()" @focusin="show()">
            <a
                href="{{ PortalHost::wwwRootUrl() }}"
                @click="if (! menu && window.matchMedia('(hover: none)').matches) { $event.preventDefault(); show() }"
                :aria-expanded="menu.toString()"
                aria-haspopup="true"
                aria-controls="company-platform-menu"
                :class="menu ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-indigo-300 text-indigo-700'"
                class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border px-2.5 py-[9px] text-xs font-semibold transition-colors duration-300 ease-in-out hover:border-indigo-600 hover:bg-indigo-600 hover:text-white sm:px-5 sm:py-[11px] sm:text-sm"
                data-company-platform-trigger
            >
                {{ __('talenma.nav.our_platform') }}
                <svg class="h-4 w-4 shrink-0 transition-transform duration-300 ease-in-out" :class="menu ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </a>
        </div>

            @foreach ($bandLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    @class([
                        'hidden items-center whitespace-nowrap rounded-md border px-5 py-[11px] text-sm font-semibold transition-colors duration-300 ease-in-out hover:border-indigo-600 hover:bg-indigo-600 hover:text-white lg:inline-flex',
                        'border-indigo-600 bg-indigo-600 text-white' => $link['active'],
                        'border-indigo-300 text-indigo-700' => ! $link['active'],
                    ])
                    @if ($link['active']) aria-current="page" @endif
                    data-company-header-link="{{ $link['route'] }}"
                >{{ $link['label'] }}</a>
            @endforeach

            <div class="relative lg:hidden" @click.outside="more = false">
                <button
                    type="button"
                    @click="more = ! more; menu = false"
                    :aria-expanded="more.toString()"
                    aria-controls="company-band-more"
                    class="inline-flex items-center gap-1 whitespace-nowrap rounded-md border px-2.5 py-[9px] text-xs font-semibold transition-colors duration-300 ease-in-out hover:border-indigo-600 hover:bg-indigo-600 hover:text-white sm:px-4 sm:py-[11px] sm:text-sm"
                    :class="more || @js($bandLinkActive) ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-indigo-300 text-indigo-700'"
                    data-company-band-more
                >
                    {{ __('talenma.nav.band_more') }}
                    <svg class="h-4 w-4 shrink-0 transition-transform duration-300 ease-in-out" :class="more ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>

                <div
                    id="company-band-more"
                    x-show="more"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="absolute left-0 top-full z-40 w-52 pt-4"
                >
                    <div class="space-y-1 rounded-xl border border-gray-200 bg-white p-2 shadow-2xl shadow-indigo-950/10">
                        @foreach ($bandLinks as $link)
                            <a
                                href="{{ route($link['route']) }}"
                                @class([
                                    'block rounded-lg px-3 py-2.5 text-sm font-semibold transition-colors duration-300',
                                    'bg-indigo-600 text-white' => $link['active'],
                                    'text-gray-800 hover:bg-indigo-50 hover:text-indigo-700' => ! $link['active'],
                                ])
                                @if ($link['active']) aria-current="page" @endif
                            >{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div
            id="company-platform-menu"
            x-show="menu"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @mouseenter="show()"
            @mouseleave="hide()"
            class="absolute left-1/2 top-full z-40 w-[calc(100vw-2rem)] max-w-4xl -translate-x-1/2 pt-3 lg:w-full lg:max-w-none"
            data-company-platform-menu
        >
            <div class="grid max-h-[calc(100vh-10rem)] gap-4 overflow-y-auto rounded-2xl border border-gray-200 bg-gray-50 p-3 shadow-2xl shadow-indigo-950/10 sm:p-4 md:grid-cols-2">
                <div class="flex flex-col rounded-xl bg-indigo-600 p-5 text-white sm:p-6">
                    <div class="relative mx-auto hidden h-32 w-60 md:block" aria-hidden="true">
                        @foreach ($platformMenuItems as $index => $item)
                            <div class="absolute w-36 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black/5" style="left: {{ $index * 28 }}px; top: {{ $index * 18 }}px;">
                                <div class="{{ $item['tone'] }} px-2 py-0.5 text-center text-[9px] font-semibold text-gray-900">{{ __('talenma.nav.platform_menu_short_'.($index + 1)) }}</div>
                                <div class="space-y-1 p-2">
                                    <div class="h-1 w-3/4 rounded bg-gray-200"></div>
                                    <div class="h-1 w-1/2 rounded bg-gray-200"></div>
                                    <div class="h-1 w-2/3 rounded bg-gray-200"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xl font-extrabold tracking-tight md:mt-6">{{ __('talenma.nav.platform_menu_title') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-indigo-100">{{ __('talenma.nav.platform_menu_text') }}</p>
                    <a
                        href="{{ PortalHost::wwwRootUrl() }}"
                        class="mt-5 inline-flex self-start rounded-md bg-gray-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >{{ __('talenma.nav.platform_menu_cta') }}</a>
                </div>

                <div class="flex flex-col">
                    <ul class="space-y-2.5" role="list">
                        @foreach ($platformMenuItems as $index => $item)
                            <li>
                                <a
                                    href="{{ route('company.offer') }}#{{ $item['anchor'] }}"
                                    @click="menu = false"
                                    class="flex items-center gap-3.5 rounded-lg border border-gray-200 bg-white px-4 py-3.5 text-sm font-bold text-gray-950 transition hover:border-indigo-300 hover:text-indigo-700"
                                    data-company-platform-link="{{ $item['anchor'] }}"
                                >
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                                    </span>
                                    <span>{{ __('talenma.company_offer.includes_'.($index + 1)) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-auto px-1 pt-4">
                        <p class="text-sm font-medium text-gray-700">{{ __('talenma.nav.platform_menu_footer') }}</p>
                        <a href="{{ PortalHost::wwwRootUrl() }}" class="mt-1 inline-flex text-sm italic text-gray-500 underline underline-offset-2 hover:text-indigo-700">{{ __('talenma.nav.platform_menu_footer_link') }} &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        @unless (auth()->user()?->isCompany())
            <a
                href="{{ route('company.offer', ['tab' => 'demo']) }}"
                @if (request()->routeIs('company.offer'))
                    @click.prevent="$dispatch('company-offer-drawer', 'demo')"
                @endif
                class="inline-flex shrink-0 items-center justify-center whitespace-nowrap rounded-md border-2 border-gray-950 bg-gray-950 px-2.5 py-2 text-xs font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800 sm:px-5 sm:py-2.5 sm:text-sm"
                data-company-header-demo
            >
                <span class="xl:hidden">{{ __('talenma.nav.request_demo_short') }}</span>
                <span class="hidden xl:inline">{{ __('talenma.nav.request_demo') }}</span>
            </a>
        @endunless
    </div>
</header>
