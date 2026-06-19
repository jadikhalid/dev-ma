<header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <x-brand-logo href="{{ route('home') }}" size="sm" />
            <div class="hidden md:flex items-center">
                <x-social-links />
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <x-locale-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">{{ __('talenma.nav.my_space') }}</a>
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
