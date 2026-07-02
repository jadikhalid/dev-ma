@props([
    'sectors' => [],
    'sector' => '',
    'profession' => '',
    'specialization' => '',
    'titleInputId' => 'title',
])

@php
    $selectClass = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 pl-3 pr-10 appearance-none bg-white';
@endphp

<div
    x-data="heroProgressiveSearch({
        sectors: @js($sectors),
        initialSector: @js($sector),
        initialProfession: @js($profession),
        initialKeyword: @js($specialization),
        specializationAllLabel: @js(__('talenma.home.search_specialization_all')),
        specializationSelectProfessionLabel: @js(__('talenma.home.search_specialization_select_profession')),
        titleInputId: @js($titleInputId),
    })"
    class="space-y-4"
>
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="relative">
            <x-input-label for="profile-sector" :value="__('talenma.home.search_sector')" />
            <select
                id="profile-sector"
                name="sector"
                x-model="sectorSlug"
                @change="onSectorChange()"
                class="{{ $selectClass }}"
                required
            >
                <option value="">{{ __('talenma.talent.sector_placeholder') }}</option>
                @foreach ($sectors as $sectorOption)
                    <option value="{{ $sectorOption['slug'] }}" @selected($sector === $sectorOption['slug'])>
                        {{ $sectorOption['name'] }}
                    </option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-[2.15rem] w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <x-input-error :messages="$errors->get('sector')" class="mt-2" />
        </div>

        <div class="relative">
            <x-input-label for="profile-profession" :value="__('talenma.home.search_profession')" />
            <select
                id="profile-profession"
                name="profession"
                x-model="professionSlug"
                @change="onProfessionChange()"
                class="{{ $selectClass }}"
                :disabled="!filteredProfessions.length"
                required
            >
                <option value="">{{ __('talenma.talent.profession_placeholder') }}</option>
                <template x-for="profession in filteredProfessions" :key="profession.slug">
                    <option :value="profession.slug" x-text="profession.name" :selected="profession.slug === professionSlug"></option>
                </template>
            </select>
            <svg class="pointer-events-none absolute right-3 top-[2.15rem] w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            <x-input-error :messages="$errors->get('profession')" class="mt-2" />
        </div>
    </div>

    <div class="relative">
        <x-input-label for="profile-specialization" :value="__('talenma.home.search_specialization')" />
        <select
            id="profile-specialization"
            name="specialization"
            x-model="query"
            @change="suggestTitle()"
            class="{{ $selectClass }}"
            :disabled="!filteredSpecializations.length"
            required
        >
            <option value="" x-text="specializationPlaceholder"></option>
            <template x-for="item in filteredSpecializations" :key="item">
                <option :value="item" x-text="item" :selected="item === query"></option>
            </template>
        </select>
        <svg class="pointer-events-none absolute right-3 top-[2.15rem] w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.talent.specialization_hint') }}</p>
        <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
    </div>
</div>
