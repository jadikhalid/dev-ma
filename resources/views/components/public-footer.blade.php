<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col gap-6">
            <div class="flex items-start justify-between gap-4">
                <x-brand-logo white size="sm" :linked="false" />
                <div class="flex flex-col items-end gap-3 shrink-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('talenma.footer.follow_us') }}</p>
                    <x-social-links variant="dark" />
                </div>
            </div>
            <p class="text-sm text-gray-400 max-w-md">{{ __('talenma.footer.tagline') }}</p>
            <div class="mt-6 max-w-md">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('talenma.newsletter.public_subscribe_title') }}</p>
                <p class="mt-1 text-sm text-gray-400">{{ __('talenma.newsletter.public_subscribe_help') }}</p>
                <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-3 flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input
                        type="email"
                        name="email"
                        required
                        maxlength="255"
                        placeholder="{{ __('talenma.newsletter.public_subscribe_placeholder') }}"
                        class="w-full rounded-lg border-gray-700 bg-gray-800 text-gray-100 text-sm placeholder:text-gray-500 focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ old('email') }}"
                    >
                    <button type="submit" class="inline-flex justify-center whitespace-nowrap rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('talenma.newsletter.public_subscribe_submit') }}
                    </button>
                </form>
                @error('email')
                    <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-10 pt-6 border-t border-gray-800 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <p>&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
                <a href="{{ route('privacy') }}" class="text-gray-400 underline hover:text-white transition">{{ __('talenma.footer.privacy') }}</a>
            </div>
            <p>{{ __('talenma.footer.developed_by') }} <a href="https://www.jadi-digital.com" target="_blank" rel="noopener noreferrer" class="text-gray-400 underline hover:text-white transition">{{ __('talenma.footer.jadi_digital') }}</a></p>
        </div>
    </div>
</footer>
