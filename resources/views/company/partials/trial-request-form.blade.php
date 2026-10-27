@php
    use App\Models\CompanyDemoRequest;
    use App\Support\ConsumerEmail;

    $trialOld = old('offer_form') === 'trial';
    $fieldShell = 'flex flex-col gap-1.5 rounded-[14px] border border-gray-200 bg-white px-4 py-3';
    $inputClass = 'w-full border-0 bg-transparent p-0 text-base text-gray-950 outline-none focus:ring-0';
    $selectClass = 'w-full border-0 bg-transparent p-0 text-base text-gray-950 outline-none focus:ring-0';
    $labelClass = 'text-[13px] font-bold text-gray-950';
    $oldPhoneCountry = $trialOld ? old('phone_country', 'ma') : 'ma';
    $btnPrimary = 'inline-flex flex-1 items-center justify-center gap-2 rounded-[14px] bg-[linear-gradient(120deg,#2563EB_0%,#5546F0_45%,#8B5CF6_100%)] px-5 py-3.5 text-sm font-bold text-white shadow-[0_14px_28px_-14px_rgba(79,70,229,0.8)] transition hover:brightness-105 disabled:opacity-70';
    $btnSecondary = 'inline-flex items-center justify-center rounded-[14px] border border-gray-200 bg-white px-5 py-3.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50';
@endphp

