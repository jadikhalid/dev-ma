<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">
            {{ $user->isCompany() ? __('talenma.account.rejected_title_company') : __('talenma.account.rejected_title_talent') }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border p-8">
            <p class="text-gray-700">
                {{ $user->isCompany() ? __('talenma.account.rejected_text_company') : __('talenma.account.rejected_text_talent') }}
            </p>
            @if ($user->rejection_reason)
                <p class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-900">
                    {{ $user->rejection_reason }}
                </p>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ __('talenma.nav.logout') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
