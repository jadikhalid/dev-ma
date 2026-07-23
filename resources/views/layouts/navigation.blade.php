@php
    $authUser = Auth::user();
    $pendingAccount = $authUser->isPendingApproval();
    $companyOrg = $authUser->isCompany() ? $authUser->companyOrganization() : null;
    $catalogBlocked = $authUser->isCompany()
        && ! app(\App\Services\CompanyProfileCompletionService::class)
            ->assess($companyOrg)['is_catalog_ready'];
    $isCompanyOwner = $authUser->isCompanyOwner();
    $inboxUnread = (! $pendingAccount && ($authUser->isCompany() || $authUser->isTalent() || $authUser->isStaff()))
        ? app(\App\Services\MessagingService::class)->unreadCountFor($authUser)
        : 0;
@endphp

<nav x-data="{ open: false }" class="relative bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                <x-brand-logo :href="route('home')" size="sm" />
                <div class="hidden lg:flex items-center gap-1 min-w-0">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" :disabled="$pendingAccount">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 8.25 20.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            {{ __('talenma.nav.dashboard') }}
                        </span>
                    </x-nav-link>
                    @if ($authUser->isStaff())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('talenma.nav.admin_users') }}</x-nav-link>
                        @if ($authUser->isAdmin())
                            <x-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')">{{ __('talenma.nav.admin_publications') }}</x-nav-link>
                        @endif
                        <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-nav-link>
                    @elseif ($authUser->isTalent())
                        <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')" :disabled="$pendingAccount">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-nav-link>
                        <x-nav-link :href="route('talent.jobs.index')" :active="request()->routeIs('talent.jobs.*')" :disabled="$pendingAccount">{{ __('talenma.nav.jobs') }}</x-nav-link>
                        <x-nav-link :href="route('profile.details.edit')" :active="request()->routeIs('profile.details.*')" :disabled="$pendingAccount">{{ __('talenma.nav.my_profile') }}</x-nav-link>
                    @elseif ($authUser->isCompany())
                        <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')" :disabled="$pendingAccount || $catalogBlocked">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-nav-link>
                    @endif
                </div>
            </div>
            <div class="hidden lg:flex items-center gap-3 shrink-0">
                <x-locale-switcher />
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $authUser->isAdmin() ? 'bg-violet-100 text-violet-700' : ($authUser->isModerator() ? 'bg-purple-100 text-purple-700' : ($authUser->isTalent() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700')) }}">
                    @if ($authUser->isAdmin())
                        {{ __('talenma.roles.admin') }}
                    @elseif ($authUser->isModerator())
                        {{ __('talenma.roles.moderator') }}
                    @elseif ($authUser->isTalent())
                        {{ __('talenma.roles.talent') }}
                    @elseif ($authUser->isCompanyOwner())
                        {{ __('talenma.roles.company_owner') }}
                    @elseif ($authUser->isCompanyMember())
                        {{ __('talenma.roles.company_member') }}
                    @else
                        {{ __('talenma.roles.company') }}
                    @endif
                </span>
                <x-dropdown align="right" width="48" :open-on-hover="true">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                            @if ($authUser->isCompany())
                                <x-company-logo :profile="$companyOrg" size="xs" class="ring-1 ring-gray-200" />
                            @else
                                <x-user-avatar :user="$authUser" size="xs" class="ring-1 ring-gray-200" />
                            @endif
                            <span class="hidden xl:inline">{{ $authUser->isCompany() ? $authUser->companyDisplayName() : $authUser->name }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('home')">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                                {{ __('talenma.nav.home') }}
                            </span>
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit', $authUser->canManageCompanyProfile() ? ['panel' => 'account'] : [])">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995s.145.755.438.995l1.003.827c.424.35.534.954.26 1.431l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.437-.995s-.145-.755-.437-.995l-1.004-.827a1.125 1.125 0 0 1-.26-1.431l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                {{ __('talenma.nav.settings') }}
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
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 8.25 20.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            {{ __('talenma.nav.dashboard') }}
                        </span>
                    </x-responsive-nav-link>
                    @if ($authUser->isStaff())
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('talenma.nav.admin_users') }}</x-responsive-nav-link>
                        @if ($authUser->isAdmin())
                            <x-responsive-nav-link :href="route('admin.publications.index')" :active="request()->routeIs('admin.publications.*')">{{ __('talenma.nav.admin_publications') }}</x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-responsive-nav-link>
                    @elseif ($authUser->isTalent())
                        <x-responsive-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('talent.jobs.index')" :active="request()->routeIs('talent.jobs.*')">{{ __('talenma.nav.jobs') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('profile.details.edit')" :active="request()->routeIs('profile.details.*')">{{ __('talenma.nav.my_profile') }}</x-responsive-nav-link>
                    @elseif ($authUser->isCompany())
                        <x-responsive-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*')" :disabled="$catalogBlocked">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                {{ __('talenma.nav.messages') }}
                                @if ($inboxUnread > 0)
                                    <span class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $inboxUnread > 99 ? '99+' : $inboxUnread }}</span>
                                @endif
                            </span>
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endunless
            <x-dropdown-link :href="route('home')">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                    {{ __('talenma.nav.home') }}
                </span>
            </x-dropdown-link>
            <x-dropdown-link :href="route('profile.edit', $authUser->canManageCompanyProfile() ? ['panel' => 'account'] : [])">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995s.145.755.438.995l1.003.827c.424.35.534.954.26 1.431l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.437-.995s-.145-.755-.437-.995l-1.004-.827a1.125 1.125 0 0 1-.26-1.431l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    {{ __('talenma.nav.settings') }}
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
        </div>
    </div>
</nav>
