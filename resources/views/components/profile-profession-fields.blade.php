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
        keywordMode: true,
        specializationSelectProfessionLabel: @js(__('talenma.talent.specialization_select_sector')),
        keywordPlaceholder: @js(__('talenma.talent.specialization_keyword_placeholder')),
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

    <div>
        <x-input-label for="profile-specialization-input" :value="__('talenma.home.search_specialization')" />

        <input type="hidden" name="specialization" :value="specializationValue" required>

        <div
            class="mt-1 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
            :class="{ 'opacity-60 pointer-events-none': !filteredSpecializations.length }"
        >
            <div class="flex flex-wrap gap-2" x-show="selectedKeywords.length">
                <template x-for="keyword in selectedKeywords" :key="keyword">
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-800">
                        <span x-text="keyword"></span>
                        <button
                            type="button"
                            class="rounded-full p-0.5 text-indigo-500 hover:bg-indigo-200 hover:text-indigo-900"
                            @click="removeKeyword(keyword)"
                            :aria-label="`{{ __('talenma.talent.specialization_remove_keyword') }} ${keyword}`"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                </template>
            </div>

            <div class="relative mt-1">
                <input
                    id="profile-specialization-input"
                    type="text"
                    x-model="keywordInput"
                    @input="onKeywordInput()"
                    @keydown="onKeywordKeydown($event)"
                    @focus="onKeywordInput()"
                    @blur="setTimeout(() => keywordSuggestionsOpen = false, 150)"
                    class="block w-full border-0 p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0"
                    :placeholder="filteredSpecializations.length ? keywordPlaceholder : specializationSelectProfessionLabel"
                    :disabled="!filteredSpecializations.length"
                    autocomplete="off"
                >

                <ul
                    x-show="keywordSuggestionsOpen && filteredAvailableKeywords.length"
                    x-cloak
                    class="absolute left-0 right-0 z-20 mt-2 max-h-48 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
                >
                    <template x-for="item in filteredAvailableKeywords" :key="item">
                        <li>
                            <button
                                type="button"
                                class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-800"
                                @mousedown.prevent="addKeyword(item)"
                                x-text="item"
                            ></button>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div class="mt-3" x-show="unselectedSpecializations.length">
            <p class="text-xs font-medium text-gray-500">{{ __('talenma.talent.specialization_suggestions') }}</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <template x-for="item in unselectedSpecializations" :key="item">
                    <button
                        type="button"
                        class="rounded-full border border-gray-200 px-2.5 py-1 text-xs text-gray-700 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-800"
                        @click="addKeyword(item)"
                        x-text="`+ ${item}`"
                    ></button>
                </template>
            </div>
        </div>

        <p class="mt-2 text-xs text-gray-500">{{ __('talenma.talent.specialization_hint') }}</p>
        <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
    </div>
</div>
