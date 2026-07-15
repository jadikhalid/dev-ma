<x-guest-layout>
    <x-slot name="title">
        {{ session('pending_registration_email') ? __('talenma.auth.verify_email_title') : __('talenma.auth.login_title') }}
    </x-slot>

    <x-toast-stack />

    @if (session('pending_registration_email'))
        <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-5 text-sm text-indigo-900">
            <h2 class="text-base font-semibold text-indigo-950">{{ __('talenma.auth.verify_email_pending_title') }}</h2>
            <p class="mt-2 leading-relaxed">
                {{ __('talenma.auth.register_resend_hint', ['email' => session('pending_registration_email')]) }}
            </p>
            <p class="mt-2 text-indigo-800/90">{{ __('talenma.auth.verify_email_pending_no_login') }}</p>
            <form method="POST" action="{{ route('register.resend-verification') }}" class="mt-4">
                @csrf
                <input type="hidden" name="email" value="{{ session('pending_registration_email') }}">
                <x-primary-button type="submit" class="text-sm">
                    {{ __('talenma.auth.resend_registration_verification') }}
                </x-primary-button>
            </form>
        </div>
    @else
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}">@csrf
            <div>
                <x-input-label for="email" :value="__('talenma.auth.email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="password" :value="__('talenma.auth.password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <label class="flex items-center mt-4">
                <input type="checkbox" name="remember" class="rounded text-indigo-600">
                <span class="ms-2 text-sm text-gray-600">{{ __('talenma.auth.remember') }}</span>
            </label>
            <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3 items-center">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('talenma.auth.forgot') }}</a>
                @endif
                <x-primary-button class="w-full sm:w-auto justify-center">{{ __('talenma.auth.login_btn') }}</x-primary-button>
            </div>
            <p class="mt-4 text-center text-sm text-gray-600">{{ __('talenma.auth.no_account') }} <a href="{{ route('register') }}" class="text-indigo-600 font-medium">{{ __('talenma.nav.register') }}</a></p>
        </form>
    @endif
</x-guest-layout>
