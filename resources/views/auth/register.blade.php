@php
    $talentStep = 1;

    if (old('role') === 'dev' && (
        $errors->has('sector')
        || $errors->has('description')
        || $errors->has('documents')
        || $errors->has('documents.*')
    )) {
        $talentStep = 2;
    }
@endphp

<x-guest-layout viewport-fit>
    <x-slot name="title">{{ __('talenma.auth.register_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.register_desc') }}</x-slot>

    <x-toast-stack />

    <form
        method="POST"
        action="{{ route('register') }}"
        enctype="multipart/form-data"
        novalidate
        class="flex flex-col h-full min-h-0"
        @submit="onSubmit($event)"
        x-data="registerWizard({
            initialRole: @js(old('role', $defaultRole ?? '')),
            initialStep: @js($talentStep),
            initialFirstName: @js(old('first_name', '')),
            initialLastName: @js(old('last_name', '')),
            initialName: @js(old('name', '')),
            initialEmail: @js(old('email', '')),
            initialSector: @js(old('sector', '')),
            initialDescription: @js(old('description', '')),
            initialDocumentsCount: @js(is_array(old('documents')) ? count(old('documents')) : 0),
            initialRepresentativeName: @js(old('representative_name', '')),
            initialRepresentativeEmail: @js(old('representative_email', '')),
            initialCompanyNeed: @js(old('company_need', '')),
        })"
    >@csrf
        <div class="hidden" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        {{-- Indicateur d'étapes (talent uniquement) --}}
        <div
            x-show="isTalent"
            x-cloak
            class="shrink-0 mb-3"
            aria-live="polite"
        >
            <div class="flex items-center justify-between text-[11px] sm:text-xs font-medium text-gray-500 mb-1.5">
                <span :class="step === 1 ? 'text-indigo-600' : ''">{{ __('talenma.auth.register_step_1_label') }}</span>
                <span :class="step === 2 ? 'text-indigo-600' : ''">{{ __('talenma.auth.register_step_2_label') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors"
                    :class="step === 1 ? 'bg-indigo-600 text-white' : (step1Valid ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-400')"
                >1</span>
                <div class="flex-1 h-1 rounded-full bg-gray-200 overflow-hidden">
                    <div class="h-full bg-indigo-600 transition-all duration-300" :style="`width: ${step === 2 ? '100%' : '0%'}`"></div>
                </div>
                <span
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors"
                    :class="step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400'"
                >2</span>
            </div>
            <p class="mt-1.5 text-[11px] sm:text-xs text-gray-500 line-clamp-2" x-text="step === 1 ? @js(__('talenma.auth.register_step_1_hint')) : @js(__('talenma.auth.register_step_2_hint'))"></p>
        </div>

        {{-- Zone scrollable : champs du formulaire --}}
        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain -mx-1 px-1">
            {{-- Étape 1 : identité + rôle --}}
            <div
                x-show="!isTalent || step === 1"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-4"
                class="space-y-3"
            >
                <div x-show="isCompany" x-cloak>
                    <label for="name" class="block font-medium text-xs sm:text-sm text-gray-700">
                        {{ __('talenma.auth.company_name') }}
                    </label>
                    <x-text-input id="name" name="name" x-model="name" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="255" autocomplete="organization" x-bind:required="isCompany" x-bind:disabled="!isCompany" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div x-show="!isCompany" class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="first_name" :value="__('talenma.auth.first_name')" class="text-xs sm:text-sm" />
                        <x-text-input id="first_name" name="first_name" x-model="firstName" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="127" autocomplete="given-name" x-bind:required="isTalent" x-bind:disabled="isCompany" x-bind:autofocus="!isCompany" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('talenma.auth.last_name')" class="text-xs sm:text-sm" />
                        <x-text-input id="last_name" name="last_name" x-model="lastName" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="127" autocomplete="family-name" x-bind:required="isTalent" x-bind:disabled="isCompany" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label for="email" :value="__('talenma.auth.email')" class="text-xs sm:text-sm" />
                    <x-text-input id="email" name="email" type="email" x-model="email" class="mt-1 block w-full text-sm py-2" required maxlength="255" autocomplete="email" inputmode="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="password" :value="__('talenma.auth.password')" class="text-xs sm:text-sm" />
                        <x-text-input id="password" name="password" type="password" @input="password = $event.target.value" class="mt-1 block w-full text-sm py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" class="text-xs sm:text-sm" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" @input="passwordConfirmation = $event.target.value" class="mt-1 block w-full text-sm py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                    </div>
                </div>
                <div class="pt-1">
                    <x-input-label :value="__('talenma.auth.register_as')" class="text-xs sm:text-sm" />
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <label class="flex items-start gap-2 p-2.5 sm:gap-2.5 sm:p-3 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="role" value="dev" class="mt-0.5 shrink-0" x-model="role" required>
                            <div class="min-w-0">
                                <span class="font-semibold text-xs sm:text-sm">{{ __('talenma.auth.role_talent') }}</span>
                                @if (__('talenma.auth.role_talent_desc'))
                                    <p class="text-[10px] sm:text-xs text-gray-500 line-clamp-2">{{ __('talenma.auth.role_talent_desc') }}</p>
                                @endif
                            </div>
                        </label>
                        <label class="flex items-start gap-2 p-2.5 sm:gap-2.5 sm:p-3 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50">
                            <input type="radio" name="role" value="company" class="mt-0.5 shrink-0" x-model="role" required>
                            <div class="min-w-0">
                                <span class="font-semibold text-xs sm:text-sm">{{ __('talenma.auth.role_company') }}</span>
                                @if (__('talenma.auth.role_company_desc'))
                                    <p class="text-[10px] sm:text-xs text-gray-500 line-clamp-2">{{ __('talenma.auth.role_company_desc') }}</p>
                                @endif
                            </div>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>

                {{-- Champs entreprise --}}
                <div
                    x-show="isCompany"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-3 pt-1 border-t border-gray-100"
                >
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="representative_name" :value="__('talenma.auth.representative_name')" class="text-xs sm:text-sm" />
                            <x-text-input id="representative_name" name="representative_name" x-model="representativeName" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="255" autocomplete="name" />
                            <x-input-error :messages="$errors->get('representative_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="representative_email" :value="__('talenma.auth.representative_email')" class="text-xs sm:text-sm" />
                            <x-text-input id="representative_email" name="representative_email" type="email" x-model="representativeEmail" class="mt-1 block w-full text-sm py-2" maxlength="255" autocomplete="work email" inputmode="email" />
                            <x-input-error :messages="$errors->get('representative_email')" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="company_need" :value="__('talenma.auth.company_need')" class="text-xs sm:text-sm" />
                        <textarea
                            id="company_need"
                            name="company_need"
                            rows="2"
                            maxlength="1000"
                            x-model="companyNeed"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm resize-none"
                            placeholder="{{ __('talenma.auth.company_need_placeholder') }}"
                        >{{ old('company_need') }}</textarea>
                        <p class="mt-0.5 text-[11px] text-gray-500 text-right"><span x-text="companyNeed.length"></span>/1000</p>
                        <x-input-error :messages="$errors->get('company_need')" class="mt-1" />
                    </div>
                </div>
            </div>

            {{-- Étape 2 : profil talent --}}
            <div
                x-show="isTalent && step === 2"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-4"
                class="space-y-3"
            >
                <div>
                    <x-input-label for="sector" :value="__('talenma.auth.sector')" class="text-xs sm:text-sm" />
                    <select id="sector" name="sector" x-model="sector" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm py-2">
                        <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                        @foreach ($professionSectors as $sectorOption)
                            <option value="{{ $sectorOption['slug'] }}">{{ $sectorOption['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sector')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="description" :value="__('talenma.auth.registration_description')" class="text-xs sm:text-sm" />
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="500"
                        x-model="description"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm resize-none"
                        placeholder="{{ __('talenma.auth.registration_description_placeholder') }}"
                    >{{ old('description') }}</textarea>
                    <p class="mt-0.5 text-[11px] text-gray-500 text-right"><span x-text="description.length"></span>/500</p>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="documents" :value="__('talenma.auth.registration_documents')" class="text-xs sm:text-sm" />
                    <input
                        id="documents"
                        name="documents[]"
                        type="file"
                        @change="onDocumentsChange($event)"
                        class="mt-1 block w-full text-xs sm:text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                        multiple
                    >
                    <p class="mt-0.5 text-[11px] sm:text-xs text-gray-500">{{ __('talenma.auth.registration_documents_hint') }}</p>
                    <x-input-error :messages="$errors->get('documents')" class="mt-1" />
                    <x-input-error :messages="$errors->get('documents.*')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Navigation fixe en bas --}}
        <div class="shrink-0 pt-3 mt-3 border-t border-gray-100 flex items-center gap-2 sm:gap-3">
            <button
                type="button"
                @click="prev()"
                :disabled="!canGoBack"
                :class="canGoBack
                    ? 'inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition'
                    : 'inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50'"
                aria-label="{{ __('talenma.auth.register_back') }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span class="hidden sm:inline">{{ __('talenma.auth.register_back') }}</span>
            </button>

            <div class="flex-1 flex justify-center min-w-0">
                <button
                    type="button"
                    x-show="isTalent && step === 1"
                    x-cloak
                    @click="next()"
                    :disabled="!canGoNext"
                    :class="canGoNext
                        ? 'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm'
                        : 'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-200 rounded-lg cursor-not-allowed'"
                >
                    {{ __('talenma.auth.register_continue') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <button
                type="submit"
                x-show="showSubmit"
                x-cloak
                :disabled="!canSubmit"
                :class="canSubmit
                    ? 'inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ml-auto shrink-0'
                    : 'inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-400 cursor-not-allowed ml-auto shrink-0'"
            >
                {{ __('talenma.auth.register_btn') }}
            </button>

            <button
                type="button"
                x-show="!showSubmit"
                x-cloak
                disabled
                class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-400 cursor-not-allowed ml-auto shrink-0"
            >
                {{ __('talenma.auth.register_btn') }}
            </button>
        </div>

        <p class="shrink-0 mt-2 text-center text-xs sm:text-sm text-gray-600">{{ __('talenma.auth.has_account') }} <a href="{{ route('login') }}" class="text-indigo-600 font-medium">{{ __('talenma.auth.login_btn') }}</a></p>
    </form>
</x-guest-layout>
