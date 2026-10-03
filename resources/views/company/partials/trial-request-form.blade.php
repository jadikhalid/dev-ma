@php
    use App\Models\CompanyDemoRequest;

    $trialOld = old('offer_form') === 'trial';
    $inputClass = 'mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500';
    $oldPhoneCountry = $trialOld ? old('phone_country', 'ma') : 'ma';
@endphp

<div
    x-data="companyOfferAjaxForm({
        mode: 'trial',
        loadingTargetId: 'company-trial-request-form',
        messages: {
            step_contact: @js(__('talenma.company_offer.trial_form.step_contact')),
            step_company: @js(__('talenma.company_offer.trial_form.step_company')),
            first_name_required: @js(__('talenma.company_offer.demo_form.first_name_required')),
            last_name_required: @js(__('talenma.company_offer.demo_form.last_name_required')),
            company_required: @js(__('talenma.company_offer.demo_company_required')),
            email_required: @js(__('talenma.company_offer.demo_email_required')),
            email_invalid: @js(__('talenma.company_offer.demo_email_invalid')),
            phone_required: @js(__('talenma.company_offer.trial_phone_required')),
            phone_invalid: @js(__('talenma.company.phone_invalid')),
            sector_required: @js(__('talenma.auth.validation.sector_required')),
            country_required: @js(__('talenma.auth.validation.company_country_required')),
            description_required: @js(__('talenma.auth.validation.company_description_required')),
            description_min: @js(__('talenma.auth.validation.company_description_min')),
            website_invalid: @js(__('talenma.auth.validation.company_website_invalid')),
            consent_required: @js(__('talenma.auth.validation.data_processing_consent_required')),
            incomplete: @js(__('talenma.auth.register_incomplete_toast')),
            network_error: @js(__('talenma.common.network_error')),
            sent: @js(__('talenma.company_offer.trial_sent')),
        },
    })"
    data-trial-wizard
