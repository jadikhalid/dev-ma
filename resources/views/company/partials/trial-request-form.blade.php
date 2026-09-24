<form
    id="company-trial-request-form"
    method="POST"
    action="{{ route('company.trial.store') }}"
    class="relative space-y-4"
    novalidate
    @submit="onSubmit($event)"
    :aria-busy="submitting"
    x-data="companyOfferAjaxForm({
        mode: 'trial',
        loadingTargetId: 'company-trial-request-form',
        messages: {
            company_required: @js(__('talenma.company_offer.demo_company_required')),
            contact_required: @js(__('talenma.company_offer.trial_contact_required')),
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
>
    @csrf

    <div>
        <label for="trial_company_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.company_name') }}</label>
        <input
            id="trial_company_name"
            name="company_name"
            type="text"
            required
            maxlength="255"
            value="{{ old('company_name') }}"
            @input="clearFieldError('company_name')"
            :class="fieldInvalidClass('company_name')"
            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
        <p x-show="fieldMessage('company_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_name')"></p>
    </div>

    <div>
        <label for="trial_contact_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.trial_contact') }}</label>
        <input
            id="trial_contact_name"
            name="contact_name"
            type="text"
            required
            maxlength="255"
            value="{{ old('contact_name') }}"
            @input="clearFieldError('contact_name')"
            :class="fieldInvalidClass('contact_name')"
            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
        <p x-show="fieldMessage('contact_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('contact_name')"></p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="trial_email" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.email') }}</label>
            <input
                id="trial_email"
                name="email"
                type="email"
                required
                maxlength="255"
                value="{{ old('email') }}"
                @input="clearFieldError('email')"
                :class="fieldInvalidClass('email')"
                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
            <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('email')"></p>
        </div>
        <div>
            <label for="trial_phone" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.trial_phone') }}</label>
            <input
                id="trial_phone"
                name="phone"
                type="tel"
                required
                maxlength="50"
                value="{{ old('phone') }}"
                placeholder="+212 6 00 00 00 00"
                @input="clearFieldError('phone')"
                :class="fieldInvalidClass('phone')"
                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
            <p x-show="fieldMessage('phone')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('phone')"></p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="trial_sector" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.sector') }}</label>
            <select
                id="trial_sector"
                name="sector"
                required
                @change="clearFieldError('sector')"
                :class="fieldInvalidClass('sector')"
                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
                <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                @foreach ($professionSectors as $sectorOption)
                    <option value="{{ $sectorOption['slug'] }}" @selected(old('sector') === $sectorOption['slug'])>{{ $sectorOption['name'] }}</option>
                @endforeach
            </select>
            <p x-show="fieldMessage('sector')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('sector')"></p>
        </div>
        <div>
            <label for="trial_country" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.company_country') }}</label>
            <select
                id="trial_country"
                name="company_country"
                required
                @change="clearFieldError('company_country')"
                :class="fieldInvalidClass('company_country')"
                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
                <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                @foreach ($companyCountryOptions as $code => $label)
                    <option value="{{ $code }}" @selected(old('company_country') === $code)>{{ $label }}</option>
                @endforeach
            </select>
            <p x-show="fieldMessage('company_country')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_country')"></p>
        </div>
    </div>

    <div>
        <label for="trial_description" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company.description') }}</label>
        <textarea
            id="trial_description"
            name="company_description"
            rows="4"
            required
            maxlength="5000"
            @input="clearFieldError('company_description')"
            :class="fieldInvalidClass('company_description')"
            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 resize-none"
            placeholder="{{ __('talenma.company.description_placeholder') }}"
        >{{ old('company_description') }}</textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company_offer.trial_description_hint') }}</p>
        <p x-show="fieldMessage('company_description')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_description')"></p>
    </div>

    <div>
        <label for="trial_website" class="block text-sm font-semibold text-gray-700">{{ __('talenma.auth.company_website') }}</label>
        <input
            id="trial_website"
            name="company_website"
            type="url"
            maxlength="255"
            value="{{ old('company_website') }}"
            placeholder="https://..."
            @input="clearFieldError('company_website')"
            :class="fieldInvalidClass('company_website')"
            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
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
                @checked(old('data_processing_consent'))
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

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-1">
        <p class="text-xs text-gray-500">{{ __('talenma.company_offer.trial_privacy') }}</p>
        <button
            type="submit"
            :disabled="submitting"
            class="inline-flex justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-70"
        >
            <span x-show="!submitting">{{ __('talenma.company_offer.trial_submit') }}</span>
            <span x-show="submitting" x-cloak>{{ __('talenma.auth.register_submitting') }}</span>
        </button>
    </div>
</form>
