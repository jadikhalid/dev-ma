<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <x-user-avatar :user="$user" size="md" />
            <div>
                <h2 class="text-xl font-bold">{{ trim($user->first_name.' '.$user->last_name) ?: $user->name }}</h2>
                <p id="profile-header-profession" class="text-sm text-gray-500">{{ $profile->professionLabel() ?? '—' }}</p>
                <p id="profile-header-sector" class="mt-1 text-xs font-medium text-indigo-600">{{ $profile->sectorLabel() ?? '—' }}</p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8"
        data-ajax-network-error="{{ __('talenma.talent.network_error') }}"
        data-ajax-timeout-error="{{ __('talenma.talent.timeout_error') }}"
    >
        <x-toast-stack persistent />

        <form method="POST" action="{{ route('profile.details.update') }}" class="bg-white rounded-2xl border p-3 space-y-3" data-ajax novalidate data-error-message="{{ __('talenma.talent.save_error') }}">
            @csrf
            <input type="hidden" name="section" value="visibility">

            @php $isPrivate = ! (bool) old('is_public', $profile->is_public ?? true); @endphp
            <div
                class="flex items-start justify-between gap-4 rounded-xl border border-gray-200 p-3"
                x-data="{ isPrivate: @js($isPrivate) }"
            >
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.visibility_private') }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ __('talenma.talent.visibility_private_hint') }}</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center shrink-0">
                    <input type="hidden" name="is_public" :value="isPrivate ? '0' : '1'">
                    <input
                        type="checkbox"
                        class="peer sr-only"
                        :checked="isPrivate"
                        @change="isPrivate = $event.target.checked; $nextTick(() => $el.form.requestSubmit())"
                    >
                    <span class="h-7 w-12 rounded-full bg-gray-300 transition peer-checked:bg-indigo-600 peer-focus:ring-2 peer-focus:ring-indigo-500 peer-focus:ring-offset-2 after:absolute after:left-0.5 after:top-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:transition after:content-[''] peer-checked:after:translate-x-5"></span>
                    <span class="sr-only">{{ __('talenma.talent.visibility_private') }}</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('is_public')" class="mt-2" />
        </form>

        <form
            id="talent-profession-card"
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6"
            data-ajax
            data-loading-target="talent-profession-card"
            novalidate
            data-error-message="{{ __('talenma.talent.save_error') }}"
        >
            @csrf
            <input type="hidden" name="section" value="profession">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_profession') }}</h3>
            </div>

            <x-profile-profession-fields
                :sectors="$professionSectors"
                :sector="$sectorSlug"
                :profession="$professionSlug"
                :specialization="$specialization"
            />

            <div>
                <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>

        <div id="talent-presentation-card" class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            <form
                method="POST"
                action="{{ route('profile.details.update') }}"
                enctype="multipart/form-data"
                class="space-y-6"
                data-ajax
                data-refresh="presentation"
                data-loading-target="talent-presentation-card"
                novalidate
                data-error-message="{{ __('talenma.talent.save_error') }}"
                x-data="talentDocumentsPicker({
                    savedOtherCount: {{ $registrationDocuments->count() }},
                    savedFileNames: @js($registrationDocuments->pluck('original_name')->values()),
                    maxOther: {{ \App\Services\ProfileDocumentService::MAX_REGISTRATION }},
                    maxBytes: {{ 1024 * 1024 }},
                    allowedMimes: @js(\App\Services\ProfileDocumentService::ALLOWED_MIMES),
                    messages: {
                        invalidType: @js(__('talenma.auth.validation.documents_type')),
                        tooLarge: @js(__('talenma.auth.validation.documents_size')),
                        otherMax: @js(__('talenma.auth.validation.documents_max')),
                        duplicateName: @js(__('talenma.talent.certifications_docs_duplicate')),
                    },
                })"
            >
                @csrf
                <input type="hidden" name="section" value="presentation">

                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_presentation') }}</h3>
                </div>

                <div>
                    <x-input-label for="bio" :value="__('talenma.talent.bio')" />
                    <textarea
                        id="bio"
                        name="bio"
                        rows="6"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                        required
                        data-required
                        data-required-message="{{ __('talenma.talent.required_bio') }}"
                        data-min-length="30"
                        data-min-length-message="{{ __('talenma.talent.required_bio_min') }}"
                        placeholder="{{ __('talenma.talent.bio_placeholder') }}"
                    >{{ old('bio', filled($profile->bio) ? $profile->bio : $profile->registration_description) }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="experience_years" :value="__('talenma.talent.experience')" />
                        <x-text-input id="experience_years" name="experience_years" type="number" class="mt-1 block w-full" :value="old('experience_years', $profile->experience_years)" min="0" max="50" required data-required data-required-message="{{ __('talenma.talent.required_experience') }}" />
                        <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="education_level" :value="__('talenma.talent.education')" />
                        <select id="education_level" name="education_level" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required data-required data-required-message="{{ __('talenma.talent.required_education') }}">
                            <option value="">{{ __('talenma.talent.education_placeholder') }}</option>
                            @foreach ($educationOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('education_level', $profile->education_level) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('education_level')" class="mt-2" />
                    </div>
                </div>

                <div
                    class="space-y-4 border-t border-gray-100 pt-5"
                    data-required-group
                    data-required-message="{{ __('talenma.talent.required_languages') }}"
                >
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ __('talenma.talent.languages') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @php $selectedLanguages = old('languages', $profile->languages ?? []); @endphp
                        @foreach ($languageOptions as $value => $label)
                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm cursor-pointer hover:border-indigo-200">
                                <input type="checkbox" name="languages[]" value="{{ $value }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($value, $selectedLanguages, true))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('languages')" class="mt-2" />
                </div>

                <div class="space-y-4 border-t border-gray-100 pt-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ __('talenma.talent.certifications') }}</p>
                    </div>

                    @if ($registrationDocuments->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach ($registrationDocuments as $document)
                                <li class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $document->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $document->formattedSize() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ route('profile.documents.show', $document) }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.talent.document_view') }}</a>
                                        <button
                                            type="submit"
                                            form="delete-cert-{{ $document->id }}"
                                            class="text-sm font-semibold text-red-600 hover:text-red-700"
                                        >{{ __('talenma.talent.document_remove') }}</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">{{ __('talenma.talent.certifications_docs_empty') }}</p>
                    @endif

                    <div>
                        <x-input-label for="certification_documents" :value="__('talenma.talent.certifications_docs_upload')" />
                        <input
                            id="certification_documents"
                            name="certification_documents[]"
                            type="file"
                            multiple
                            x-ref="othersInput"
                            accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                            @change="onOthersChange($event)"
                            :disabled="!canAddOthers"
                            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 disabled:opacity-50"
                        >
                        <ul x-show="pendingOthers.length" class="mt-2 space-y-2" x-cloak>
                            <template x-for="(file, index) in pendingOthers" :key="fileKey(file)">
                                <li class="flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatSize(file.size)"></p>
                                    </div>
                                    <button
                                        type="button"
                                        class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700"
                                        @click="removePendingOther(index)"
                                    >{{ __('talenma.talent.document_cancel_selection') }}</button>
                                </li>
                            </template>
                        </ul>
                        <p class="mt-1 text-xs text-gray-500">
                            <span x-text="otherTotalCount"></span> / {{ \App\Services\ProfileDocumentService::MAX_REGISTRATION }} {{ __('talenma.talent.other_documents_hint_suffix') }}
                        </p>
                        <x-input-error :messages="$errors->get('certification_documents')" class="mt-2" />
                        <x-input-error :messages="$errors->get('certification_documents.*')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                    <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</button>
                    <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
                </div>
            </form>

            @foreach ($registrationDocuments as $document)
                <form
                    id="delete-cert-{{ $document->id }}"
                    method="POST"
                    action="{{ route('profile.documents.destroy', $document) }}"
                    data-ajax
                    data-refresh="presentation"
                    data-loading-target="talent-presentation-card"
                    data-confirm="{{ __('talenma.talent.document_delete_confirm') }}"
                    data-error-message="{{ __('talenma.talent.save_error') }}"
                    class="hidden"
                >
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach
        </div>

        <div id="talent-documents-card" class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_documents') }}</h3>
            </div>

            @php
                $cvByLanguage = $cvDocuments->keyBy('language');
            @endphp

            <div class="space-y-2">
                @forelse ($cvDocuments as $document)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50/60 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $document->original_name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $document->languageLabel() }}
                                · {{ $document->formattedSize() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('profile.documents.show', $document) }}" target="_blank" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.talent.document_view') }}</a>
                            <form method="POST" action="{{ route('profile.documents.destroy', $document) }}" data-ajax data-refresh="documents" data-loading-target="talent-documents-card" data-confirm="{{ __('talenma.talent.document_delete_confirm') }}" data-error-message="{{ __('talenma.talent.save_error') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">{{ __('talenma.talent.document_remove') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('talenma.talent.cv_empty') }}</p>
                @endforelse
            </div>

            <form
                method="POST"
                action="{{ route('profile.details.update') }}"
                enctype="multipart/form-data"
                data-ajax
                data-refresh="documents"
                data-loading-target="talent-documents-card"
                novalidate
                data-error-message="{{ __('talenma.talent.save_error') }}"
                class="space-y-4 border-t border-gray-100 pt-5"
                x-data="talentDocumentsPicker({
                    maxBytes: {{ 1024 * 1024 }},
                    allowedMimes: @js(\App\Services\ProfileDocumentService::ALLOWED_MIMES),
                    existingCvs: @js($cvDocuments->map(fn ($document) => [
                        'language' => $document->language,
                        'name' => $document->original_name,
                    ])->values()),
                    messages: {
                        invalidType: @js(__('talenma.auth.validation.documents_type')),
                        tooLarge: @js(__('talenma.auth.validation.documents_size')),
                        duplicateName: @js(__('talenma.talent.cv_docs_duplicate')),
                    },
                })"
            >
                @csrf
                <input type="hidden" name="section" value="documents">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="cv_language" :value="__('talenma.talent.cv_language')" />
                        <select
                            id="cv_language"
                            name="cv_language"
                            x-ref="cvLanguage"
                            @change="onCvLanguageChange()"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                            required
                            data-required
                            data-required-message="{{ __('talenma.talent.cv_language_required') }}"
                        >
                            <option value="">{{ __('talenma.talent.cv_language_placeholder') }}</option>
                            @foreach ($cvLanguageOptions as $code => $label)
                                <option value="{{ $code }}">
                                    {{ $label }}@if ($cvByLanguage->has($code)) — {{ __('talenma.talent.cv_replace_hint') }}@endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cv_language')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cv" :value="__('talenma.talent.cv_upload')" />
                        <input
                            id="cv"
                            name="cv"
                            type="file"
                            x-ref="cvInput"
                            accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp"
                            @change="onCvChange($event)"
                            required
                            data-required
                            data-required-message="{{ __('talenma.talent.cv_required') }}"
                            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <template x-if="pendingCv">
                            <div class="mt-2 flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="pendingCv.name"></p>
                                    <p class="text-xs text-gray-500" x-text="formatSize(pendingCv.size)"></p>
                                </div>
                                <button
                                    type="button"
                                    class="shrink-0 text-sm font-semibold text-red-600 hover:text-red-700"
                                    @click="clearCv()"
                                >{{ __('talenma.talent.document_cancel_selection') }}</button>
                            </div>
                        </template>
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.talent.cv_hint') }}</p>
                        <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                    <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</button>
                    <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
                </div>
            </form>
        </div>

        <form
            id="talent-availability-card"
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6"
            data-ajax
            data-loading-target="talent-availability-card"
            novalidate
            data-error-message="{{ __('talenma.talent.save_error') }}"
        >
            @csrf
            <input type="hidden" name="section" value="availability">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.section_availability') }}</h3>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="availability" :value="__('talenma.talent.status')" />
                    <select
                        id="availability"
                        name="availability"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                        required
                        data-required
                        data-required-message="{{ __('talenma.talent.required_availability') }}"
                    >
                        @foreach (\App\Models\Profile::statusOptions() as $value => $key)
                            <option value="{{ $value }}" @selected(old('availability', $profile->availability ?? \App\Models\Profile::STATUS_AVAILABLE) === $value)>{{ __('talenma.talent.'.$key) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('availability')" class="mt-2" />
                </div>
            </div>

            <div
                data-required-group
                data-required-message="{{ __('talenma.talent.required_work_modes') }}"
            >
                <x-input-label :value="__('talenma.talent.work_modes')" />
                <div class="mt-3 grid sm:grid-cols-3 gap-3">
                    @php $selectedModes = old('work_modes', $profile->work_modes ?? []); @endphp
                    @foreach ($workModeOptions as $value => $label)
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 hover:border-indigo-200 cursor-pointer">
                            <input type="checkbox" name="work_modes[]" value="{{ $value }}" class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($value, $selectedModes, true))>
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('work_modes')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>

        <form
            id="talent-links-card"
            method="POST"
            action="{{ route('profile.details.update') }}"
            class="relative bg-white rounded-2xl border p-6 sm:p-8 space-y-6"
            data-ajax
            data-loading-target="talent-links-card"
            novalidate
            data-error-message="{{ __('talenma.talent.save_error') }}"
            x-data="talentLocationSelect({
                country: @js(old('country', $profile->country)),
                city: @js(old('city', $profile->city)),
                citiesByCountry: @js($citiesByCountry),
            })"
        >
            @csrf
            <input type="hidden" name="section" value="links">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.talent.links') }}</h3>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="country" :value="__('talenma.talent.country')" />
                    <select
                        id="country"
                        name="country"
                        x-model="country"
                        @change="onCountryChange()"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                    >
                        <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                        @foreach ($countryOptions as $code => $label)
                            <option value="{{ $code }}" @selected(old('country', $profile->country) === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="city" :value="__('talenma.talent.city')" />
                    <input type="hidden" name="city" :value="city">
                    <select
                        id="city"
                        x-model="city"
                        :disabled="!country"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm disabled:bg-gray-50 disabled:text-gray-400"
                    >
                        <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                        @foreach ($citiesByCountry as $countryCode => $cityList)
                            @foreach ($cityList as $cityOption)
                                <option
                                    value="{{ $cityOption }}"
                                    data-country="{{ $countryCode }}"
                                    @selected(old('city', $profile->city) === $cityOption && old('country', $profile->country) === $countryCode)
                                    :hidden="country !== '{{ $countryCode }}'"
                                    :disabled="country !== '{{ $countryCode }}'"
                                >{{ $cityOption }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('talenma.talent.phone')" />
                    <x-text-input
                        id="phone"
                        name="phone"
                        type="tel"
                        class="mt-1 block w-full"
                        :value="old('phone', $profile->phone)"
                        placeholder="+212 6 00 00 00 00"
                        maxlength="30"
                        data-phone
                        data-phone-message="{{ __('talenma.talent.phone_invalid') }}"
                    />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="whatsapp" :value="__('talenma.talent.whatsapp')" />
                    <x-text-input
                        id="whatsapp"
                        name="whatsapp"
                        type="tel"
                        class="mt-1 block w-full"
                        :value="old('whatsapp', $profile->whatsapp)"
                        placeholder="+212 6 00 00 00 00"
                        maxlength="30"
                        data-phone
                        data-phone-message="{{ __('talenma.talent.whatsapp_invalid') }}"
                    />
                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="linkedin_url" value="LinkedIn" />
                    <x-text-input
                        id="linkedin_url"
                        name="linkedin_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('linkedin_url', $profile->linkedin_url)"
                        placeholder="https://linkedin.com/in/..."
                        data-url
                        data-url-message="{{ __('talenma.talent.linkedin_invalid') }}"
                        data-url-host="linkedin.com"
                        data-url-host-message="{{ __('talenma.talent.linkedin_host_invalid') }}"
                    />
                    <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="github_url" value="GitHub" />
                    <x-text-input
                        id="github_url"
                        name="github_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('github_url', $profile->github_url)"
                        placeholder="https://github.com/..."
                        data-url
                        data-url-message="{{ __('talenma.talent.github_invalid') }}"
                        data-url-host="github.com"
                        data-url-host-message="{{ __('talenma.talent.github_host_invalid') }}"
                    />
                    <x-input-error :messages="$errors->get('github_url')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="portfolio_url" :value="__('talenma.talent.portfolio')" />
                    <x-text-input
                        id="portfolio_url"
                        name="portfolio_url"
                        type="url"
                        class="mt-1 block w-full"
                        :value="old('portfolio_url', $profile->portfolio_url)"
                        placeholder="https://..."
                        data-url
                        data-url-message="{{ __('talenma.talent.portfolio_invalid') }}"
                    />
                    <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.talent.cancel') }}</button>
                <x-primary-button class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
            </div>
        </form>

        </div>
    </div>
</x-app-layout>
