{{-- Expects: $profile, $professionSectors, $sectorSlug, $employeeCountOptions, $countryOptions, $citiesByCountry --}}
@php
    $companyProfileUpdateUrl = $companyProfileUpdateUrl ?? route('company.profile.update');
    $formId = $formId ?? 'company-identity-card';
    $idPrefix = $idPrefix ?? '';
    $fromDashboard = (bool) ($fromDashboard ?? false);
    $variant = $variant ?? 'page';
    $isModal = $variant === 'modal';
@endphp

<form
    id="{{ $formId }}"
    method="POST"
    action="{{ $companyProfileUpdateUrl }}"
    @class([
        'relative space-y-6',
        'bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8' => ! $isModal,
        'p-5 sm:p-6' => $isModal,
    ])
    data-ajax
    data-loading-target="{{ $formId }}"
    data-error-message="{{ __('talenma.company.save_error') }}"
    novalidate
    x-data="talentLocationSelect({
        country: @js(old('country', $profile->country)),
        city: @js(old('city', $profile->city)),
        citiesByCountry: @js($citiesByCountry),
    })"
>
    @csrf
    <input type="hidden" name="section" value="identity">
    @if ($fromDashboard)
        <input type="hidden" name="from_dashboard" value="1">
    @endif

    <div @class(['flex items-start justify-between gap-3' => $isModal])>
        <div>
            <h3 @class([
                'font-bold text-gray-900',
                'text-lg' => ! $isModal,
                'text-base' => $isModal,
            ])>{{ __('talenma.company.section_identity') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_identity_desc') }}</p>
        </div>
        @if ($isModal)
            <button type="button" class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" x-on:click="$dispatch('close')" aria-label="{{ __('talenma.company.cancel') }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label :for="$idPrefix.'sector'" :value="__('talenma.company.sector')" />
            <select
                id="{{ $idPrefix }}sector"
                name="sector"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                required
                data-required
                data-required-message="{{ __('talenma.company.sector_required') }}"
            >
                <option value="">{{ __('talenma.auth.sector_placeholder') }}</option>
                @foreach ($professionSectors as $sectorOption)
                    <option value="{{ $sectorOption['slug'] }}" @selected($sectorSlug === $sectorOption['slug'])>{{ $sectorOption['name'] }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
        </div>
        <div>
            <x-input-label :for="$idPrefix.'employee_count'" :value="__('talenma.company.employees')" />
            <select id="{{ $idPrefix }}employee_count" name="employee_count" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                <option value="">—</option>
                @foreach ($employeeCountOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('employee_count', $profile->employee_count) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('employee_count')" class="mt-2" />
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label :for="$idPrefix.'country'" :value="__('talenma.talent.country')" />
            <select
                id="{{ $idPrefix }}country"
                name="country"
                x-model="country"
                @change="onCountryChange()"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                required
                data-required
                data-required-message="{{ __('talenma.company.country_required') }}"
            >
                <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                @foreach ($countryOptions as $code => $label)
                    <option value="{{ $code }}">{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>
        <div>
            <x-input-label :for="$idPrefix.'city'" :value="__('talenma.talent.city')" />
            <input
                type="hidden"
                name="city"
                :value="city"
                data-required
                data-required-message="{{ __('talenma.company.city_required') }}"
            >
            <select
                id="{{ $idPrefix }}city"
                x-model="city"
                :disabled="!country"
                class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm disabled:bg-gray-50 disabled:text-gray-400"
                required
            >
                <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                @foreach ($citiesByCountry as $countryCode => $cityList)
                    @foreach ($cityList as $cityOption)
                        <option
                            value="{{ $cityOption }}"
                            data-country="{{ $countryCode }}"
                            :hidden="country !== '{{ $countryCode }}'"
                            :disabled="country !== '{{ $countryCode }}'"
                        >{{ $cityOption }}</option>
                    @endforeach
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label :for="$idPrefix.'website'" :value="__('talenma.company.website')" />
        <x-text-input
            :id="$idPrefix.'website'"
            name="website"
            type="url"
            class="mt-1 block w-full"
            :value="old('website', $profile->website)"
            placeholder="https://..."
            data-url
            data-url-message="{{ __('talenma.company.website_invalid') }}"
        />
        <x-input-error :messages="$errors->get('website')" class="mt-2" />
    </div>

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        @if ($isModal)
            <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50" x-on:click="$dispatch('close')">{{ __('talenma.company.cancel') }}</button>
        @else
            <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</button>
        @endif
        <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
    </div>
</form>
