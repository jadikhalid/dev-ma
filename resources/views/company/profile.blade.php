<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.company.profile_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.company.profile_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6">
        @if (session('status') === 'company-profile-updated')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.company.updated') }}</div>
        @endif

        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <form method="POST" action="{{ route('company.profile.update') }}" class="space-y-6">@csrf
                <div>
                    <x-input-label for="company_name" :value="__('talenma.company.name')" />
                    <x-text-input id="company_name" name="company_name" class="mt-1 block w-full" :value="old('company_name', $profile->company_name)" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="sector" :value="__('talenma.company.sector')" />
                        <x-text-input id="sector" name="sector" class="mt-1 block w-full" :value="old('sector', $profile->sector)" placeholder="SaaS, E-commerce, Fintech…" />
                    </div>
                    <div>
                        <x-input-label for="employee_count" :value="__('talenma.company.employees')" />
                        <select id="employee_count" name="employee_count" class="mt-1 block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">—</option>
                            @foreach (['1-10', '11-50', '51-200', '200+'] as $size)
                                <option value="{{ $size }}" {{ old('employee_count', $profile->employee_count) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="country" :value="__('talenma.talent.country')" />
                        <x-text-input id="country" name="country" class="mt-1 block w-full" :value="old('country', $profile->country ?? __('talenma.common.france'))" required />
                    </div>
                    <div>
                        <x-input-label for="city" :value="__('talenma.talent.city')" />
                        <x-text-input id="city" name="city" class="mt-1 block w-full" :value="old('city', $profile->city)" />
                    </div>
                </div>

                <div>
                    <x-input-label for="website" :value="__('talenma.company.website')" />
                    <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $profile->website)" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('talenma.company.description')" />
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">{{ old('description', $profile->description) }}</textarea>
                </div>

                <div>
                    <x-input-label for="hiring_needs" :value="__('talenma.company.needs')" />
                    <textarea id="hiring_needs" name="hiring_needs" rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Stacks recherchées, type de mission, durée…">{{ old('hiring_needs', $profile->hiring_needs) }}</textarea>
                </div>

                <x-primary-button>{{ __('talenma.company.save') }}</x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
