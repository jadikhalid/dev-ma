<x-guest-layout>
    <x-slot name="title">{{ __('talenma.auth.register_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.register_desc') }}</x-slot>

    <x-toast-stack />

    <form
        method="POST"
        action="{{ route('register') }}"
        enctype="multipart/form-data"
        novalidate
        x-data="{ role: @js(old('role', $defaultRole ?? 'dev')), description: @js(old('description', '')) }"
    >@csrf
        <div class="hidden" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <div>
            <x-input-label for="name" :value="__('talenma.auth.full_name')" />
            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name')" required autofocus minlength="2" maxlength="255" autocomplete="name" />
        </div>
        <div class="mt-4">
            <x-input-label for="email" :value="__('talenma.auth.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required maxlength="255" autocomplete="email" inputmode="email" />
        </div>
        <div class="mt-4">
            <x-input-label for="password" :value="__('talenma.auth.password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required minlength="8" maxlength="128" autocomplete="new-password" />
        </div>
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required minlength="8" maxlength="128" autocomplete="new-password" />
        </div>
        <div class="mt-6">
            <x-input-label :value="__('talenma.auth.register_as')" />
            <div class="mt-3 grid gap-3">
                <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="role" value="dev" class="mt-1" x-model="role" {{ old('role', $defaultRole ?? 'dev') === 'dev' ? 'checked' : '' }} required>
                    <div>
                        <span class="font-semibold text-sm">{{ __('talenma.auth.role_talent') }}</span>
                        @if (__('talenma.auth.role_talent_desc'))
                            <p class="text-xs text-gray-500">{{ __('talenma.auth.role_talent_desc') }}</p>
                        @endif
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                    <input type="radio" name="role" value="company" class="mt-1" x-model="role" {{ old('role', $defaultRole ?? 'dev') === 'company' ? 'checked' : '' }} required>
                    <div>
                        <span class="font-semibold text-sm">{{ __('talenma.auth.role_company') }}</span>
                        @if (__('talenma.auth.role_company_desc'))
                            <p class="text-xs text-gray-500">{{ __('talenma.auth.role_company_desc') }}</p>
                        @endif
                    </div>
                </label>
            </div>
        </div>

        <div x-show="role === 'dev'" x-cloak class="mt-6 space-y-4 border-t border-gray-100 pt-6">
            <div>
                <x-input-label for="sector" :value="__('talenma.auth.sector')" />
                <select id="sector" name="sector" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" :required="role === 'dev'">
                    <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                    @foreach ($professionSectors as $sectorOption)
                        <option value="{{ $sectorOption['slug'] }}" @selected(old('sector') === $sectorOption['slug'])>{{ $sectorOption['name'] }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sector')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" :value="__('talenma.auth.registration_description')" />
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    maxlength="500"
                    x-model="description"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                    :required="role === 'dev'"
                    placeholder="{{ __('talenma.auth.registration_description_placeholder') }}"
                >{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500 text-right"><span x-text="description.length"></span>/500</p>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="documents" :value="__('talenma.auth.registration_documents')" />
                <input
                    id="documents"
                    name="documents[]"
                    type="file"
                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                    multiple
                    :required="role === 'dev'"
                >
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.auth.registration_documents_hint') }}</p>
                <x-input-error :messages="$errors->get('documents')" class="mt-2" />
                <x-input-error :messages="$errors->get('documents.*')" class="mt-2" />
            </div>
        </div>

        <x-primary-button class="mt-6 w-full justify-center">{{ __('talenma.auth.register_btn') }}</x-primary-button>
        <p class="mt-4 text-center text-sm text-gray-600">{{ __('talenma.auth.has_account') }} <a href="{{ route('login') }}" class="text-indigo-600 font-medium">{{ __('talenma.auth.login_btn') }}</a></p>
    </form>
</x-guest-layout>