<div
    class="scroll-mt-20 sm:scroll-mt-32"
    x-data="companyOfferAjaxForm({
        mode: 'trial',
        loadingTargetId: 'company-trial-request-form',
        consumerEmailDomains: @js(ConsumerEmail::domains()),
        consumerEmailRoots: @js(ConsumerEmail::roots()),
        messages: {
            step_contact: @js(__('talenma.company_offer.trial_form.step_contact')),
            step_company: @js(__('talenma.company_offer.trial_form.step_company')),
            first_name_required: @js(__('talenma.company_offer.demo_form.first_name_required')),
            last_name_required: @js(__('talenma.company_offer.demo_form.last_name_required')),
            company_required: @js(__('talenma.company_offer.demo_company_required')),
            email_required: @js(__('talenma.company_offer.demo_email_required')),
            email_invalid: @js(__('talenma.company_offer.demo_email_invalid')),
            email_professional: @js(__('talenma.company_offer.trial_email_professional')),
            password_required: @js(__('talenma.auth.validation.password_required')),
            password_confirmed: @js(__('talenma.auth.validation.password_confirmed')),
            password_min: @js(__('talenma.auth.validation.password_min')),
            password_max: @js(__('talenma.auth.validation.password_max')),
            password_letters: @js(__('talenma.auth.validation.password_letters')),
            password_numbers: @js(__('talenma.auth.validation.password_numbers')),
            phone_required: @js(__('talenma.company_offer.trial_phone_required')),
            phone_invalid: @js(__('talenma.company.phone_invalid')),
            sector_required: @js(__('talenma.auth.validation.sector_required')),
            country_required: @js(__('talenma.auth.validation.company_country_required')),
            website_invalid: @js(__('talenma.auth.validation.company_website_invalid')),
            consent_required: @js(__('talenma.auth.validation.data_processing_consent_required')),
            terms_required: @js(__('talenma.company_offer.trial_terms.required')),
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
        class="relative flex flex-col gap-3.5"
        novalidate
        x-show="step < 3"
        @submit="onSubmit($event)"
        :aria-busy="submitting"
    >
        @csrf
        <input type="hidden" name="offer_form" value="trial">

        <div>
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-gray-500">
                <span x-text="stepTitle()">{{ __('talenma.company_offer.trial_form.step_contact') }}</span>
                <span x-text="@js(__('talenma.company_offer.trial_form.step_label', ['current' => '__C__', 'total' => 2])).replace('__C__', step)">{{ __('talenma.company_offer.trial_form.step_label', ['current' => 1, 'total' => 2]) }}</span>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-1.5" aria-hidden="true">
                <span class="h-1.5 rounded-full bg-[#5546F0]"></span>
                <span class="h-1.5 rounded-full transition-colors" :class="step >= 2 ? 'bg-[#5546F0]' : 'bg-gray-200'"></span>
            </div>
        </div>

        {{-- Étape 1 : coordonnées --}}
        <fieldset x-show="step === 1" class="flex flex-col gap-3.5" data-trial-step="1">
            <legend class="sr-only">{{ __('talenma.company_offer.trial_form.step_contact') }}</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="trial_first_name" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.company_offer.demo_form.first_name') }}<span class="text-rose-500">*</span></span>
                        <input id="trial_first_name" name="first_name" type="text" required maxlength="100" autocomplete="given-name"
                            placeholder="{{ __('talenma.company_offer.demo_form.first_name') }}"
                            value="{{ $trialOld ? old('first_name') : '' }}"
                            @input="clearFieldError('first_name')" :class="fieldInvalidClass('first_name')" class="{{ $inputClass }}">
                    </label>
                    <p x-show="fieldMessage('first_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('first_name')"></p>
                </div>
                <div>
                    <label for="trial_last_name" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.company_offer.demo_form.last_name') }}<span class="text-rose-500">*</span></span>
                        <input id="trial_last_name" name="last_name" type="text" required maxlength="100" autocomplete="family-name"
                            placeholder="{{ __('talenma.company_offer.demo_form.last_name') }}"
                            value="{{ $trialOld ? old('last_name') : '' }}"
                            @input="clearFieldError('last_name')" :class="fieldInvalidClass('last_name')" class="{{ $inputClass }}">
                    </label>
                    <p x-show="fieldMessage('last_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('last_name')"></p>
                </div>
            </div>

            <div>
                <label for="trial_company_name" class="{{ $fieldShell }}">
                    <span class="{{ $labelClass }}">{{ __('talenma.company_offer.demo_form.company_name') }}<span class="text-rose-500">*</span></span>
                    <input id="trial_company_name" name="company_name" type="text" required maxlength="255" autocomplete="organization"
                        placeholder="{{ __('talenma.company_offer.demo_form.company_name') }}"
                        value="{{ $trialOld ? old('company_name') : '' }}"
                        @input="clearFieldError('company_name')" :class="fieldInvalidClass('company_name')" class="{{ $inputClass }}">
                </label>
                <p x-show="fieldMessage('company_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_name')"></p>
            </div>

            <div>
                <label for="trial_email" class="{{ $fieldShell }}">
                    <span class="{{ $labelClass }}">{{ __('talenma.company_offer.demo_form.email') }}<span class="text-rose-500">*</span></span>
                    <input id="trial_email" name="email" type="email" required maxlength="255" autocomplete="email"
                        placeholder="vous@entreprise.com"
                        value="{{ $trialOld ? old('email') : '' }}"
                        @input="clearFieldError('email')" :class="fieldInvalidClass('email')" class="{{ $inputClass }}">
                </label>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company_offer.trial_email_hint') }}</p>
                <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('email')"></p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="trial_password" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.auth.password') }}<span class="text-rose-500">*</span></span>
                        <input id="trial_password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="new-password"
                            @input="clearFieldError('password')" :class="fieldInvalidClass('password')" class="{{ $inputClass }}">
                    </label>
                    <p x-show="fieldMessage('password')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('password')"></p>
                </div>
                <div>
                    <label for="trial_password_confirmation" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.auth.confirm_password') }}<span class="text-rose-500">*</span></span>
                        <input id="trial_password_confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="128" autocomplete="new-password"
                            @input="clearFieldError('password_confirmation')" :class="fieldInvalidClass('password_confirmation')" class="{{ $inputClass }}">
                    </label>
                    <p x-show="fieldMessage('password_confirmation')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('password_confirmation')"></p>
                </div>
            </div>

            <div>
                <div class="{{ $fieldShell }}">
                    <span class="{{ $labelClass }}">{{ __('talenma.company_offer.demo_form.phone') }}<span class="text-rose-500">*</span></span>
                    <div class="flex gap-2">
                        <select name="phone_country" aria-label="{{ __('talenma.company_offer.demo_form.phone_country') }}" class="w-28 shrink-0 {{ $selectClass }}">
                            @foreach (CompanyDemoRequest::PHONE_COUNTRIES as $code => $country)
                                <option value="{{ $code }}" @selected($oldPhoneCountry === $code)>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                            @endforeach
                        </select>
                        <input id="trial_phone" name="phone" type="tel" required maxlength="20" autocomplete="tel-national" placeholder="+212 / +33 …"
                            value="{{ $trialOld ? old('phone') : '' }}"
                            @input="clearFieldError('phone')" :class="fieldInvalidClass('phone')" class="min-w-0 flex-1 {{ $inputClass }}">
                    </div>
                </div>
                <p x-show="fieldMessage('phone')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('phone')"></p>
            </div>

            <p class="text-xs leading-relaxed text-gray-500">{{ __('talenma.company_offer.trial_privacy') }}</p>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row">
                <button type="button" @click="cancel()" class="{{ $btnSecondary }}" data-trial-cancel>
                    {{ __('talenma.company_offer.trial_form.cancel') }}
                </button>
                <button type="button" @click="nextStep($el.form)" class="{{ $btnPrimary }}" data-trial-next>
                    {{ __('talenma.company_offer.trial_form.next') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </div>
        </fieldset>

        {{-- Étape 2 : entreprise --}}
        <fieldset x-show="step === 2" x-cloak class="flex flex-col gap-3.5" data-trial-step="2">
            <legend class="sr-only">{{ __('talenma.company_offer.trial_form.step_company') }}</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="trial_sector" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.auth.sector') }}<span class="text-rose-500">*</span></span>
                        <select id="trial_sector" name="sector" required
                            @change="clearFieldError('sector')" :class="fieldInvalidClass('sector')" class="{{ $selectClass }}">
                            <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                            @foreach ($professionSectors as $sectorOption)
                                <option value="{{ $sectorOption['slug'] }}" @selected($trialOld && old('sector') === $sectorOption['slug'])>{{ $sectorOption['name'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <p x-show="fieldMessage('sector')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('sector')"></p>
                </div>
                <div>
                    <label for="trial_country" class="{{ $fieldShell }}">
                        <span class="{{ $labelClass }}">{{ __('talenma.auth.company_country') }}<span class="text-rose-500">*</span></span>
                        <select id="trial_country" name="company_country" required
                            @change="clearFieldError('company_country')" :class="fieldInvalidClass('company_country')" class="{{ $selectClass }}">
                            <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                            @foreach ($companyCountryOptions as $code => $label)
                                <option value="{{ $code }}" @selected($trialOld && old('company_country') === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <p x-show="fieldMessage('company_country')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_country')"></p>
                </div>
            </div>

            <div>
                <label for="trial_description" class="{{ $fieldShell }}">
                    <span class="{{ $labelClass }}">{{ __('talenma.company.description') }}</span>
                    <textarea id="trial_description" name="company_description" rows="3" maxlength="5000"
                        @input="clearFieldError('company_description')" :class="fieldInvalidClass('company_description')"
                        class="{{ $inputClass }} resize-none"
                        placeholder="{{ __('talenma.company.description_placeholder') }}">{{ $trialOld ? old('company_description') : '' }}</textarea>
                </label>
                <p x-show="fieldMessage('company_description')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_description')"></p>
            </div>

            <div>
                <label for="trial_website" class="{{ $fieldShell }}">
                    <span class="{{ $labelClass }}">{{ __('talenma.auth.company_website') }}</span>
                    <input id="trial_website" name="company_website" type="url" maxlength="255"
                        value="{{ $trialOld ? old('company_website') : '' }}"
                        placeholder="https://..."
                        @input="clearFieldError('company_website')" :class="fieldInvalidClass('company_website')" class="{{ $inputClass }}">
                </label>
                <p x-show="fieldMessage('company_website')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_website')"></p>
            </div>

            <div
                class="rounded-[14px] border bg-gray-50 px-4 py-3"
                :class="fieldErrors.data_processing_consent ? 'border-rose-400' : 'border-gray-200'"
            >
                <label class="flex cursor-pointer items-start gap-3">
                    <input
                        id="trial_consent"
                        name="data_processing_consent"
                        type="checkbox"
                        value="1"
                        @checked($trialOld && old('data_processing_consent'))
                        @change="clearFieldError('data_processing_consent')"
                        class="mt-0.5 size-5 rounded border-gray-300 text-[#5546F0] shadow-sm focus:ring-[#5546F0]"
                        required
                    >
                    <span class="text-[13px] leading-snug text-gray-600">
                        {!! __('talenma.auth.data_processing_consent_company', [
                            'policy' => '<a href="'.e(route('privacy')).'" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#5546F0] underline decoration-indigo-200 underline-offset-2 hover:text-indigo-800">'.e(__('talenma.auth.privacy_policy')).'</a>',
                        ]) !!}
                    </span>
                </label>
                <p x-show="fieldMessage('data_processing_consent')" x-cloak class="mt-2 text-xs text-rose-600" x-text="fieldMessage('data_processing_consent')"></p>
            </div>

            <div
                class="rounded-[14px] border bg-gray-50 px-4 py-3"
                :class="fieldErrors.trial_terms ? 'border-rose-400' : 'border-gray-200'"
            >
                <label class="flex cursor-pointer items-start gap-3">
                    <input
                        id="trial_terms"
                        name="trial_terms"
                        type="checkbox"
                        value="1"
                        @checked($trialOld && old('trial_terms'))
                        @change="clearFieldError('trial_terms')"
                        class="mt-0.5 size-5 rounded border-gray-300 text-[#5546F0] shadow-sm focus:ring-[#5546F0]"
                        required
                    >
                    <span class="text-[13px] leading-snug text-gray-600">
                        {!! __('talenma.company_offer.trial_terms.accept', [
                            'terms' => '<a href="'.e(route('company.trial.terms')).'" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#5546F0] underline decoration-indigo-200 underline-offset-2 hover:text-indigo-800">'.e(__('talenma.company_offer.trial_terms.link')).'</a>',
                        ]) !!}
                    </span>
                </label>
                <p x-show="fieldMessage('trial_terms')" x-cloak class="mt-2 text-xs text-rose-600" x-text="fieldMessage('trial_terms')"></p>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:flex-wrap">
                <button type="button" @click="cancel()" class="{{ $btnSecondary }}" data-trial-cancel>
                    {{ __('talenma.company_offer.trial_form.cancel') }}
                </button>
                <button type="button" @click="previousStep()" class="{{ $btnSecondary }}" data-trial-previous>
                    {{ __('talenma.company_offer.trial_form.previous') }}
                </button>
                <button type="submit" :disabled="submitting" class="{{ $btnPrimary }}" data-trial-submit>
                    <span x-show="!submitting" class="inline-flex items-center gap-2">
                        {{ __('talenma.company_offer.trial_submit') }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                    <span x-show="submitting" x-cloak>{{ __('talenma.auth.register_submitting') }}</span>
                </button>
            </div>
        </fieldset>
    </form>

    {{-- Étape 3 : remerciement --}}
    <div x-show="step === 3" x-cloak class="space-y-4" data-trial-step="3">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-[#5546F0]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ __('talenma.company_offer.trial_form.thanks_title') }}</h3>
        <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_offer.trial_form.thanks_text') }}</p>
        <button type="button" @click="close()" class="{{ $btnSecondary }} w-full">
            {{ __('talenma.company_offer.trial_form.close') }}
        </button>
    </div>
</div>
