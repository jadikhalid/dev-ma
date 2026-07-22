@props(['fullWidth' => false])

<header class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur border-b border-gray-100">
    <div @class([
        'w-full mx-auto',
        'px-4 sm:px-6 lg:px-10 xl:px-12' => $fullWidth,
        'max-w-7xl px-4 sm:px-6 lg:px-8' => ! $fullWidth,
    ])>
        <div class="flex items-center justify-between h-16">
            <x-brand-logo href="{{ route('home') }}" size="sm" />
            <div class="hidden md:flex items-center">
                <x-social-links />
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="hidden lg:block">
                    <x-locale-switcher />
                </div>
                @auth
                    @if (Auth::user()->isStaff() || Auth::user()->isTalent() || (Auth::user()->isCompany() && Auth::user()->isPendingApproval()))
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    aria-label="{{ Auth::user()->name }}"
                                >
                                    <x-user-avatar :user="Auth::user()" size="sm" />
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">{{ __('talenma.nav.dashboard') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @elseif (Auth::user()->isCompany())
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    aria-label="{{ Auth::user()->companyProfile?->company_name ?? Auth::user()->name }}"
                                >
                                    <x-company-logo
                                        :profile="Auth::user()->companyProfile"
                                        size="sm"
                                        class="!rounded-full ring-1 ring-gray-200"
                                    />
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">{{ __('talenma.nav.dashboard') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @endif
                @else
                    {{-- Mobile : icônes --}}
                    <a href="{{ route('login') }}"
                       class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg border border-indigo-200/80 bg-indigo-50/60 text-indigo-600 hover:bg-indigo-100 hover:border-indigo-300 transition"
                       aria-label="{{ __('talenma.nav.login') }}"
                       title="{{ __('talenma.nav.login') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                    </a>
                    <a href="{{ route('register') }}"
                       class="sm:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition"
                       aria-label="{{ __('talenma.nav.register') }}"
                       title="{{ __('talenma.nav.register') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </a>

                    {{-- Desktop : boutons texte --}}
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-indigo-600 rounded-lg border border-indigo-200/80 bg-indigo-50/60 hover:bg-indigo-100 hover:border-indigo-300 hover:text-indigo-700 hover:shadow-sm transition-all duration-300 ease-in-out">{{ __('talenma.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-300 ease-in-out">{{ __('talenma.nav.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
