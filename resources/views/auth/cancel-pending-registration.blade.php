<x-guest-layout>
    <x-slot name="title">{{ __('talenma.auth.registration_cancel_confirm_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.registration_cancel_confirm_text') }}</x-slot>

    <p class="mb-2 text-sm text-gray-600">
        {{ __('talenma.auth.registration_cancel_confirm_text') }}
    </p>
    <p class="mb-6 text-sm font-medium text-gray-900">{{ $email }}</p>

    <form method="POST" action="{{ route('register.cancel', ['token' => $token]) }}" class="space-y-3">
        @csrf
        <x-primary-button class="w-full justify-center">
            {{ __('talenma.auth.registration_cancel_confirm_btn') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('register.verify', ['token' => $token]) }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            {{ __('talenma.auth.registration_cancel_keep_btn') }}
        </a>
    </p>
</x-guest-layout>
