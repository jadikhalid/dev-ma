@php
    $pendingAccount = Auth::user()->isPendingApproval();
    $catalogBlocked = Auth::user()->isCompany()
        && ! app(\App\Services\CompanyProfileCompletionService::class)
            ->assess(Auth::user()->companyProfile)['is_catalog_ready'];
@endphp

<nav x-data="{ open: false }" class="relative bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                <x-brand-logo :href="route('home')" size="sm" />
                <div class="hidden lg:flex items-center gap-1 min-w-0">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :disabled="$pendingAccount">{{ __('talenma.nav.dashboard') }}</x-nav-link>
                    @if (Auth::user()->isStaff())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('talenma.nav.admin_users') }}</x-nav-link>
                        @if (Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')">{{ __('talenma.nav.admin_publications') }}</x-nav-link>
                        @endif
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('talenma.nav.my_account') }}</x-nav-link>
                    @elseif (Auth::user()->isTalent())
                        <x-nav-link :href="route('profile.details.edit')" :active="request()->routeIs('profile.details.*')" :disabled="$pendingAccount">{{ __('talenma.nav.my_profile') }}</x-nav-link>
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" :disabled="$pendingAccount">{{ __('talenma.nav.my_account') }}</x-nav-link>
                    @elseif (Auth::user()->isCompany())
                        <x-nav-link :href="route('company.search')" :active="request()->routeIs('company.search') || request()->routeIs('company.talent.*')" :disabled="$pendingAccount || $catalogBlocked">{{ __('talenma.nav.talents') }}</x-nav-link>
                        <x-nav-link :href="route('company.profile.edit')" :active="request()->routeIs('company.profile.*')" :disabled="$pendingAccount">{{ __('talenma.nav.my_company') }}</x-nav-link>
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')" :disabled="$pendingAccount">{{ __('talenma.nav.morocco_setup') }}</x-nav-link>
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" :disabled="$pendingAccount">{{ __('talenma.nav.my_account') }}</x-nav-link>
                    @endif
                </div>
            </div>
            <div class="hidden lg:flex items-center gap-3 shrink-0">
                <x-locale-switcher />
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ Auth::user()->isAdmin() ? 'bg-violet-100 text-violet-700' : (Auth::user()->isModerator() ? 'bg-purple-100 text-purple-700' : (Auth::user()->isTalent() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700')) }}">
                    @if (Auth::user()->isAdmin())
                        {{ __('talenma.roles.admin') }}
                    @elseif (Auth::user()->isModerator())
                        {{ __('talenma.roles.moderator') }}
                    @elseif (Auth::user()->isTalent())
                        {{ __('talenma.roles.talent') }}
                    @else
                        {{ __('talenma.roles.company') }}
                    @endif
                </span>
                <x-dropdown align="right" width="48" :open-on-hover="true">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="hidden xl:inline">{{ Auth::user()->name }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">{{ __('talenma.nav.home') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="flex items-center gap-2 lg:hidden shrink-0">
                <x-locale-switcher />
                <button
                    type="button"
                    @click="open = !open"
                    class="p-2 text-gray-500"
                    :aria-expanded="open"
                    aria-controls="mobile-navigation-menu"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        id="mobile-navigation-menu"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        class="lg:hidden absolute left-0 right-0 top-full z-50 border-t border-gray-200 bg-white shadow-lg"
    >
        <div class="max-h-[calc(100vh-4rem)] overflow-y-auto py-2">
            @unless ($pendingAccount)
                <div class="border-b border-gray-100 pb-2 mb-2">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('talenma.nav.dashboard') }}</x-responsive-nav-link>
                    @if (Auth::user()->isStaff())
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('talenma.nav.admin_users') }}</x-responsive-nav-link>
                        @if (Auth::user()->isAdmin())
                            <x-responsive-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')">{{ __('talenma.nav.admin_publications') }}</x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('talenma.nav.my_account') }}</x-responsive-nav-link>
                    @elseif (Auth::user()->isTalent())
                        <x-responsive-nav-link :href="route('profile.details.edit')" :active="request()->routeIs('profile.details.*')">{{ __('talenma.nav.my_profile') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('talenma.nav.my_account') }}</x-responsive-nav-link>
                    @elseif (Auth::user()->isCompany())
                        <x-responsive-nav-link :href="route('company.search')" :active="request()->routeIs('company.search') || request()->routeIs('company.talent.*')" :disabled="$catalogBlocked">{{ __('talenma.nav.talents') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('company.profile.edit')" :active="request()->routeIs('company.profile.*')">{{ __('talenma.nav.my_company') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">{{ __('talenma.nav.morocco_setup') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('talenma.nav.my_account') }}</x-responsive-nav-link>
                    @endif
                </div>
            @endunless
            <x-dropdown-link :href="route('home')">{{ __('talenma.nav.home') }}</x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
            </form>
        </div>
    </div>
</nav>