>
    <form
        id="company-trial-request-form"
        method="POST"
        action="{{ route('company.trial.store') }}"
        class="relative"
        novalidate
        x-show="step < 3"
        @submit="onSubmit($event)"
        :aria-busy="submitting"
    >
        @csrf
        <input type="hidden" name="offer_form" value="trial">

        <div class="mb-6">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-gray-500">
                <span x-text="stepTitle()">{{ __('talenma.company_offer.trial_form.step_contact') }}</span>
                <span x-text="@js(__('talenma.company_offer.trial_form.step_label', ['current' => '__C__', 'total' => 2])).replace('__C__', step)">{{ __('talenma.company_offer.trial_form.step_label', ['current' => 1, 'total' => 2]) }}</span>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-1.5" aria-hidden="true">
                <span class="h-1.5 rounded-full bg-emerald-600"></span>
                <span class="h-1.5 rounded-full transition-colors" :class="step >= 2 ? 'bg-emerald-600' : 'bg-gray-200'"></span>
            </div>
        </div>

        {{-- Étape 1 : coordonnées --}}
        <fieldset x-show="step === 1" class="space-y-4" data-trial-step="1">
            <legend class="sr-only">{{ __('talenma.company_offer.trial_form.step_contact') }}</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="trial_first_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.first_name') }}<span class="text-rose-500">*</span></label>
                    <input id="trial_first_name" name="first_name" type="text" required maxlength="100" autocomplete="given-name"
                        value="{{ $trialOld ? old('first_name') : '' }}"
                        @input="clearFieldError('first_name')" :class="fieldInvalidClass('first_name')" class="{{ $inputClass }}">
                    <p x-show="fieldMessage('first_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('first_name')"></p>
                </div>
                <div>
                    <label for="trial_last_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.last_name') }}<span class="text-rose-500">*</span></label>
                    <input id="trial_last_name" name="last_name" type="text" required maxlength="100" autocomplete="family-name"
                        value="{{ $trialOld ? old('last_name') : '' }}"
                        @input="clearFieldError('last_name')" :class="fieldInvalidClass('last_name')" class="{{ $inputClass }}">
                    <p x-show="fieldMessage('last_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('last_name')"></p>
                </div>
            </div>

            <div>
                <label for="trial_email" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.email') }}<span class="text-rose-500">*</span></label>
                <input id="trial_email" name="email" type="email" required maxlength="255" autocomplete="email"
                    value="{{ $trialOld ? old('email') : '' }}"
                    @input="clearFieldError('email')" :class="fieldInvalidClass('email')" class="{{ $inputClass }}">
                <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('email')"></p>
            </div>

            <div>
                <label for="trial_phone" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.phone') }}<span class="text-rose-500">*</span></label>
                <div class="mt-1.5 flex gap-2">
                    <select name="phone_country" aria-label="{{ __('talenma.company_offer.demo_form.phone_country') }}" class="w-28 shrink-0 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @foreach (CompanyDemoRequest::PHONE_COUNTRIES as $code => $country)
                            <option value="{{ $code }}" @selected($oldPhoneCountry === $code)>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                        @endforeach
                    </select>
                    <input id="trial_phone" name="phone" type="tel" required maxlength="20" autocomplete="tel-national" placeholder="6 00 00 00 00"
                        value="{{ $trialOld ? old('phone') : '' }}"
                        @input="clearFieldError('phone')" :class="fieldInvalidClass('phone')" class="w-full min-w-0 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <p x-show="fieldMessage('phone')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('phone')"></p>
            </div>

            <div>
                <label for="trial_company_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.company_name') }}<span class="text-rose-500">*</span></label>
                <input id="trial_company_name" name="company_name" type="text" required maxlength="255" autocomplete="organization"
                    value="{{ $trialOld ? old('company_name') : '' }}"
                    @input="clearFieldError('company_name')" :class="fieldInvalidClass('company_name')" class="{{ $inputClass }}">
                <p x-show="fieldMessage('company_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_name')"></p>
            </div>

            <p class="text-xs leading-relaxed text-gray-500">{{ __('talenma.company_offer.trial_privacy') }}</p>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row">
                <button type="button" @click="cancel()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-trial-cancel>
                    {{ __('talenma.company_offer.trial_form.cancel') }}
                </button>
                <button type="button" @click="nextStep($el.form)" class="inline-flex flex-1 justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" data-trial-next>
                    {{ __('talenma.company_offer.trial_form.next') }}
                </button>
            </div>
        </fieldset>

        {{-- Étape 2 : entreprise --}}
        <fieldset x-show="step === 2" x-cloak class="space-y-4" data-trial-step="2">
            <legend class="sr-only">{{ __('talenma.company_offer.trial_form.step_company') }}</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="trial_sector" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.sector') }}<span class="text-rose-500">*</span></label>
                    <select id="trial_sector" name="sector" required
                        @change="clearFieldError('sector')" :class="fieldInvalidClass('sector')" class="{{ $inputClass }}">
                        <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                        @foreach ($professionSectors as $sectorOption)
                            <option value="{{ $sectorOption['slug'] }}" @selected($trialOld && old('sector') === $sectorOption['slug'])>{{ $sectorOption['name'] }}</option>
                        @endforeach
                    </select>
                    <p x-show="fieldMessage('sector')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('sector')"></p>
                </div>
                <div>
                    <label for="trial_country" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.company_country') }}<span class="text-rose-500">*</span></label>
                    <select id="trial_country" name="company_country" required
                        @change="clearFieldError('company_country')" :class="fieldInvalidClass('company_country')" class="{{ $inputClass }}">
                        <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                        @foreach ($companyCountryOptions as $code => $label)
                            <option value="{{ $code }}" @selected($trialOld && old('company_country') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p x-show="fieldMessage('company_country')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_country')"></p>
                </div>
            </div>

            <div>
                <label for="trial_description" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company.description') }}<span class="text-rose-500">*</span></label>
                <textarea id="trial_description" name="company_description" rows="4" required maxlength="5000"
                    @input="clearFieldError('company_description')" :class="fieldInvalidClass('company_description')"
                    class="{{ $inputClass }} resize-none"
                    placeholder="{{ __('talenma.company.description_placeholder') }}">{{ $trialOld ? old('company_description') : '' }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company_offer.trial_description_hint') }}</p>
                <p x-show="fieldMessage('company_description')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_description')"></p>
            </div>

            <div>
                <label for="trial_website" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.company_website') }}</label>
                <input id="trial_website" name="company_website" type="url" maxlength="255"
                    value="{{ $trialOld ? old('company_website') : '' }}"
                    placeholder="https://..."
                    @input="clearFieldError('company_website')" :class="fieldInvalidClass('company_website')" class="{{ $inputClass }}">
                <p x-show="fieldMessage('company_website')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_website')"></p>
            </div>

            <div
                class="rounded-lg border bg-gray-50 px-3 py-3"
                :class="fieldErrors.data_processing_consent ? 'border-rose-400' : 'border-gray-200'"
            >
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input
                        id="trial_consent"
                        name="data_processing_consent"
                        type="checkbox"
                        value="1"
                        @checked($trialOld && old('data_processing_consent'))
                        @change="clearFieldError('data_processing_consent')"
                        class="mt-0.5 size-4 rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                        required
                    >
                    <span class="text-sm text-gray-700 leading-snug">
                        {!! __('talenma.auth.data_processing_consent_company', [
                            'policy' => '<a href="'.e(route('privacy')).'" target="_blank" rel="noopener noreferrer" class="font-semibold text-emerald-700 underline decoration-emerald-300 underline-offset-2 hover:text-emerald-900">'.e(__('talenma.auth.privacy_policy')).'</a>',
                        ]) !!}
                    </span>
                </label>
                <p x-show="fieldMessage('data_processing_consent')" x-cloak class="mt-2 text-xs text-rose-600" x-text="fieldMessage('data_processing_consent')"></p>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:flex-wrap">
                <button type="button" @click="cancel()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-trial-cancel>
                    {{ __('talenma.company_offer.trial_form.cancel') }}
                </button>
                <button type="button" @click="previousStep()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-trial-previous>
                    {{ __('talenma.company_offer.trial_form.previous') }}
                </button>
                <button type="submit" :disabled="submitting" class="inline-flex flex-1 justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-70" data-trial-submit>
                    <span x-show="!submitting">{{ __('talenma.company_offer.trial_submit') }}</span>
                    <span x-show="submitting" x-cloak>{{ __('talenma.auth.register_submitting') }}</span>
                </button>
            </div>
        </fieldset>
    </form>

    {{-- Étape 3 : remerciement --}}
    <div x-show="step === 3" x-cloak class="space-y-4" data-trial-step="3">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ __('talenma.company_offer.trial_form.thanks_title') }}</h3>
        <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_offer.trial_form.thanks_text') }}</p>
        <button type="button" @click="close()" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            {{ __('talenma.company_offer.trial_form.close') }}
        </button>
    </div>
</div>
