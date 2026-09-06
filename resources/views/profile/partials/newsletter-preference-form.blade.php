@if (Auth::user()->isApproved() && (Auth::user()->isTalent() || Auth::user()->isCompany()))
    <div id="account-newsletter-card" class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <section>
            <header>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.newsletter.preference_title') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.newsletter.preference_help') }}</p>
                <p class="mt-2 text-xs text-gray-400">{{ __('talenma.newsletter.preference_open_list_note') }}</p>
            </header>

            <form method="POST" action="{{ route('newsletter.preferences') }}" class="mt-5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="newsletter_opt_in" value="0">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="newsletter_opt_in"
                        value="1"
                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        @checked(Auth::user()->wantsNewsletter())
                    >
                    <span class="text-sm text-gray-700">{{ __('talenma.newsletter.preference_label') }}</span>
                </label>
                <div class="mt-4">
                    <x-primary-button type="submit">{{ __('talenma.newsletter.preference_save') }}</x-primary-button>
                </div>
            </form>
        </section>
    </div>
@endif
