<div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-5 text-sm text-indigo-900">
    <h2 class="text-base font-semibold text-indigo-950">{{ __('talenma.auth.verify_email_pending_title') }}</h2>
    <p class="mt-2 leading-relaxed">
        {{ __('talenma.auth.register_resend_hint_sent') }}
        <span class="mt-1 block break-all text-base font-bold text-amber-700">{{ $pendingEmail }}</span>
    </p>
    <p class="mt-2 leading-relaxed text-indigo-800/90">{{ __('talenma.auth.register_resend_hint_spam') }}</p>
    <p class="mt-2 text-indigo-800/90">{{ __('talenma.auth.verify_email_pending_no_login') }}</p>
    <p class="mt-4 text-indigo-900 font-medium">{{ __('talenma.auth.register_resend_if_nothing') }}</p>
    <form method="POST" action="{{ route('register.resend-verification') }}" class="mt-2">
        @csrf
        <input type="hidden" name="email" value="{{ $pendingEmail }}">
        <x-primary-button type="submit" class="text-sm">
            {{ __('talenma.auth.resend_registration_verification') }}
        </x-primary-button>
    </form>
    <div class="mt-5 border-t border-indigo-100 pt-4">
        <p class="text-xs font-medium text-indigo-800/90">{{ __('talenma.auth.register_wrong_email_prompt') }}</p>
        <form method="POST" action="{{ route('register.restart') }}" class="mt-1.5">
            @csrf
            <input type="hidden" name="email" value="{{ $pendingEmail }}">
            <button
                type="submit"
                class="text-xs font-semibold text-amber-700 underline decoration-1 underline-offset-2 hover:text-amber-800"
            >
                {{ __('talenma.auth.register_restart_with_other_email') }}
            </button>
        </form>
    </div>
</div>
