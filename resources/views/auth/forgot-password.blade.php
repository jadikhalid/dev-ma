<x-guest-layout>
    <x-slot name="title">{{ __('talenma.auth.forgot_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.forgot_desc') }}</x-slot>
    <p class="mb-4 text-sm text-gray-600">{{ __('talenma.auth.forgot_text') }}</p>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}">@csrf
        <x-input-label for="email" :value="__('talenma.auth.email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
        <x-primary-button class="mt-6 w-full justify-center">{{ __('talenma.auth.forgot_btn') }}</x-primary-button>
    </form>
</x-guest-layout>
