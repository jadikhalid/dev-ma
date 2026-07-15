<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.account.title') }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('talenma.account.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10">
        <x-toast-stack />
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
