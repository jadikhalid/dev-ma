@props([
    'fullWidth' => false,
])

@php
    use App\Models\JobPosting;

    $freshJobTimestamps = JobPosting::query()
        ->where('status', JobPosting::STATUS_PUBLISHED)
        ->whereNotNull('published_at')
        ->where('published_at', '>=', now()->subDay())
        ->orderByDesc('published_at')
        ->pluck('published_at')
        ->map(fn ($publishedAt) => $publishedAt->getTimestamp())
        ->values()
        ->all();
@endphp

<header
    @class([
        'sticky top-0 z-50 w-full backdrop-blur-md bg-indigo-600/90 sm:bg-white/90',
        'border-b border-white/10 sm:border-gray-100' => ! $fullWidth,
        'border-b border-white/10 sm:border-gray-100 2xl:border-b-0' => $fullWidth,
    ])
>
    <div @class([
        'w-full mx-auto',
        'home-align-wide px-4 sm:px-6 lg:px-10 xl:px-12 2xl:px-10' => $fullWidth,
        'max-w-7xl px-4 sm:px-6 lg:px-8' => ! $fullWidth,
    ])>
        <div @class([
            'flex items-center justify-between h-20 sm:h-16',
            '2xl:border-b 2xl:border-gray-100' => $fullWidth,
        ])>
            <div class="brand-logo-phone">
                <x-brand-logo href="{{ route('home') }}" size="md" classic :light="true" />
            </div>
            <div class="brand-logo-desktop">
                <x-brand-logo href="{{ route('home') }}" size="sm" />
            </div>

            <div class="flex items-center gap-2.5 sm:gap-3">
                <a
                    href="{{ route('home') }}#opportunites"
                    class="relative mr-0.5 sm:mr-1 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-semibold transition text-white/95 hover:bg-white/15 sm:text-indigo-700 sm:hover:bg-indigo-50"
                    x-data="homeAnnoncesNav({
                        timestamps: @js($freshJobTimestamps),
                        homeUrl: @js(route('home')),
                        sectionId: 'opportunites',
                        storageKey: 'tdm.home.annonces.seen_at',
                    })"
                    @click="onClick($event)"
                    :aria-label="badgeCount > 0 ? @js(__('talenma.nav.annonces_with_new')).replace(':count', String(badgeCount)) : @js(__('talenma.nav.jobs'))"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.875 1.975-1.95 1.975H5.7c-1.075 0-1.95-.881-1.95-1.975v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.875-1.954-1.95-1.954h-3.15V4.875C14.25 3.839 13.41 3 12.375 3h-0.75C10.59 3 9.75 3.839 9.75 4.875V6.752H6.6c-1.075 0-1.95.873-1.95 1.954v3.783c0 .655.287 1.252.75 1.661m16.5 0H3.75"/>
                    </svg>
                    <span class="relative inline-block leading-none">
                        <span
                            x-show="badgeCount > 0"
                            x-cloak
                            x-text="badgeCount > 99 ? '99+' : badgeCount"
                            class="pointer-events-none absolute -top-2 -right-2.5 z-20 inline-flex h-4 min-w-4 items-center justify-center rounded-[50%] bg-rose-500 px-1 text-center text-[10px] font-bold leading-none text-white shadow-sm ring-2 ring-indigo-600 sm:ring-white"
                        ></span>
                        <span>{{ __('talenma.nav.jobs') }}</span>
                    </span>
                </a>
                <a
                    href="{{ route('blog.index') }}"
                    class="mr-1 sm:mr-2 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-semibold transition text-white/95 hover:bg-white/15 sm:text-indigo-700 sm:hover:bg-indigo-50"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5"/>
                    </svg>
                    <span>{{ __('talenma.nav.blog') }}</span>
                </a>
                <div class="hidden lg:block">
                    <x-locale-switcher />
                </div>
                @auth
                    @php $authUser = Auth::user(); @endphp
                    <span class="hidden sm:inline-flex text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap {{ $authUser->roleBadgeClasses() }}">
                        {{ $authUser->roleLabel() }}
                    </span>
                    @if ($authUser->canAccessWorkspaceApps())
                        <x-talent-apps-launcher />
                    @endif
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
                    <x-talent-apps-launcher :guest="true" />
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
