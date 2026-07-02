<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.account.pending_title') }}</h2>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border p-8">
            <p class="text-gray-700">{{ __('talenma.account.pending_text') }}</p>
            <p class="mt-4 text-sm text-gray-500">{{ __('talenma.account.pending_email', ['email' => $user->email]) }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <x-primary-button class="w-full sm:w-auto justify-center">
                    {{ __('talenma.nav.logout') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
