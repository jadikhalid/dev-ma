<x-guest-layout>
    <x-slot name="title">{{ __('talenma.auth.verify_email_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.verify_email_desc') }}</x-slot>

    <x-toast-stack />

    <p class="mb-4 text-sm text-gray-600">
        {{ __('talenma.auth.verify_email_text') }}
    </p>

    <div @class([
        'rounded-xl border p-4',
        'border-red-200 bg-red-50' => session('toast_error'),
        'border-gray-200 bg-gray-50' => ! session('toast_error'),
    ])>
        <p @class([
            'text-sm font-medium mb-3',
            'text-red-900' => session('toast_error'),
            'text-gray-700' => ! session('toast_error'),
        ])>
            {{ session('toast_error') ? __('talenma.auth.verify_email_resend_prompt') : __('talenma.auth.verify_email_resend_hint') }}
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>{{ __('talenma.auth.verify_email_resend_btn') }}</x-primary-button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
            {{ __('talenma.nav.logout') }}
        </button>
    </form>
</x-guest-layout>
