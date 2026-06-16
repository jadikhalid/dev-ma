<header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <x-brand-logo href="{{ route('home') }}" size="sm" />
            <div class="hidden md:flex items-center">
                <x-social-links />
            </div>
            <div class="flex items-center gap-3">
                <x-locale-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">{{ __('talenma.nav.my_space') }}</a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-indigo-600 rounded-lg border border-indigo-200/80 bg-indigo-50/60 hover:bg-indigo-100 hover:border-indigo-300 hover:text-indigo-700 hover:shadow-sm transition-all duration-300 ease-in-out">{{ __('talenma.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-300 ease-in-out">{{ __('talenma.nav.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
