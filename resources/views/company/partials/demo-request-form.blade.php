@php
    use App\Models\CompanyDemoRequest;

    $demoOld = old('offer_form') === 'demo';
    $inputClass = 'mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
    $chipClass = 'inline-flex cursor-pointer select-none items-center rounded-full border border-gray-300 bg-white px-3.5 py-1.5 text-sm font-medium text-gray-700 transition hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500 peer-focus-visible:ring-offset-1';
    $oldLocations = $demoOld ? (array) old('hiring_locations', []) : [];
    $oldSlots = $demoOld ? (array) old('preferred_slots', []) : [];
    $oldPhoneCountry = $demoOld ? old('phone_country', 'ma') : 'ma';
    $bookingDates = CompanyDemoRequest::availableBookingDates();
@endphp

<div
    x-data="companyOfferAjaxForm({
        mode: 'demo',
        loadingTargetId: 'company-demo-request-form',
        messages: {
            step_contact: @js(__('talenma.company_offer.demo_form.step_contact')),
            step_needs: @js(__('talenma.company_offer.demo_form.step_needs')),
            step_booking: @js(__('talenma.company_offer.demo_form.step_booking')),
            first_name_required: @js(__('talenma.company_offer.demo_form.first_name_required')),
            last_name_required: @js(__('talenma.company_offer.demo_form.last_name_required')),
            company_required: @js(__('talenma.company_offer.demo_company_required')),
            email_required: @js(__('talenma.company_offer.demo_email_required')),
            email_invalid: @js(__('talenma.company_offer.demo_email_invalid')),
            phone_required: @js(__('talenma.company_offer.demo_form.phone_required')),
            phone_invalid: @js(__('talenma.company.phone_invalid')),
            company_size_required: @js(__('talenma.company_offer.demo_form.company_size_required')),
            hires_planned_required: @js(__('talenma.company_offer.demo_form.hires_planned_required')),
            hiring_locations_required: @js(__('talenma.company_offer.demo_form.hiring_locations_required')),
            uses_ats_required: @js(__('talenma.company_offer.demo_form.uses_ats_required')),
            preferred_date_required: @js(__('talenma.company_offer.demo_form.preferred_date_required')),
            preferred_slots_required: @js(__('talenma.company_offer.demo_form.preferred_slots_required')),
            meeting_platform_required: @js(__('talenma.company_offer.demo_form.meeting_platform_required')),
            incomplete: @js(__('talenma.auth.register_incomplete_toast')),
            network_error: @js(__('talenma.common.network_error')),
            sent: @js(__('talenma.company_offer.demo_sent')),
        },
    })"
    data-demo-wizard
