<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <x-brand-logo href="{{ route('home') }}" light size="sm" class="[&_span:first-child]:bg-indigo-500" />
                <p class="mt-4 text-sm text-gray-400 max-w-md">{{ __('talenma.footer.tagline') }}</p>
                <div class="mt-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">{{ __('talenma.footer.follow_us') }}</p>
                    <x-social-links variant="dark" />
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-white text-sm mb-3">{{ __('talenma.footer.platform') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-white">{{ __('talenma.footer.become_talent') }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white">{{ __('talenma.footer.recruit_free') }}</a></li>
                    <li><a href="{{ route('magazine.index') }}" class="hover:text-white">{{ __('talenma.nav.magazine') }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white text-sm mb-3">{{ __('talenma.footer.services') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services.index') }}" class="hover:text-white">{{ __('talenma.footer.morocco_setup') }}</a></li>
                    <li><a href="{{ route('services.show', 'conseil-juridique') }}" class="hover:text-white">{{ __('talenma.footer.legal') }}</a></li>
                    <li><a href="{{ route('services.show', 'ressources-humaines') }}" class="hover:text-white">{{ __('talenma.footer.hr') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-gray-800 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
            <p>{{ __('talenma.footer.tech') }}</p>
        </div>
    </div>
</footer>
