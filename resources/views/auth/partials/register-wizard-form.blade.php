@php
    $formClass = $formClass ?? 'flex flex-col sm:h-full sm:min-h-0';

    $registerValidationMessages = [
        'first_name_required' => __('talenma.auth.validation.first_name_required'),
        'last_name_required' => __('talenma.auth.validation.last_name_required'),
        'first_name_min' => __('talenma.auth.validation.first_name_min'),
        'last_name_min' => __('talenma.auth.validation.last_name_min'),
        'first_name_max' => __('talenma.auth.validation.first_name_max'),
        'last_name_max' => __('talenma.auth.validation.last_name_max'),
        'first_name_format' => __('talenma.auth.validation.first_name_format'),
        'last_name_format' => __('talenma.auth.validation.last_name_format'),
        'email_required' => __('talenma.auth.validation.email_required'),
        'email_invalid' => __('talenma.auth.validation.email_invalid'),
        'email_max' => __('talenma.auth.validation.email_max'),
        'email_taken' => __('talenma.auth.validation.email_taken'),
        'email_available' => __('talenma.auth.validation.email_available'),
        'email_checking' => __('talenma.auth.validation.email_checking'),
        'network_error' => __('talenma.common.network_error'),
        'password_required' => __('talenma.auth.validation.password_required'),
        'password_confirmed' => __('talenma.auth.validation.password_confirmed'),
        'password_min' => __('talenma.auth.validation.password_min'),
        'password_max' => __('talenma.auth.validation.password_max'),
        'password_letters' => __('talenma.auth.validation.password_letters'),
        'password_numbers' => __('talenma.auth.validation.password_numbers'),
        'data_processing_consent_required' => __('talenma.auth.validation.data_processing_consent_required'),
        'register_incomplete_toast' => __('talenma.auth.register_incomplete_toast'),
    ];
