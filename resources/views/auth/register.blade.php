@php
    $initialStep = 1;

    if (old('role') === 'dev' && (
        $errors->has('sector')
        || $errors->has('description')
        || $errors->has('documents')
        || $errors->has('documents.*')
    )) {
        $initialStep = 2;
    }

    if (old('role') === 'company') {
        if (
            $errors->has('representative_name')
            || $errors->has('sector')
            || $errors->has('company_need')
            || $errors->has('company_website')
            || $errors->has('company_country')
        ) {
            $initialStep = 2;
        }

        if ($errors->has('documents') || $errors->has('documents.*')) {
            $initialStep = 3;
        }
    }
@endphp

@php
    $registerValidationMessages = [
        'name_required' => __('talenma.auth.validation.name_required'),
        'first_name_required' => __('talenma.auth.validation.first_name_required'),
        'last_name_required' => __('talenma.auth.validation.last_name_required'),
        'first_name_min' => __('talenma.auth.validation.first_name_min'),
        'last_name_min' => __('talenma.auth.validation.last_name_min'),
        'first_name_max' => __('talenma.auth.validation.first_name_max'),
        'last_name_max' => __('talenma.auth.validation.last_name_max'),
        'first_name_format' => __('talenma.auth.validation.first_name_format'),
        'last_name_format' => __('talenma.auth.validation.last_name_format'),
        'name_min' => __('talenma.auth.validation.name_min'),
        'name_max' => __('talenma.auth.validation.name_max'),
        'name_format' => __('talenma.auth.validation.name_format'),
        'email_required' => __('talenma.auth.validation.email_required'),
        'email_invalid' => __('talenma.auth.validation.email_invalid'),
        'email_max' => __('talenma.auth.validation.email_max'),
        'password_required' => __('talenma.auth.validation.password_required'),
        'password_confirmed' => __('talenma.auth.validation.password_confirmed'),
        'password_min' => __('talenma.auth.validation.password_min'),
        'password_max' => __('talenma.auth.validation.password_max'),
        'password_letters' => __('talenma.auth.validation.password_letters'),
        'password_numbers' => __('talenma.auth.validation.password_numbers'),
        'sector_required' => __('talenma.auth.validation.sector_required'),
        'description_required' => __('talenma.auth.validation.description_required'),
        'description_min' => __('talenma.auth.validation.description_min'),
        'description_max' => __('talenma.auth.validation.description_max'),
        'documents_required' => __('talenma.auth.validation.documents_required'),
        'documents_max' => __('talenma.auth.validation.documents_max'),
        'documents_max_company' => __('talenma.auth.validation.documents_max_company'),
        'documents_size' => __('talenma.auth.validation.documents_size'),
        'documents_type' => __('talenma.auth.validation.documents_type'),
        'representative_name_required' => __('talenma.auth.validation.representative_name_required'),
        'representative_name_min' => __('talenma.auth.validation.representative_name_min'),
        'representative_name_max' => __('talenma.auth.validation.representative_name_max'),
        'representative_name_format' => __('talenma.auth.validation.representative_name_format'),
        'company_need_required' => __('talenma.auth.validation.company_need_required'),
        'company_need_min' => __('talenma.auth.validation.company_need_min'),
        'company_need_max' => __('talenma.auth.validation.company_need_max'),
        'company_website_invalid' => __('talenma.auth.validation.company_website_invalid'),
    ];
@endphp

