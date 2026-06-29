@props([
    'sectors' => [],
    'keyword' => '',
    'sector' => '',
    'profession' => '',
    'city' => '',
])

@php
    $cities = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'];
    $selectClass = 'w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 pl-3 pr-10 appearance-none bg-white';
@endphp

<div
    x-data="heroProgressiveSearch({
        sectors: @js($sectors),
        initialKeyword: @js($keyword),
        initialSector: @js($sector),
        initialProfession: @js($profession),
        specializationAllLabel: @js(__('talenma.home.search_specialization_all')),
        specializationSelectProfessionLabel: @js(__('talenma.home.search_specialization_select_profession')),
    })"
>
    <form method="GET" action="{{ route('company.search') }}" class="p-3 sm:p-4 space-y-2.5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="relative">
                <label for="hero-sector" class="sr-only">{{ __('talenma.home.search_sector') }}</label>
                <select
                    id="hero-sector"
                    name="sector"
                    x-model="sectorSlug"
                    @change="onSectorChange()"
                    class="{{ $selectClass }}"
                >
                    <option value="">{{ __('talenma.home.search_sector_all') }}</option>
                    @foreach ($sectors as $sectorOption)
                        <option value="{{ $sectorOption['slug'] }}" @selected($sector === $sectorOption['slug'])>
                            {{ $sectorOption['name'] }}
                        </option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="relative">
                <label for="hero-profession" class="sr-only">{{ __('talenma.home.search_profession') }}</label>
                <select
                    id="hero-profession"
                    name="profession"
                    x-model="professionSlug"
                    @change="onProfessionChange()"
                    class="{{ $selectClass }}"
                    :disabled="!filteredProfessions.length"
                >
                    <option value="">{{ __('talenma.home.search_profession_all') }}</option>
                    <template x-for="profession in filteredProfessions" :key="profession.slug">
                        <option :value="profession.slug" x-text="profession.name" :selected="profession.slug === professionSlug"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-2.5 w-full">
            <div class="relative flex-1 min-w-0">
                <label for="hero-keyword" class="sr-only">{{ __('talenma.home.search_specialization') }}</label>
                <select
                    id="hero-keyword"
                    name="keyword"
                    x-model="query"
                    class="{{ $selectClass }}"
                    :disabled="!filteredSpecializations.length"
                >
                    <option value="" x-text="specializationPlaceholder"></option>
                    <template x-for="specialization in filteredSpecializations" :key="specialization">
                        <option :value="specialization" x-text="specialization" :selected="specialization === query"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="lg:w-52 shrink-0 relative">
                <label for="hero-city" class="sr-only">{{ __('talenma.home.search_location') }}</label>
                <select id="hero-city" name="city" class="{{ $selectClass }}">
                    <option value="">{{ __('talenma.home.search_location') }}</option>
                    @foreach ($cities as $cityOption)
                        <option value="{{ $cityOption }}" @selected($city === $cityOption)>{{ $cityOption }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shrink-0"
            >
                {{ __('talenma.home.search_submit') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>
</div>
