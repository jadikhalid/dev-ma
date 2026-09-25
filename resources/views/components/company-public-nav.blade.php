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
                @if ($authUser->isCompany() || $authUser->isStaff())
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
