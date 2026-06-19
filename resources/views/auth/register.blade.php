<x-guest-layout>
    <x-slot name="title">{{ __('talenma.auth.register_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.register_desc') }}</x-slot>
    <form method="POST" action="{{ route('register') }}">@csrf
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="email" :value="__('talenma.auth.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password" :value="__('talenma.auth.password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
        </div>
        <div class="mt-6">
            <x-input-label :value="__('talenma.auth.register_as')" />
            <div class="mt-3 grid gap-3">
                <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="role" value="dev" class="mt-1" {{ old('role', request('role', 'dev')) === 'dev' ? 'checked' : '' }}>
                    <div>
                        <span class="font-semibold text-sm">{{ __('talenma.auth.role_talent') }}</span>
                        <p class="text-xs text-gray-500">{{ __('talenma.auth.role_talent_desc') }}</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                    <input type="radio" name="role" value="company" class="mt-1" {{ old('role', request('role')) === 'company' ? 'checked' : '' }}>
                    <div>
                        <span class="font-semibold text-sm">{{ __('talenma.auth.role_company') }}</span>
                        <p class="text-xs text-gray-500">{{ __('talenma.auth.role_company_desc') }}</p>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>
        <x-primary-button class="mt-6 w-full justify-center">{{ __('talenma.auth.register_btn') }}</x-primary-button>
        <p class="mt-4 text-center text-sm text-gray-600">{{ __('talenma.auth.has_account') }} <a href="{{ route('login') }}" class="text-indigo-600 font-medium">{{ __('talenma.auth.login_btn') }}</a></p>
    </form>
</x-guest-layout>
