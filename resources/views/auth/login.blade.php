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
                    <x-input-label for="email" :value="__('talenma.auth.email')" class="!text-base sm:!text-sm" />
                    <x-text-input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!py-2"
                        :value="old('email')"
                        required
                        autocomplete="username"
                        inputmode="email"
                        x-init="if (window.matchMedia('(min-width: 640px) and (min-height: 500px)').matches) { $el.focus() }"
                    />
                </div>
                <div class="mt-5 sm:mt-4">
                    <x-input-label for="password" :value="__('talenma.auth.password')" class="!text-base sm:!text-sm" />
                    <x-text-input id="password" name="password" type="password" class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!py-2" required autocomplete="current-password" />
                </div>
                <label class="flex items-center mt-5 sm:mt-4">
                    <input type="checkbox" name="remember" class="size-5 sm:size-4 rounded text-indigo-600" autocomplete="off">
                    <span class="ms-2.5 text-base sm:text-sm text-gray-600">{{ __('talenma.auth.remember') }}</span>
                </label>
                <div class="mt-7 sm:mt-6 flex flex-col sm:flex-row justify-between gap-3.5 sm:gap-3 items-stretch sm:items-center">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-center sm:text-left text-base sm:text-sm text-indigo-600 hover:text-indigo-800">{{ __('talenma.auth.forgot') }}</a>
                    @endif
                    <x-primary-button class="w-full sm:w-auto justify-center !text-base !py-3.5 sm:!text-sm sm:!py-2.5">{{ __('talenma.auth.login_btn') }}</x-primary-button>
                </div>
                <p class="mt-5 sm:mt-4 text-center text-base sm:text-sm text-gray-600">{{ __('talenma.auth.no_account') }} <a href="{{ route('register') }}" class="text-indigo-600 font-medium">{{ __('talenma.nav.register') }}</a></p>
            </form>
        </div>
    @endif
</x-guest-layout>