<x-guest-layout viewport-fit>
    <x-slot name="title">{{ __('talenma.auth.register_title') }}</x-slot>
    <x-slot name="description">{{ __('talenma.auth.register_desc') }}</x-slot>

    <x-toast-stack persistent />

    <form
        method="POST"
        action="{{ route('register') }}"
        enctype="multipart/form-data"
        novalidate
        class="flex flex-col h-full min-h-0"
        @submit="onSubmit($event)"
        x-data="registerWizard({
            initialRole: @js(old('role', $defaultRole ?? '')),
            initialStep: @js($initialStep),
            initialFirstName: @js(old('first_name', '')),
            initialLastName: @js(old('last_name', '')),
            initialName: @js(old('name', '')),
            initialEmail: @js(old('email', '')),
            initialSector: @js(old('sector', '')),
            initialDescription: @js(old('description', '')),
            initialDocumentsCount: @js(is_array(old('documents')) ? count(old('documents')) : 0),
            initialRepresentativeName: @js(old('representative_name', '')),
            initialCompanyNeed: @js(old('company_need', '')),
            initialCompanyWebsite: @js(old('company_website', '')),
            initialCompanyCountry: @js(old('company_country', \App\Models\CompanyProfile::DEFAULT_COUNTRY)),
            defaultCompanyCountry: @js(\App\Models\CompanyProfile::DEFAULT_COUNTRY),
            validationMessages: @js($registerValidationMessages),
        })"
    >@csrf
        <div class="hidden" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        {{-- Indicateur d'étapes --}}
        <div
            x-show="navEnabled"
            x-cloak
            class="shrink-0 mb-3"
            aria-live="polite"
        >
            {{-- Talent : 2 étapes --}}
            <template x-if="isTalent">
                <div>
                    <div class="flex items-center justify-between text-[11px] sm:text-xs font-medium text-gray-500 mb-1.5">
                        <span :class="step === 1 ? 'text-indigo-600' : ''">{{ __('talenma.auth.register_step_1_label') }}</span>
                        <span :class="step === 2 ? 'text-indigo-600' : ''">{{ __('talenma.auth.register_step_2_label') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors" :class="step === 1 ? 'bg-indigo-600 text-white' : (step1Valid ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-400')">1</span>
                        <div class="flex-1 h-1 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-full bg-indigo-600 transition-all duration-300" :style="`width: ${step === 2 ? '100%' : '0%'}`"></div>
                        </div>
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors" :class="step === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400'">2</span>
                    </div>
                    <p class="mt-1.5 text-[11px] sm:text-xs text-gray-500 line-clamp-2" x-text="step === 1 ? @js(__('talenma.auth.register_step_1_hint')) : @js(__('talenma.auth.register_step_2_hint'))"></p>
                </div>
            </template>

            {{-- Entreprise : 3 étapes --}}
            <template x-if="isCompany">
                <div>
                    <div class="flex items-center justify-between text-[11px] sm:text-xs font-medium text-gray-500 mb-1.5">
                        <span :class="step === 1 ? 'text-emerald-600' : ''">{{ __('talenma.auth.register_step_1_label') }}</span>
                        <span :class="step === 2 ? 'text-emerald-600' : ''">{{ __('talenma.auth.register_company_step_2_label') }}</span>
                        <span :class="step === 3 ? 'text-emerald-600' : ''">{{ __('talenma.auth.register_company_step_3_label') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors" :class="step === 1 ? 'bg-emerald-600 text-white' : (step1Valid ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400')">1</span>
                        <div class="flex-1 h-1 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-full bg-emerald-600 transition-all duration-300" :style="`width: ${step >= 2 ? '100%' : '0%'}`"></div>
                        </div>
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors" :class="step === 2 ? 'bg-emerald-600 text-white' : (step > 2 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400')">2</span>
                        <div class="flex-1 h-1 rounded-full bg-gray-200 overflow-hidden">
                            <div class="h-full bg-emerald-600 transition-all duration-300" :style="`width: ${step === 3 ? '100%' : '0%'}`"></div>
                        </div>
                        <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-colors" :class="step === 3 ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-400'">3</span>
                    </div>
                    <p class="mt-1.5 text-[11px] sm:text-xs text-gray-500 line-clamp-2" x-text="step === 1 ? @js(__('talenma.auth.register_step_1_hint')) : (step === 2 ? @js(__('talenma.auth.register_company_step_2_hint')) : @js(__('talenma.auth.register_company_step_3_hint')))"></p>
                </div>
            </template>
        </div>

        {{-- Profil choisi + lien discret pour revenir au choix (toutes les étapes) --}}
        <div x-show="hasRole" x-cloak class="shrink-0 mb-3 flex items-center justify-between gap-2">
            <span
                class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold"
                :class="isCompany ? 'text-emerald-700' : 'text-indigo-700'"
            >
                <span class="w-1.5 h-1.5 rounded-full" :class="isCompany ? 'bg-emerald-500' : 'bg-indigo-500'"></span>
                <span x-text="isCompany ? @js(__('talenma.auth.role_company')) : @js(__('talenma.auth.role_talent'))"></span>
            </span>
            <button
                type="button"
                @click="resetRole()"
                class="text-xs sm:text-sm font-semibold underline underline-offset-2 transition-colors"
                :class="isCompany ? 'text-emerald-700 hover:text-emerald-900' : 'text-indigo-700 hover:text-indigo-900'"
            >
                {{ __('talenma.auth.register_change_role') }}
            </button>
        </div>

        {{-- Zone scrollable : champs du formulaire --}}
        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain -mx-1 px-1">
            {{-- Étape 1 : identité + rôle --}}
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
                {{-- Choix du profil : affiché tant qu'aucun rôle n'est sélectionné --}}
                <div x-show="!hasRole" x-cloak>
                    <x-input-label :value="__('talenma.auth.register_as')" class="text-xs sm:text-sm" />
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <label class="group flex flex-col items-center text-center gap-2 p-3 sm:p-4 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 hover:border-indigo-300">
                            <input type="radio" name="role" value="dev" class="sr-only" x-model="role" required>
                            <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-indigo-100 text-indigo-600 group-has-[:checked]:bg-indigo-600 group-has-[:checked]:text-white transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <span class="font-semibold text-xs sm:text-sm">{{ __('talenma.auth.role_talent') }}</span>
                            @if (__('talenma.auth.role_talent_desc'))
                                <p class="text-[10px] sm:text-xs text-gray-500 line-clamp-2">{{ __('talenma.auth.role_talent_desc') }}</p>
                            @endif
                        </label>
                        <label class="group flex flex-col items-center text-center gap-2 p-3 sm:p-4 border-2 rounded-xl cursor-pointer transition-colors has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50 hover:border-emerald-300">
                            <input type="radio" name="role" value="company" class="sr-only" x-model="role" required>
                            <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-emerald-100 text-emerald-600 group-has-[:checked]:bg-emerald-600 group-has-[:checked]:text-white transition-colors">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-14h.01M9 11h.01M9 15h.01M15 7h.01M15 11h.01M15 15h.01"/></svg>
                            </span>
                            <span class="font-semibold text-xs sm:text-sm">{{ __('talenma.auth.role_company') }}</span>
                            @if (__('talenma.auth.role_company_desc'))
                                <p class="text-[10px] sm:text-xs text-gray-500 line-clamp-2">{{ __('talenma.auth.role_company_desc') }}</p>
                            @endif
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    <p class="mt-3 text-center text-[11px] sm:text-xs text-gray-500">
                        {{ __('talenma.auth.register_choose_hint') }}
                    </p>
                </div>

                {{-- Champs d'identité : révélés une fois le profil choisi --}}
                <div
                    x-show="hasRole"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-3"
                >
                    <div x-show="isCompany" x-cloak>
                        <label for="name" class="block font-medium text-xs sm:text-sm text-gray-700">
                            {{ __('talenma.auth.company_name') }}
                        </label>
                        <x-text-input id="name" name="name" x-model="name" @blur="onFieldBlur('name')" @input="onFieldInput('name')" x-bind:class="fieldInvalidClass('name')" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="255" autocomplete="organization" x-bind:required="isCompany" x-bind:disabled="!isCompany" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div x-show="!isCompany" class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="first_name" :value="__('talenma.auth.first_name')" class="text-xs sm:text-sm" />
                            <x-text-input id="first_name" name="first_name" x-model="firstName" @blur="onFieldBlur('first_name')" @input="onFieldInput('first_name')" x-bind:class="fieldInvalidClass('first_name')" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="127" autocomplete="given-name" x-bind:required="isTalent" x-bind:disabled="isCompany" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('talenma.auth.last_name')" class="text-xs sm:text-sm" />
                            <x-text-input id="last_name" name="last_name" x-model="lastName" @blur="onFieldBlur('last_name')" @input="onFieldInput('last_name')" x-bind:class="fieldInvalidClass('last_name')" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="127" autocomplete="family-name" x-bind:required="isTalent" x-bind:disabled="isCompany" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('talenma.auth.email')" class="text-xs sm:text-sm" />
                        <x-text-input id="email" name="email" type="email" x-model="email" @blur="onFieldBlur('email')" @input="onFieldInput('email')" x-bind:class="fieldInvalidClass('email')" class="mt-1 block w-full text-sm py-2" required maxlength="255" autocomplete="email" inputmode="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="password" :value="__('talenma.auth.password')" class="text-xs sm:text-sm" />
                            <x-text-input id="password" name="password" type="password" x-model="password" @blur="onFieldBlur('password')" @input="onFieldInput('password')" x-bind:class="fieldInvalidClass('password')" class="mt-1 block w-full text-sm py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('talenma.auth.confirm_password')" class="text-xs sm:text-sm" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" x-model="passwordConfirmation" @blur="onFieldBlur('password_confirmation')" @input="onFieldInput('password_confirmation')" x-bind:class="fieldInvalidClass('password_confirmation')" class="mt-1 block w-full text-sm py-2" required minlength="8" maxlength="128" autocomplete="new-password" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Étape 2 entreprise : contact & besoin --}}
            <div
                x-show="isCompany && step === 2"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-3"
            >
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <x-input-label for="representative_name" :value="__('talenma.auth.representative_name')" class="text-xs sm:text-sm" />
                        <x-text-input id="representative_name" name="representative_name" x-model="representativeName" @blur="onFieldBlur('representative_name')" @input="onFieldInput('representative_name')" x-bind:class="fieldInvalidClass('representative_name')" class="mt-1 block w-full text-sm py-2" minlength="2" maxlength="255" autocomplete="name" />
                        <x-input-error :messages="$errors->get('representative_name')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label for="company_sector" :value="__('talenma.auth.sector')" class="text-xs sm:text-sm" />
                    <select id="company_sector" name="sector" x-model="sector" @blur="onFieldBlur('sector')" @change="onFieldInput('sector')" x-bind:class="fieldInvalidClass('sector')" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm py-2">
                        <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                        @foreach ($professionSectors as $sectorOption)
                            <option value="{{ $sectorOption['slug'] }}">{{ $sectorOption['name'] }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sector')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="company_need" :value="__('talenma.auth.company_need')" class="text-xs sm:text-sm" />
                    <textarea
                        id="company_need"
                        name="company_need"
                        rows="3"
                        maxlength="1000"
                        x-model="companyNeed"
                        @blur="onFieldBlur('company_need')"
                        @input="onFieldInput('company_need')"
                        x-bind:class="fieldInvalidClass('company_need')"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm resize-none"
                        placeholder="{{ __('talenma.auth.company_need_placeholder') }}"
                    >{{ old('company_need') }}</textarea>
                    <p class="mt-0.5 text-[11px] text-gray-500 text-right"><span x-text="companyNeed.length"></span>/1000</p>
                    <x-input-error :messages="$errors->get('company_need')" class="mt-1" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="company_website" :value="__('talenma.auth.company_website')" class="text-xs sm:text-sm" />
                        <x-text-input id="company_website" name="company_website" type="url" x-model="companyWebsite" @blur="onFieldBlur('company_website')" @input="onFieldInput('company_website')" x-bind:class="fieldInvalidClass('company_website')" class="mt-1 block w-full text-sm py-2" placeholder="https://..." />
                        <x-input-error :messages="$errors->get('company_website')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="company_country" :value="__('talenma.auth.company_country')" class="text-xs sm:text-sm" />
                        <select
                            id="company_country"
                            name="company_country"
                            x-model="companyCountry"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm py-2"
                        >
                            <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                            @foreach ($companyCountryOptions as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('company_country')" class="mt-1" />
                    </div>
                </div>
            </div>

            {{-- Étape 3 entreprise : justificatifs --}}
            <div
                x-show="isCompany && step === 3"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4"
                x-transition:enter-end="opacity-100 translate-x-0"
                class="space-y-3"
            >
                <div>
                    <x-input-label for="company_documents" :value="__('talenma.auth.company_registration_documents')" class="text-xs sm:text-sm" />
                    <input
                        id="company_documents"
                        x-ref="companyDocuments"
                        name="documents[]"
                        type="file"
                        x-bind:disabled="!isCompany"
                        @change="onDocumentsChange($event)"
                        @blur="onFieldBlur('documents')"
                        x-bind:class="fieldInvalidClass('documents')"
                        class="mt-1 block w-full text-xs sm:text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                        accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                        multiple
                    >
                    <ul x-show="documentFiles.length > 0" class="mt-2 space-y-1.5" x-cloak>
                        <template x-for="(file, index) in documentFiles" :key="documentFileKey(file)">
                            <li class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-700">
                                <span class="min-w-0 truncate" x-text="file.name"></span>
                                <button
                                    type="button"
                                    class="shrink-0 font-semibold text-red-600 hover:text-red-700"
                                    @click="removeDocument(index)"
                                    :aria-label="@js(__('talenma.auth.registration_documents_remove'))"
                                >{{ __('talenma.auth.registration_documents_remove') }}</button>
                            </li>
                        </template>
                    </ul>
                    <p class="mt-0.5 text-[11px] sm:text-xs text-gray-500">{{ __('talenma.auth.company_registration_documents_hint') }}</p>
                    <x-input-error :messages="$errors->get('documents')" class="mt-1" />
                    <x-input-error :messages="$errors->get('documents.*')" class="mt-1" />
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
                    <select id="sector" name="sector" x-model="sector" @blur="onFieldBlur('sector')" @change="onFieldInput('sector')" x-bind:class="fieldInvalidClass('sector')" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm py-2">
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
                        rows="5"
                        minlength="255"
                        maxlength="2550"
                        x-model="description"
                        @blur="onFieldBlur('description')"
                        @input="onFieldInput('description')"
                        x-bind:class="fieldInvalidClass('description')"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm resize-none"
                        placeholder="{{ __('talenma.auth.registration_description_placeholder') }}"
                    >{{ old('description') }}</textarea>
                    <p class="mt-0.5 text-[11px] text-gray-500 text-right"><span x-text="description.length"></span>/2550</p>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="documents" :value="__('talenma.auth.registration_documents')" class="text-xs sm:text-sm" />
                    <input
                        id="documents"
                        x-ref="talentDocuments"
                        name="documents[]"
                        type="file"
                        x-bind:disabled="!isTalent"
                        @change="onDocumentsChange($event)"
                        @blur="onFieldBlur('documents')"
                        x-bind:class="fieldInvalidClass('documents')"
                        class="mt-1 block w-full text-xs sm:text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                        multiple
                    >
                    <ul x-show="documentFiles.length > 0" class="mt-2 space-y-1.5" x-cloak>
                        <template x-for="(file, index) in documentFiles" :key="documentFileKey(file)">
                            <li class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs text-gray-700">
                                <span class="min-w-0 truncate" x-text="file.name"></span>
                                <button
                                    type="button"
                                    class="shrink-0 font-semibold text-red-600 hover:text-red-700"
                                    @click="removeDocument(index)"
                                    :aria-label="@js(__('talenma.auth.registration_documents_remove'))"
                                >{{ __('talenma.auth.registration_documents_remove') }}</button>
                            </li>
                        </template>
                    </ul>
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

            <div class="flex-1 flex justify-end min-w-0">
                <button
                    type="button"
                    x-show="showNext"
                    x-cloak
                    @click="next()"
                    :disabled="!canGoNext"
                    :class="canGoNext
                        ? (isCompany
                            ? 'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition shadow-sm'
                            : 'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm')
                        : 'inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50'"
                >
                    {{ __('talenma.auth.register_continue') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button
                    type="submit"
                    x-show="showSubmit"
                    x-cloak
                    :disabled="!canSubmit"
                    :class="canSubmit
                        ? (isCompany
                            ? 'inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition'
                            : 'inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition')
                        : 'inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-lg font-semibold text-sm text-gray-400 cursor-not-allowed'"
                >
                    {{ __('talenma.auth.register_btn') }}
                </button>
            </div>
        </div>

        <p class="shrink-0 mt-2 text-center text-xs sm:text-sm text-gray-600">{{ __('talenma.auth.has_account') }} <a href="{{ route('login') }}" class="text-indigo-600 font-medium">{{ __('talenma.auth.login_btn') }}</a></p>
    </form>
</x-guest-layout>
