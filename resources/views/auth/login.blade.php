<x-guest-layout :viewport-fit="! session('pending_registration_email')">
    <x-slot name="title">
        {{ session('pending_registration_email') ? __('talenma.auth.verify_email_title') : __('talenma.auth.login_title') }}
    </x-slot>

    <x-toast-stack />

    @if (session('pending_registration_email'))
        @include('auth.partials.pending-registration-verification', [
            'pendingEmail' => session('pending_registration_email'),
        ])
    @else
        <div class="flex flex-col h-full min-h-0">
            <x-auth-session-status class="mb-4 shrink-0" :status="session('status')" />
            <form method="POST" action="{{ route('login') }}" class="flex flex-col flex-1 min-h-0">@csrf
                <div>
                    <x-input-label for="email" :value="__('talenma.auth.email')" />
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        :value="old('email')"
                        required
                        x-init="if (window.matchMedia('(min-width: 640px)').matches) { $el.focus() }"
                    />
                </div>
                <div class="mt-4">
                    <x-input-label for="password" :value="__('talenma.auth.password')" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
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
        </div>
    @endif
</x-guest-layout>
