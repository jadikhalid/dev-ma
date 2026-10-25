{{-- Expects: $user, $profile, $memberships, $professionSectors, $sectorSlug, $employeeCountOptions, $countryOptions, $citiesByCountry --}}
@php
    $companyProfileUpdateUrl = $companyProfileUpdateUrl ?? route('company.profile.update');
@endphp
@if (session('status') === 'company-profile-updated' && session('updated_section'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
        {{ __('talenma.company.section_updated.'.session('updated_section')) }}
    </div>
@endif

{{-- Bannière publique : réservée à l’admin / modérateur (édition fiche gérée) --}}
@if (! empty($isAdminProfileEdit))
<form
    id="company-banner-card"
    method="POST"
    action="{{ $companyProfileUpdateUrl }}"
    enctype="multipart/form-data"
    class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5"
    data-ajax
    data-loading-target="company-banner-card"
    data-error-message="{{ __('talenma.company.save_error') }}"
    novalidate
    x-data="{
        previewUrl: @js($profile->bannerUrl()),
        removing: false,
        onFileChange(event) {
            const file = event.target.files?.[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
                this.removing = false;
            }
        },
    }"
    data-company-banner-form
>
    @csrf
    <input type="hidden" name="section" value="banner">

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_banner') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_banner_desc') }}</p>
    </div>

    <div class="relative aspect-[1440/450] overflow-hidden rounded-xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500 ring-1 ring-gray-200">
        <img
            x-show="previewUrl && !removing"
            x-cloak
            :src="previewUrl"
            alt=""
            class="absolute inset-0 h-full w-full object-cover object-center"
        >
        <p
            x-show="!previewUrl || removing"
            class="absolute inset-0 flex items-center justify-center px-4 text-center text-sm font-medium text-white/90"
        >{{ __('talenma.company.banner_empty') }}</p>
    </div>

    <div>
        <x-input-label for="banner" :value="__('talenma.company.banner')" />
        <input
            id="banner"
            name="banner"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            @change="onFileChange($event)"
            class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
        >
        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.banner_hint') }}</p>
        <x-input-error :messages="$errors->get('banner')" class="mt-2" />
    </div>

    @if ($profile->banner_path)
        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
            <input
                type="checkbox"
                name="remove_banner"
                value="1"
                x-model="removing"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            >
            {{ __('talenma.company.banner_remove') }}
        </label>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</button>
        <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
    </div>
</form>
@endif

{{-- Section A : Identité --}}
<form
    id="company-identity-card"
    method="POST"
    action="{{ $companyProfileUpdateUrl }}"
    class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6"
    data-ajax
    data-loading-target="company-identity-card"
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

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_identity') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_identity_desc') }}</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="sector" :value="__('talenma.company.sector')" />
            <select
                id="sector"
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
            <x-input-label for="employee_count" :value="__('talenma.company.employees')" />
            <select id="employee_count" name="employee_count" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
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
            <x-input-label for="country" :value="__('talenma.talent.country')" />
            <select
                id="country"
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
            <x-input-label for="city" :value="__('talenma.talent.city')" />
            <input
                type="hidden"
                name="city"
                :value="city"
                data-required
                data-required-message="{{ __('talenma.company.city_required') }}"
            >
            <select
                id="city"
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
        <x-input-label for="website" :value="__('talenma.company.website')" />
        <x-text-input
            id="website"
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
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</button>
        <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
    </div>
</form>

{{-- Section B : Présentation --}}
<form
    id="company-presentation-card"
    method="POST"
    action="{{ $companyProfileUpdateUrl }}"
    class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6"
    data-ajax
    data-loading-target="company-presentation-card"
    data-error-message="{{ __('talenma.company.save_error') }}"
    novalidate
>
    @csrf
    <input type="hidden" name="section" value="presentation">

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_presentation') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_presentation_desc') }}</p>
    </div>

    <div>
        <x-input-label for="description" :value="__('talenma.company.description')" />
        <textarea
            id="description"
            name="description"
            rows="5"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
            required
            data-required
            data-required-message="{{ __('talenma.company.description_required') }}"
            data-min-length="50"
            data-min-length-message="{{ __('talenma.company.description_min') }}"
            placeholder="{{ __('talenma.company.description_placeholder') }}"
        >{{ old('description', $profile->description) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.description_hint') }}</p>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</button>
        <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
    </div>
</form>

{{-- Section C : Besoins de candidature --}}
<form
    id="company-hiring-card"
    method="POST"
    action="{{ $companyProfileUpdateUrl }}"
    class="relative bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6"
    data-ajax
    data-loading-target="company-hiring-card"
    data-error-message="{{ __('talenma.company.save_error') }}"
    novalidate
>
    @csrf
    <input type="hidden" name="section" value="hiring">

    <div>
        <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_hiring') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_hiring_desc') }}</p>
    </div>

    <div>
        <x-input-label for="hiring_needs" :value="__('talenma.company.needs')" />
        <textarea
            id="hiring_needs"
            name="hiring_needs"
            rows="5"
            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
            required
            data-required
            data-required-message="{{ __('talenma.company.hiring_required') }}"
            data-min-length="20"
            data-min-length-message="{{ __('talenma.company.hiring_min') }}"
            placeholder="{{ __('talenma.company.needs_placeholder') }}"
        >{{ old('hiring_needs', $profile->hiring_needs) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.needs_hint') }}</p>
        <x-input-error :messages="$errors->get('hiring_needs')" class="mt-2" />
    </div>

    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
        <button type="button" data-reset class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</button>
        <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
    </div>
</form>
