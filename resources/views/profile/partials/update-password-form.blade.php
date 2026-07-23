<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.account.password_title') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('talenma.account.password_desc') }}</p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="relative mt-6 space-y-6"
        data-ajax
        data-ajax-clear
        data-loading-target="account-password-card"
        data-error-message="{{ __('talenma.common.save_error') }}"
        novalidate
    >
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('talenma.account.current_password')" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                data-required
                data-required-message="{{ __('talenma.account.current_password') }}"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('talenma.account.new_password')" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                data-required
                data-required-message="{{ __('talenma.account.new_password') }}"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('talenma.account.confirm_password')" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                data-required
                data-required-message="{{ __('talenma.account.confirm_password') }}"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
            <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">
                {{ __('talenma.common.cancel') }}
            </button>
            <x-primary-button class="justify-center">{{ __('talenma.common.save') }}</x-primary-button>
        </div>
    </form>
</section>
