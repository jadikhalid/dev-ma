<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col gap-6">
            <div class="flex items-start justify-between gap-4">
                <x-brand-logo light size="sm" :linked="false" />
                <div class="flex flex-col items-end gap-3 shrink-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('talenma.footer.follow_us') }}</p>
                    <x-social-links variant="dark" />
                </div>
            </div>
            <p class="text-sm text-gray-400 max-w-md">{{ __('talenma.footer.tagline') }}</p>
        </div>
        <div class="mt-10 pt-6 border-t border-gray-800 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
            <p>{{ __('talenma.footer.developed_by') }} <a href="https://www.jadi-digital.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 underline hover:text-white transition">{{ __('talenma.footer.jadi_digital') }}</a></p>
        </div>
    </div>
</footer>