>
    <form
        id="company-demo-request-form"
        method="POST"
        action="{{ route('company.demo.store') }}"
        class="relative"
        novalidate
        x-show="step < 4"
        @submit="onSubmit($event)"
        :aria-busy="submitting"
    >
        @csrf
        <input type="hidden" name="offer_form" value="demo">

        <div class="mb-6">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-gray-500">
                <span x-text="stepTitle()">{{ __('talenma.company_offer.demo_form.step_contact') }}</span>
                <span x-text="@js(__('talenma.company_offer.demo_form.step_label', ['current' => '__C__', 'total' => 3])).replace('__C__', step)">{{ __('talenma.company_offer.demo_form.step_label', ['current' => 1, 'total' => 3]) }}</span>
            </div>
            <div class="mt-2 grid grid-cols-3 gap-1.5" aria-hidden="true">
                <span class="h-1.5 rounded-full bg-indigo-600"></span>
                <span class="h-1.5 rounded-full transition-colors" :class="step >= 2 ? 'bg-indigo-600' : 'bg-gray-200'"></span>
                <span class="h-1.5 rounded-full transition-colors" :class="step >= 3 ? 'bg-indigo-600' : 'bg-gray-200'"></span>
            </div>
        </div>

        {{-- Étape 1 : coordonnées --}}
        <fieldset x-show="step === 1" class="space-y-4" data-demo-step="1">
            <legend class="sr-only">{{ __('talenma.company_offer.demo_form.step_contact') }}</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="demo_first_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.first_name') }}<span class="text-rose-500">*</span></label>
                    <input id="demo_first_name" name="first_name" type="text" required maxlength="100" autocomplete="given-name"
                        value="{{ $demoOld ? old('first_name') : '' }}"
                        @input="clearFieldError('first_name')" :class="fieldInvalidClass('first_name')" class="{{ $inputClass }}">
                    <p x-show="fieldMessage('first_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('first_name')"></p>
                </div>
                <div>
                    <label for="demo_last_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.last_name') }}<span class="text-rose-500">*</span></label>
                    <input id="demo_last_name" name="last_name" type="text" required maxlength="100" autocomplete="family-name"
                        value="{{ $demoOld ? old('last_name') : '' }}"
                        @input="clearFieldError('last_name')" :class="fieldInvalidClass('last_name')" class="{{ $inputClass }}">
                    <p x-show="fieldMessage('last_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('last_name')"></p>
                </div>
            </div>

            <div>
                <label for="demo_email" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.email') }}<span class="text-rose-500">*</span></label>
                <input id="demo_email" name="email" type="email" required maxlength="255" autocomplete="email"
                    value="{{ $demoOld ? old('email') : '' }}"
                    @input="clearFieldError('email')" :class="fieldInvalidClass('email')" class="{{ $inputClass }}">
                <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('email')"></p>
            </div>

            <div>
                <label for="demo_phone" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.phone') }}<span class="text-rose-500">*</span></label>
                <div class="mt-1.5 flex gap-2">
                    <select name="phone_country" aria-label="{{ __('talenma.company_offer.demo_form.phone_country') }}" class="w-28 shrink-0 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" data-demo-phone-country>
                        @foreach (CompanyDemoRequest::PHONE_COUNTRIES as $code => $country)
                            <option value="{{ $code }}" @selected($oldPhoneCountry === $code)>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                        @endforeach
                    </select>
                    <input id="demo_phone" name="phone" type="tel" required maxlength="20" autocomplete="tel-national" placeholder="6 00 00 00 00"
                        value="{{ $demoOld ? old('phone') : '' }}"
                        @input="clearFieldError('phone')" :class="fieldInvalidClass('phone')" class="w-full min-w-0 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <p x-show="fieldMessage('phone')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('phone')"></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="demo_company_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.company_name') }}<span class="text-rose-500">*</span></label>
                    <input id="demo_company_name" name="company_name" type="text" required maxlength="255" autocomplete="organization"
                        value="{{ $demoOld ? old('company_name') : '' }}"
                        @input="clearFieldError('company_name')" :class="fieldInvalidClass('company_name')" class="{{ $inputClass }}">
                    <p x-show="fieldMessage('company_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_name')"></p>
                </div>
                <div>
                    <label for="demo_company_size" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.company_size') }}<span class="text-rose-500">*</span></label>
                    <select id="demo_company_size" name="company_size" required
                        @change="clearFieldError('company_size')" :class="fieldInvalidClass('company_size')" class="{{ $inputClass }}">
                        <option value="">{{ __('talenma.company_offer.demo_form.select_placeholder') }}</option>
                        @foreach (CompanyDemoRequest::COMPANY_SIZES as $size)
                            <option value="{{ $size }}" @selected($demoOld && old('company_size') === $size)>{{ $size }} {{ __('talenma.company_offer.demo_form.company_size_suffix') }}</option>
                        @endforeach
                    </select>
                    <p x-show="fieldMessage('company_size')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_size')"></p>
                </div>
            </div>

            <p class="text-xs leading-relaxed text-gray-500">
                {{ __('talenma.company_offer.demo_form.privacy') }}
                <a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="font-medium text-indigo-600 underline hover:text-indigo-700">{{ __('talenma.company_offer.demo_form.privacy_link') }}</a>.
            </p>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row">
                <button type="button" @click="cancel()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-demo-cancel>
                    {{ __('talenma.company_offer.demo_form.cancel') }}
                </button>
                <button type="button" @click="nextStep($el.form)" class="inline-flex flex-1 justify-center rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800" data-demo-next>
                    {{ __('talenma.company_offer.demo_form.next') }}
                </button>
            </div>
        </fieldset>

        {{-- Étape 2 : besoins --}}
        <fieldset x-show="step === 2" x-cloak class="space-y-5" data-demo-step="2">
            <legend class="sr-only">{{ __('talenma.company_offer.demo_form.step_needs') }}</legend>
            <div>
                <label for="demo_hires_planned" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.hires_planned') }}<span class="text-rose-500">*</span></label>
                <select id="demo_hires_planned" name="hires_planned" required
                    @change="clearFieldError('hires_planned')" :class="fieldInvalidClass('hires_planned')" class="{{ $inputClass }}">
                    <option value="">{{ __('talenma.company_offer.demo_form.select_placeholder') }}</option>
                    @foreach (CompanyDemoRequest::HIRES_PLANNED as $range)
                        <option value="{{ $range }}" @selected($demoOld && old('hires_planned') === $range)>{{ $range }}</option>
                    @endforeach
                </select>
                <p x-show="fieldMessage('hires_planned')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('hires_planned')"></p>
            </div>

            <div>
                <p class="block text-sm font-semibold text-gray-700" id="demo_hiring_locations_label">{{ __('talenma.company_offer.demo_form.hiring_locations') }}<span class="text-rose-500">*</span></p>
                <div class="mt-2 flex flex-wrap gap-2" role="group" aria-labelledby="demo_hiring_locations_label">
                    @foreach (CompanyDemoRequest::HIRING_LOCATIONS as $code)
                        <label class="relative">
                            <input type="checkbox" name="hiring_locations[]" value="{{ $code }}" class="peer sr-only"
                                @checked(in_array($code, $oldLocations, true))
                                @change="clearFieldError('hiring_locations')">
                            <span class="{{ $chipClass }}">{{ __('talenma.company_offer.demo_form.locations.'.$code) }}</span>
                        </label>
                    @endforeach
                </div>
                <p x-show="fieldMessage('hiring_locations')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('hiring_locations')"></p>
            </div>

            <div>
                <label for="demo_hiring_city" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.hiring_city') }}</label>
                <input id="demo_hiring_city" name="hiring_city" type="text" maxlength="120" placeholder="{{ __('talenma.company_offer.demo_form.hiring_city_placeholder') }}"
                    value="{{ $demoOld ? old('hiring_city') : '' }}" class="{{ $inputClass }}">
            </div>

            <div>
                <p class="block text-sm font-semibold text-gray-700" id="demo_uses_ats_label">{{ __('talenma.company_offer.demo_form.uses_ats') }}<span class="text-rose-500">*</span></p>
                <div class="mt-2 flex flex-wrap gap-2" role="radiogroup" aria-labelledby="demo_uses_ats_label">
                    @foreach (CompanyDemoRequest::ATS_OPTIONS as $option)
                        <label class="relative">
                            <input type="radio" name="uses_ats" value="{{ $option }}" class="peer sr-only"
                                @checked($demoOld && old('uses_ats') === $option)
                                @change="clearFieldError('uses_ats')">
                            <span class="{{ $chipClass }}">{{ __('talenma.company_offer.demo_form.ats.'.$option) }}</span>
                        </label>
                    @endforeach
                </div>
                <p x-show="fieldMessage('uses_ats')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('uses_ats')"></p>
            </div>

            <div>
                <label for="demo_message" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.message') }}</label>
                <textarea id="demo_message" name="message" rows="4" maxlength="5000" placeholder="{{ __('talenma.company_offer.demo_form.message_placeholder') }}"
                    class="{{ $inputClass }}">{{ $demoOld ? old('message') : '' }}</textarea>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:flex-wrap">
                <button type="button" @click="cancel()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-demo-cancel>
                    {{ __('talenma.company_offer.demo_form.cancel') }}
                </button>
                <button type="button" @click="previousStep()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-demo-previous>
                    {{ __('talenma.company_offer.demo_form.previous') }}
                </button>
                <button type="button" @click="nextStep($el.form)" class="inline-flex flex-1 justify-center rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800" data-demo-next-needs>
                    {{ __('talenma.company_offer.demo_form.next') }}
                </button>
            </div>
        </fieldset>

        {{-- Étape 3 : disponibilités --}}
        <fieldset x-show="step === 3" x-cloak class="space-y-5" data-demo-step="3">
            <legend class="sr-only">{{ __('talenma.company_offer.demo_form.step_booking') }}</legend>
            <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_offer.demo_form.booking_intro') }}</p>

            <div>
                <label for="demo_preferred_date" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_form.preferred_date') }}<span class="text-rose-500">*</span></label>
                <select id="demo_preferred_date" name="preferred_date" required
                    @change="clearFieldError('preferred_date')" :class="fieldInvalidClass('preferred_date')" class="{{ $inputClass }}" data-demo-preferred-date>
                    <option value="">{{ __('talenma.company_offer.demo_form.select_placeholder') }}</option>
                    @foreach ($bookingDates as $date)
                        <option value="{{ $date->toDateString() }}" @selected($demoOld && old('preferred_date') === $date->toDateString())>
                            {{ $date->translatedFormat('l j F Y') }}
                        </option>
                    @endforeach
                </select>
                <p x-show="fieldMessage('preferred_date')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('preferred_date')"></p>
            </div>

            <div>
                <p class="block text-sm font-semibold text-gray-700" id="demo_preferred_slots_label">{{ __('talenma.company_offer.demo_form.preferred_slots') }}<span class="text-rose-500">*</span></p>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company_offer.demo_form.preferred_slots_hint') }}</p>
                <div class="mt-2 grid grid-cols-2 gap-2" role="group" aria-labelledby="demo_preferred_slots_label">
                    @foreach (CompanyDemoRequest::SLOT_STARTS as $start)
                        <label class="relative">
                            <input type="checkbox" name="preferred_slots[]" value="{{ $start }}" class="peer sr-only"
                                @checked(in_array($start, $oldSlots, true))
                                @change="clearFieldError('preferred_slots')">
                            <span class="{{ $chipClass }} w-full justify-center">{{ CompanyDemoRequest::slotLabel($start) }}</span>
                        </label>
                    @endforeach
                </div>
                <p x-show="fieldMessage('preferred_slots')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('preferred_slots')"></p>
            </div>

            <div>
                <p class="block text-sm font-semibold text-gray-700" id="demo_meeting_platform_label">{{ __('talenma.company_offer.demo_form.meeting_platform') }}<span class="text-rose-500">*</span></p>
                <div class="mt-2 flex flex-wrap gap-2" role="radiogroup" aria-labelledby="demo_meeting_platform_label">
                    @foreach (CompanyDemoRequest::MEETING_PLATFORMS as $platform)
                        <label class="relative">
                            <input type="radio" name="meeting_platform" value="{{ $platform }}" class="peer sr-only"
                                @checked($demoOld && old('meeting_platform') === $platform)
                                @change="clearFieldError('meeting_platform')">
                            <span class="{{ $chipClass }}">{{ __('talenma.company_offer.demo_form.platforms.'.$platform) }}</span>
                        </label>
                    @endforeach
                </div>
                <p x-show="fieldMessage('meeting_platform')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('meeting_platform')"></p>
            </div>

            <div class="flex flex-col-reverse gap-3 pt-1 sm:flex-row sm:flex-wrap">
                <button type="button" @click="cancel()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-demo-cancel>
                    {{ __('talenma.company_offer.demo_form.cancel') }}
                </button>
                <button type="button" @click="previousStep()" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50" data-demo-previous>
                    {{ __('talenma.company_offer.demo_form.previous') }}
                </button>
                <button type="submit" :disabled="submitting" class="inline-flex flex-1 justify-center rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:opacity-70" data-demo-submit>
                    <span x-show="!submitting">{{ __('talenma.company_offer.demo_form.submit') }}</span>
                    <span x-show="submitting" x-cloak>{{ __('talenma.auth.register_submitting') }}</span>
                </button>
            </div>
        </fieldset>
    </form>

    {{-- Étape 4 : remerciement --}}
    <div x-show="step === 4" x-cloak class="space-y-4" data-demo-step="4">
        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ __('talenma.company_offer.demo_form.thanks_title') }}</h3>
        <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_offer.demo_form.thanks_pricing') }}</p>
        <p class="text-sm leading-relaxed text-gray-600">{{ __('talenma.company_offer.demo_form.thanks_booking') }}</p>
        <button type="button" @click="close()" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            {{ __('talenma.company_offer.demo_form.close') }}
        </button>
    </div>
</div>
