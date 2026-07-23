<div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-5 text-sm text-indigo-900">
    <h2 class="text-base font-semibold text-indigo-950">{{ __('talenma.auth.verify_email_pending_title') }}</h2>
    <p class="mt-2 leading-relaxed">
        {{ __('talenma.auth.register_resend_hint', ['email' => $pendingEmail]) }}
    </p>
    <p class="mt-2 text-indigo-800/90">{{ __('talenma.auth.verify_email_pending_no_login') }}</p>
    <p class="mt-4 text-indigo-900 font-medium">{{ __('talenma.auth.register_resend_if_nothing') }}</p>
    <form method="POST" action="{{ route('register.resend-verification') }}" class="mt-2">
        @csrf
        <input type="hidden" name="email" value="{{ $pendingEmail }}">
        <x-primary-button type="submit" class="text-sm">
            {{ __('talenma.auth.resend_registration_verification') }}
        </x-primary-button>
    </form>
</div>
