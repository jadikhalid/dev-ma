@props([
    'sectors' => [],
    'sector' => '',
    'profession' => '',
    'accent' => 'emerald',
])

@php
    $focusRing = $accent === 'indigo'
        ? 'focus:border-indigo-500 focus:ring-indigo-500'
        : 'focus:border-emerald-500 focus:ring-emerald-500';
    $selectClass = 'mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm '.$focusRing.' py-2.5 pl-3 pr-10 appearance-none bg-white disabled:bg-gray-50';
@endphp

<div
    x-data="heroProgressiveSearch({
        sectors: @js($sectors),
        initialSector: @js($sector),
        initialProfession: @js($profession),
        keywordMode: false,
    })"
    class="grid sm:grid-cols-2 gap-4"
>
    <div class="relative">
        <x-input-label for="job-sector" :value="__('talenma.jobs.field_sector')" />
        <select
            id="job-sector"
            name="sector"
            x-model="sectorSlug"
            @change="onSectorChange()"
            class="{{ $selectClass }}"
            data-required
            data-required-message="{{ __('talenma.jobs.sector_required') }}"
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
        <x-input-label for="job-profession" :value="__('talenma.jobs.field_profession')" />
        <select
            id="job-profession"
            name="profession"
            x-model="professionSlug"
            @change="onProfessionChange()"
            class="{{ $selectClass }}"
            :disabled="!filteredProfessions.length"
            data-required
            data-required-message="{{ __('talenma.jobs.profession_required') }}"
        >
            <option value="">{{ __('talenma.talent.profession_placeholder') }}</option>
            <template x-for="professionOption in filteredProfessions" :key="professionOption.slug">
                <option :value="professionOption.slug" x-text="professionOption.name" :selected="professionOption.slug === professionSlug"></option>
            </template>
        </select>
        <svg class="pointer-events-none absolute right-3 top-[2.15rem] w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        <x-input-error :messages="$errors->get('profession')" class="mt-2" />
    </div>
</div>
