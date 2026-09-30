@props([])

@php
    use App\Support\PortalHost;
@endphp

<header class="sticky top-0 z-50 w-full border-b border-indigo-100/80 bg-white/95 backdrop-blur-md">
    <div class="home-align-wide mx-auto flex h-20 w-full items-center justify-between gap-3 px-4 sm:h-16 sm:px-6 lg:px-10 xl:px-12 2xl:px-10">
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
                    x-init="$watch('open', (value) => { if (value) $nextTick(() => $refs.loginEmail.focus()) }); if (open) $nextTick(() => $refs.loginEmail.focus())"
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
                        class="absolute right-0 top-full z-50 mt-2 w-[calc(100vw-2rem)] max-w-sm origin-top rounded-2xl border border-gray-200 bg-white p-5 shadow-xl sm:w-96"
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
                            <p class="mt-4 text-center text-sm text-gray-600">
                                {{ __('talenma.auth.no_company_account') }}
                                <a
                                    href="{{ route('company.offer', ['tab' => 'trial']) }}"
                                    @if (request()->routeIs('company.offer'))
                                        @click.prevent="open = false; $dispatch('company-offer-drawer', 'trial')"
                                    @endif
                                    class="font-medium text-indigo-600"
                                >{{ __('talenma.company_offer.cta_trial') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</header>
