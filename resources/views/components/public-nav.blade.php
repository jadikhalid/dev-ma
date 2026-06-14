<header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <x-brand-logo href="{{ route('home') }}" size="sm" />
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 {{ request()->routeIs('home') ? 'text-indigo-600' : '' }}">{{ __('talenma.nav.home') }}</a>
                <a href="{{ route('magazine.index') }}" class="hover:text-indigo-600 {{ request()->routeIs('magazine.*') ? 'text-indigo-600' : '' }}">{{ __('talenma.nav.magazine') }}</a>
                <a href="{{ route('services.index') }}" class="hover:text-indigo-600 {{ request()->routeIs('services.*') ? 'text-indigo-600' : '' }}">{{ __('talenma.nav.morocco') }}</a>
            </nav>
            <div class="flex items-center gap-3">
                <x-locale-switcher />
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600">{{ __('talenma.nav.my_space') }}</a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('talenma.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">{{ __('talenma.nav.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