@endphp
    <form
        method="POST"
        action="{{ route('register') }}"
        enctype="multipart/form-data"
        novalidate
        class="{{ $formClass }}"
        @submit="onSubmit($event)"
        @keydown.enter="onEnterKey($event)"
        :aria-busy="submitting"
        x-data="registerWizard({
            lockedRole: 'dev',
            initialRole: 'dev',
            initialStep: 1,
            initialFirstName: @js(old('first_name', '')),
            initialLastName: @js(old('last_name', '')),
            initialName: @js(old('name', '')),
            initialEmail: @js(old('email', '')),
            initialContactName: '',
            initialPhone: '',
            initialSector: @js(old('sector', '')),
            initialDescription: @js(old('description', '')),
            initialCvLanguage: @js(old('cv_language', '')),
            initialHasCv: @js(false),
            initialDocumentsCount: 0,
            initialCompanyDescription: '',
            initialCompanyWebsite: '',
            initialCompanyCountry: '',
            defaultCompanyCountry: @js(\App\Models\CompanyProfile::DEFAULT_COUNTRY),
            initialDataProcessingConsent: @js((bool) old('data_processing_consent', true)),
            validationMessages: @js($registerValidationMessages),
            checkEmailUrl: @js(route('register.check-email')),
        })"
    >@csrf
        <div class="hidden" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="role" value="dev">

        <div class="-mx-1 px-1 sm:flex-1 sm:min-h-0 sm:overflow-y-auto sm:overscroll-contain">
            <div
                x-show="step === 1"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 -translate-x-4"
                class="space-y-3"
            >
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="first_name" :value="__('talenma.auth.first_name')" class="!text-base sm:!text-sm" />
                            <x-text-input id="first_name" name="first_name" x-model="firstName" @blur="onFieldBlur('first_name')" @input="onFieldInput('first_name')" x-bind:class="fieldInvalidClass('first_name')" class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!text-sm sm:!py-2" minlength="2" maxlength="127" autocomplete="given-name" required />
                            <p x-show="fieldMessage('first_name')" x-cloak class="mt-1 text-sm sm:text-xs text-red-600" x-text="fieldMessage('first_name')"></p>
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('talenma.auth.last_name')" class="!text-base sm:!text-sm" />
                            <x-text-input id="last_name" name="last_name" x-model="lastName" @blur="onFieldBlur('last_name')" @input="onFieldInput('last_name')" x-bind:class="fieldInvalidClass('last_name')" class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!text-sm sm:!py-2" minlength="2" maxlength="127" autocomplete="family-name" required />
                            <p x-show="fieldMessage('last_name')" x-cloak class="mt-1 text-sm sm:text-xs text-red-600" x-text="fieldMessage('last_name')"></p>
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('talenma.auth.email')" class="!text-base sm:!text-sm" />
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            x-model="email"
                            @blur="onEmailBlur()"
                            @input="onEmailInput()"
                            x-bind:class="fieldInvalidClass('email')"
                            class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!text-sm sm:!py-2"
                            required
                            maxlength="255"
                            autocomplete="email"
                            inputmode="email"
                        />
                        <p
                            x-show="emailStatus && !fieldMessage('email')"
                            x-cloak
                            class="mt-1 text-sm sm:text-xs"
                            :class="{
                                'text-gray-500': emailStatus === 'checking',
                                'text-emerald-700': emailStatus === 'available',
                                'text-red-600': emailStatus === 'taken' || emailStatus === 'invalid' || emailStatus === 'error',
                            }"
                            x-text="emailMessage"
                        ></p>
                        <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-sm sm:text-xs text-red-600" x-text="fieldMessage('email')"></p>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="password" :value="__('talenma.auth.password')" class="!text-base sm:!text-sm" />
                            <x-text-input id="password" name="password" type="password" x-model="password" @blur="onFieldBlur('password')" @input="onFieldInput('password')" x-bind:class="fieldInvalidClass('password')" class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!text-sm sm:!py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                            <p x-show="fieldMessage('password')" x-cloak class="mt-1 text-sm sm:text-xs text-red-600" x-text="fieldMessage('password')"></p>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" class="!text-base sm:!text-sm" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" x-model="passwordConfirmation" @blur="onFieldBlur('password_confirmation')" @input="onFieldInput('password_confirmation')" x-bind:class="fieldInvalidClass('password_confirmation')" class="mt-1.5 block w-full !text-base !py-3 sm:mt-1 sm:!text-sm sm:!py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                            <p x-show="fieldMessage('password_confirmation')" x-cloak class="mt-1 text-sm sm:text-xs text-red-600" x-text="fieldMessage('password_confirmation')"></p>
                        </div>
                    </div>

                    <div
                        class="rounded-lg border bg-gray-50 px-3 py-3"
                        :class="fieldErrors.data_processing_consent ? 'border-red-400' : 'border-gray-200'"
                    >
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input
                                id="data_processing_consent"
                                name="data_processing_consent"
                                type="checkbox"
                                value="1"
                                x-model="dataProcessingConsent"
                                @change="onFieldInput('data_processing_consent')"
                                x-bind:class="fieldInvalidClass('data_processing_consent')"
                                class="mt-0.5 size-5 sm:size-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            >
                            <span class="text-base sm:text-sm text-gray-700 leading-snug">
                                {!! __('talenma.auth.data_processing_consent', [
                                    'policy' => '<a href="'.e(route('privacy')).'" target="_blank" rel="noopener noreferrer" class="font-semibold text-indigo-600 underline decoration-indigo-300 underline-offset-2 hover:text-indigo-800">'.e(__('talenma.auth.privacy_policy')).'</a>',
                                ]) !!}
                            </span>
                        </label>
                        <p x-show="fieldMessage('data_processing_consent')" x-cloak class="mt-2 text-sm sm:text-xs text-red-600" x-text="fieldMessage('data_processing_consent')"></p>
                        <x-input-error :messages="$errors->get('data_processing_consent')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <div class="shrink-0 pt-3 mt-3 border-t border-gray-100 w-full">
            <button
                type="submit"
                x-show="showSubmit"
                x-cloak
                :disabled="!canSubmit || submitting"
                :aria-busy="submitting"
                class="relative overflow-hidden w-full inline-flex items-center justify-center px-4 py-3 sm:py-2.5 border border-transparent rounded-lg font-semibold text-base sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-80 disabled:hover:bg-indigo-600"
            >
                <span class="inline-flex items-center gap-2" :class="submitting ? 'opacity-0' : ''">
                    {{ __('talenma.auth.register_btn') }}
                </span>
                <span
                    x-show="submitting"
                    x-cloak
                    class="absolute inset-0 flex items-center justify-center gap-2 bg-indigo-600/90"
                    aria-hidden="true"
                >
                    <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-base sm:text-sm font-semibold text-white">{{ __('talenma.auth.register_submitting') }}</span>
                </span>
            </button>
        </div>

        <p class="shrink-0 mt-2 text-center text-base sm:text-sm text-gray-600">{{ __('talenma.auth.has_account') }} <a href="{{ route('login') }}" class="text-indigo-600 font-medium">{{ __('talenma.auth.login_btn') }}</a></p>
    </form>
