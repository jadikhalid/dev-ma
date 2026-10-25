{{-- Expects: $user (talent), $profileUpdateUrl --}}
@php
    $profileUpdateUrl = $profileUpdateUrl ?? route('admin.users.profile.update', $user);
@endphp

<form
    id="admin-talent-identity-card"
    method="POST"
    action="{{ $profileUpdateUrl }}"
    class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6"
    data-ajax
    data-loading-target="admin-talent-identity-card"
    data-error-message="{{ __('talenma.common.save_error') }}"
    novalidate
>
    @csrf
    <input type="hidden" name="section" value="identity">

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.account.personal_info_title') }}</h3>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="admin_first_name" :value="__('talenma.auth.first_name')" />
            <x-text-input
                id="admin_first_name"
                name="first_name"
                type="text"
                class="mt-1 block w-full"
                :value="old('first_name', $user->first_name)"
                required
                autocomplete="given-name"
                data-required
                data-required-message="{{ __('talenma.auth.validation.first_name_required') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>
        <div>
            <x-input-label for="admin_last_name" :value="__('talenma.auth.last_name')" />
            <x-text-input
                id="admin_last_name"
                name="last_name"
                type="text"
                class="mt-1 block w-full"
                :value="old('last_name', $user->last_name)"
                required
                autocomplete="family-name"
                data-required
                data-required-message="{{ __('talenma.auth.validation.last_name_required') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
            {{ __('talenma.common.cancel') }}
        </button>
        <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
    </div>
</form>
