@props([
    'fullWidth' => false,
])

<header
    class="sticky top-0 z-50 w-full backdrop-blur-md bg-indigo-600/90 border-b border-white/10 sm:bg-white/90 sm:border-gray-100"
>
    <div @class([
        'w-full mx-auto',
        'px-4 sm:px-6 lg:px-10 xl:px-12' => $fullWidth,
        'max-w-7xl px-4 sm:px-6 lg:px-8' => ! $fullWidth,
    ])>
        <div class="flex items-center justify-between h-20 sm:h-16">
            <div class="sm:hidden">
                <x-brand-logo href="{{ route('home') }}" size="md" :light="true" />
            </div>
            <div class="hidden sm:block">
                <x-brand-logo href="{{ route('home') }}" size="sm" />
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="hidden lg:block">
                    <x-locale-switcher />
                </div>
                @auth
                    @php $authUser = Auth::user(); @endphp
                    <span class="hidden sm:inline-flex text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap {{ $authUser->roleBadgeClasses() }}">
                        {{ $authUser->roleLabel() }}
                    </span>
                    @if ($authUser->isTalent())
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 sm:gap-2 px-2 py-2 sm:px-3 text-sm text-white/95 hover:bg-white/15 sm:text-gray-600 sm:hover:bg-gray-50 rounded-lg"
                                    aria-label="{{ $authUser->headerDisplayName() }}"
                                    aria-haspopup="menu"
                                    data-header-display-aria
                                >
                                    <x-user-avatar :user="$authUser" size="xs" class="ring-1 ring-white/40 sm:ring-gray-200" />
                                    <svg class="sm:hidden h-4 w-4 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span class="hidden xl:inline" data-header-display-name>{{ $authUser->headerDisplayName() }}</span>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">{{ __('talenma.nav.dashboard') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @elseif ($authUser->isStaff() || ($authUser->isCompany() && $authUser->isPendingApproval()))
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 sm:gap-2 px-2 py-2 sm:px-3 text-sm text-white/95 hover:bg-white/15 sm:text-gray-600 sm:hover:bg-gray-50 rounded-lg"
                                    aria-label="{{ $authUser->headerDisplayName() }}"
                                    aria-haspopup="menu"
                                    data-header-display-aria
                                >
                                    <x-user-avatar :user="$authUser" size="xs" class="ring-1 ring-white/40 sm:ring-gray-200" />
                                    <svg class="sm:hidden h-4 w-4 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span class="hidden xl:inline" data-header-display-name>{{ $authUser->headerDisplayName() }}</span>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('dashboard')">{{ __('talenma.nav.dashboard') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @elseif ($authUser->isCompany())
                        <x-dropdown align="right" width="48" :open-on-hover="true">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-1.5 sm:gap-2 px-2 py-2 sm:px-3 text-sm text-white/95 hover:bg-white/15 sm:text-gray-600 sm:hover:bg-gray-50 rounded-lg"
                                    aria-label="{{ $authUser->headerDisplayName() }}"
                                    aria-haspopup="menu"
                                    data-header-display-aria
                                >
                                    <x-company-logo
                                        :profile="$authUser->companyOrganization() ?? $authUser->companyProfile"
                                        size="xs"
                                        class="ring-1 ring-white/40 sm:ring-gray-200"
                                    />
                                    <svg class="sm:hidden h-4 w-4 shrink-0 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <span class="hidden xl:inline" data-header-display-name>{{ $authUser->headerDisplayName() }}</span>
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
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center px-4 py-2.5 sm:px-4 sm:py-2 text-base sm:text-sm font-semibold rounded-xl sm:rounded-lg transition-all duration-300 ease-in-out text-white/95 border border-white/30 bg-white/15 hover:bg-white/25 sm:text-indigo-600 sm:border-indigo-200/80 sm:bg-indigo-50/60 sm:hover:bg-indigo-100 sm:hover:border-indigo-300 sm:hover:text-indigo-700 sm:hover:shadow-sm"
                    >{{ __('talenma.nav.login') }}</a>
                    <a
                        href="{{ route('register') }}"
                        class="sm:hidden inline-flex items-center justify-center w-11 h-11 rounded-xl transition bg-white/95 text-indigo-700 hover:bg-white shadow-sm"
                        aria-label="{{ __('talenma.nav.register') }}"
                        title="{{ __('talenma.nav.register') }}"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-300 ease-in-out">{{ __('talenma.nav.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
