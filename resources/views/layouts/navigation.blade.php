<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <x-brand-logo :href="route('dashboard')" size="sm" />
                <div class="hidden sm:flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('talenma.nav.dashboard') }}</x-nav-link>
                    @if (Auth::user()->isStaff())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('talenma.nav.admin_users') }}</x-nav-link>
                        @if (Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')">{{ __('talenma.nav.admin_publications') }}</x-nav-link>
                        @endif
                    @elseif (Auth::user()->isTalent())
                        <x-nav-link :href="route('profile.details.edit')" :active="request()->routeIs('profile.details.*')">{{ __('talenma.nav.my_profile') }}</x-nav-link>
                    @elseif (Auth::user()->isCompany())
                        <x-nav-link :href="route('company.search')" :active="request()->routeIs('company.*')">{{ __('talenma.nav.talents') }}</x-nav-link>
                        <x-nav-link :href="route('company.profile.edit')" :active="request()->routeIs('company.profile.*')">{{ __('talenma.nav.my_company') }}</x-nav-link>
                        <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">{{ __('talenma.nav.morocco_setup') }}</x-nav-link>
                    @endif
                    <x-nav-link :href="route('home').'#publications'" :active="false">{{ __('talenma.nav.publications') }}</x-nav-link>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-3">
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
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">{{ __('talenma.nav.public_site') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('talenma.nav.my_account') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('talenma.nav.logout') }}</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            <div class="flex items-center gap-2 sm:hidden">
                <x-locale-switcher />
                <button @click="open = !open" class="p-2 text-gray-500"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            </div>
        </div>
    </div>
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t px-4 py-3 space-y-1">
        <x-responsive-nav-link :href="route('dashboard')">{{ __('talenma.nav.dashboard') }}</x-responsive-nav-link>
        @if (Auth::user()->isStaff())
            <x-responsive-nav-link :href="route('admin.users.index')">{{ __('talenma.nav.admin_users') }}</x-responsive-nav-link>
            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.publications.index')">{{ __('talenma.nav.admin_publications') }}</x-responsive-nav-link>
            @endif
        @elseif (Auth::user()->isTalent())
            <x-responsive-nav-link :href="route('profile.details.edit')">{{ __('talenma.nav.my_profile') }}</x-responsive-nav-link>
        @elseif (Auth::user()->isCompany())
            <x-responsive-nav-link :href="route('company.search')">{{ __('talenma.nav.talents') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('company.profile.edit')">{{ __('talenma.nav.my_company') }}</x-responsive-nav-link>
        @endif
        <x-responsive-nav-link :href="route('home').'#publications'">{{ __('talenma.nav.publications') }}</x-responsive-nav-link>
    </div>
</nav>
