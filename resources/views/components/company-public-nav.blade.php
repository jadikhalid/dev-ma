@props([])

@php
    use App\Support\PortalHost;
@endphp

<header class="sticky top-0 z-50 w-full border-b border-indigo-100/80 bg-white/95 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-5xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
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
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100"
                >{{ __('talenma.nav.company_login') }}</a>
            @endauth
        </div>
    </div>
</header>
