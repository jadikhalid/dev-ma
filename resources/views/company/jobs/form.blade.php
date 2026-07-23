@php
    $isEdit = $job->exists;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">
            {{ $isEdit ? __('talenma.jobs.edit') : __('talenma.jobs.create') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form
            method="POST"
            action="{{ $isEdit ? route('company.jobs.update', $job) : route('company.jobs.store') }}"
            class="bg-white rounded-2xl border p-6 sm:p-8 space-y-5"
            x-data="talentLocationSelect({
                country: @js(old('location_country', $job->location_country ?: \App\Models\CompanyProfile::DEFAULT_COUNTRY)),
                city: @js(old('location_city', $job->location_city)),
                citiesByCountry: @js($citiesByCountry),
            })"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div>
                <x-input-label for="title" :value="__('talenma.jobs.field_title')" />
                <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $job->title)" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="description" :value="__('talenma.jobs.field_description')" />
                <textarea id="description" name="description" rows="8" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>{{ old('description', $job->description) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.jobs.description_hint') }}</p>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contract_type" :value="__('talenma.jobs.field_contract')" />
                <select id="contract_type" name="contract_type" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                    <option value="">—</option>
                    @foreach (\App\Models\JobPosting::CONTRACT_TYPES as $type)
                        <option value="{{ $type }}" @selected(old('contract_type', $job->contract_type) === $type)>{{ __('talenma.jobs.contract_'.$type) }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('contract_type')" class="mt-2" />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="location_country" :value="__('talenma.talent.country')" />
                    <select id="location_country" name="location_country" x-model="country" @change="onCountryChange()" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm">
                        <option value="">{{ __('talenma.talent.country_placeholder') }}</option>
                        @foreach ($countryOptions as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('location_country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="location_city" :value="__('talenma.talent.city')" />
                    <input type="hidden" name="location_city" :value="city">
                    <select id="location_city" x-model="city" :disabled="!country" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm disabled:bg-gray-50">
                        <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                        @foreach ($citiesByCountry as $countryCode => $cityList)
                            @foreach ($cityList as $cityOption)
                                <option value="{{ $cityOption }}" data-country="{{ $countryCode }}" :hidden="country !== '{{ $countryCode }}'" :disabled="country !== '{{ $countryCode }}'">{{ $cityOption }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('location_city')" class="mt-2" />
                </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="remote_ok" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" @checked(old('remote_ok', $job->remote_ok))>
                {{ __('talenma.jobs.field_remote') }}
            </label>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <a href="{{ $isEdit ? route('company.jobs.show', $job) : route('company.jobs.index') }}" class="inline-flex justify-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</a>
                <x-primary-button>{{ __('talenma.jobs.save') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
