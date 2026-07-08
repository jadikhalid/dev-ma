<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <x-company-logo :profile="$profile" size="md" />
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $profile->company_name ?: $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $profile->sector ?? '—' }}</p>
                @if ($profile->city || $profile->country)
                    <p class="mt-1 text-xs font-medium text-emerald-600">
                        {{ collect([$profile->city, $profile->country])->filter()->implode(', ') }}
                    </p>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        @if (session('status') === 'company-profile-updated' && session('updated_section'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                {{ __('talenma.company.section_updated.'.session('updated_section')) }}
            </div>
        @endif

        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-900 rounded-xl text-sm">
            {{ __('talenma.company.tip') }}
        </div>

        {{-- Section A : Identité --}}
        <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="section" value="identity">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_identity') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_identity_desc') }}</p>
            </div>

            <div class="flex flex-col sm:flex-row items-start gap-5">
                <x-company-logo :profile="$profile" size="xl" />
                <div class="flex-1 w-full space-y-3">
                    <div>
                        <x-input-label for="logo" :value="__('talenma.company.logo')" />
                        <input
                            id="logo"
                            name="logo"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100"
                        >
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.logo_hint') }}</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>
                    @if ($profile->logo_path)
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            {{ __('talenma.company.logo_remove') }}
                        </label>
                    @endif
                </div>
            </div>

            <div>
                <x-input-label for="company_name" :value="__('talenma.company.name')" />
                <x-text-input id="company_name" name="company_name" class="mt-1 block w-full" :value="old('company_name', $profile->company_name)" required />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="sector" :value="__('talenma.company.sector')" />
                    <select id="sector" name="sector" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
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
                    <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $profile->country ?? __('talenma.common.france'))" required />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="city" :value="__('talenma.talent.city')" />
                    <select id="city" name="city" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required>
                        <option value="">{{ __('talenma.talent.city_placeholder') }}</option>
                        @foreach ($europeanCities as $cityOption)
                            <option value="{{ $cityOption }}" @selected(old('city', $profile->city) === $cityOption)>{{ $cityOption }}</option>
                        @endforeach
                        @if ($profile->city && ! in_array($profile->city, $europeanCities, true))
                            <option value="{{ $profile->city }}" selected>{{ $profile->city }}</option>
                        @endif
                    </select>
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="website" :value="__('talenma.company.website')" />
                <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $profile->website)" placeholder="https://..." />
                <x-input-error :messages="$errors->get('website')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <a href="{{ route('company.profile.edit') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</a>
                <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
            </div>
        </form>

        {{-- Section B : Présentation --}}
        <form method="POST" action="{{ route('company.profile.update') }}" class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="section" value="presentation">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_presentation') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_presentation_desc') }}</p>
            </div>

            <div>
                <x-input-label for="description" :value="__('talenma.company.description')" />
                <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required placeholder="{{ __('talenma.company.description_placeholder') }}">{{ old('description', $profile->description) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.description_hint') }}</p>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <a href="{{ route('company.profile.edit') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</a>
                <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
            </div>
        </form>

        {{-- Section C : Besoins de recrutement --}}
        <form method="POST" action="{{ route('company.profile.update') }}" class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="section" value="hiring">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_hiring') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_hiring_desc') }}</p>
            </div>

            <div>
                <x-input-label for="hiring_needs" :value="__('talenma.company.needs')" />
                <textarea id="hiring_needs" name="hiring_needs" rows="5" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm" required placeholder="{{ __('talenma.company.needs_placeholder') }}">{{ old('hiring_needs', $profile->hiring_needs) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.company.needs_hint') }}</p>
                <x-input-error :messages="$errors->get('hiring_needs')" class="mt-2" />
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <a href="{{ route('company.profile.edit') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</a>
                <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
            </div>
        </form>

        {{-- Section D : Contact recrutement --}}
        <form method="POST" action="{{ route('company.profile.update') }}" class="bg-white rounded-2xl border p-6 sm:p-8 space-y-6">
            @csrf
            <input type="hidden" name="section" value="contact">

            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ __('talenma.company.section_contact') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.company.section_contact_desc') }}</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="representative_name" :value="__('talenma.auth.representative_name')" />
                    <x-text-input id="representative_name" name="representative_name" class="mt-1 block w-full" :value="old('representative_name', $profile->representative_name)" required />
                    <x-input-error :messages="$errors->get('representative_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="representative_email" :value="__('talenma.auth.representative_email')" />
                    <x-text-input id="representative_email" name="representative_email" type="email" class="mt-1 block w-full" :value="old('representative_email', $profile->representative_email)" required />
                    <x-input-error :messages="$errors->get('representative_email')" class="mt-2" />
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" :value="__('talenma.talent.phone')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $profile->phone)" placeholder="+33 6 00 00 00 00" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="linkedin_url" :value="__('talenma.company.linkedin')" />
                    <x-text-input id="linkedin_url" name="linkedin_url" type="url" class="mt-1 block w-full" :value="old('linkedin_url', $profile->linkedin_url)" placeholder="https://linkedin.com/company/..." />
                    <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                <a href="{{ route('company.profile.edit') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.company.cancel') }}</a>
                <x-primary-button class="justify-center">{{ __('talenma.company.save_section') }}</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
