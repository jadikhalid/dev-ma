@props([
    'sectors' => [],
    'countries' => [],
])

@php
    $selectClass = 'w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 pl-3 pr-10 appearance-none bg-white';
@endphp

<div
    x-data="heroCompanySearch({
        sectors: @js($sectors),
        countries: @js($countries),
        maxKeywords: 3,
        searchUrl: @js(route('company-catalog-search')),
        keywordBlockedLabel: @js(__('talenma.home.company_search_keywords_blocked')),
        keywordPlaceholder: @js(__('talenma.home.company_search_keywords_placeholder')),
        keywordEmptyLabel: @js(__('talenma.home.search_skills_no_match')),
        keywordsMaxLabel: @js(__('talenma.home.search_skills_max_reached')),
        validationMessages: @js([
            'incomplete' => __('talenma.home.company_search_validation_incomplete'),
            'keywordsMax' => __('talenma.home.search_validation_keywords_max'),
        ]),
        drawerLabels: @js([
            'title' => __('talenma.home.company_search_drawer_title'),
            'subtitle' => __('talenma.home.company_search_drawer_subtitle'),
            'resultsSuffix' => __('talenma.home.company_search_drawer_results_suffix'),
            'loading' => __('talenma.home.company_search_drawer_loading'),
            'empty' => __('talenma.home.company_search_empty'),
            'emptyDesc' => __('talenma.home.company_search_empty_desc'),
            'error' => __('talenma.home.company_search_drawer_error'),
            'close' => __('talenma.common.close'),
        ]),
    })"
>
    <form
        class="p-3 sm:p-4 space-y-2.5"
        novalidate
        @submit="onSearchSubmit($event)"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="relative">
                <label for="company-search-sector" class="sr-only">{{ __('talenma.home.company_search_sector') }}</label>
                <select
                    id="company-search-sector"
                    x-model="sectorSlug"
                    @change="onSectorChange()"
                    class="{{ $selectClass }}"
                    required
                >
                    <option value="">{{ __('talenma.home.company_search_sector_required') }}</option>
                    @foreach ($sectors as $sectorOption)
                        <option value="{{ $sectorOption['slug'] }}">{{ $sectorOption['name'] }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            <div class="relative">
                <label for="company-search-country" class="sr-only">{{ __('talenma.home.company_search_country') }}</label>
                <select
                    id="company-search-country"
                    x-model="country"
                    class="{{ $selectClass }}"
                    :class="{ 'opacity-60 cursor-not-allowed bg-gray-50': !sectorSlug }"
                    :disabled="!sectorSlug"
                >
                    <option value="" x-text="sectorSlug ? @js(__('talenma.home.company_search_country_all')) : @js(__('talenma.home.company_search_country_blocked'))"></option>
                    <template x-for="item in countries" :key="item.value">
                        <option :value="item.value" x-text="item.label"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2.5 w-full">
            <div class="relative flex-1 min-w-0">
                <label for="company-keyword-input" class="sr-only">{{ __('talenma.home.company_search_keywords') }}</label>

                <div class="relative">
                    <div
                        class="flex h-[42px] items-center gap-2 overflow-x-auto rounded-lg border border-gray-200 bg-white px-3 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
                        :class="{ 'opacity-60 pointer-events-none bg-gray-50': !keywordsEnabled }"
                    >
                        <template x-for="keyword in selectedKeywords" :key="keyword">
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-800">
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

                        <input
                            id="company-keyword-input"
                            type="text"
                            x-model="keywordInput"
                            @input="onKeywordInput()"
                            @keydown="onKeywordKeydown($event)"
                            @focus="onKeywordFocus()"
                            @blur="onKeywordBlur()"
                            class="min-w-[8rem] flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0 disabled:cursor-not-allowed"
                            :placeholder="selectedKeywords.length ? '' : (keywordsEnabled ? keywordPlaceholder : keywordBlockedLabel)"
                            :disabled="!keywordsEnabled"
                            autocomplete="off"
                        >
                    </div>

                    <div
                        x-show="keywordsEnabled && keywordSuggestionsOpen"
                        x-cloak
                        class="absolute left-0 right-0 top-full z-30 mt-1 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
                    >
                        <p
                            x-show="keywordsAtMax"
                            class="px-3 py-2.5 text-sm text-amber-800 bg-amber-50"
                            x-text="keywordsMaxLabel"
                        ></p>

                        <ul
                            x-show="!keywordsAtMax && keywordInput.trim() && filteredAvailableKeywords.length"
                            class="max-h-48 overflow-y-auto py-1"
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

                        <p
                            x-show="!keywordsAtMax && keywordInput.trim() && !filteredAvailableKeywords.length"
                            class="px-3 py-2.5 text-sm text-gray-500"
                            x-text="keywordEmptyLabel"
                        ></p>
                    </div>
                </div>
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition shrink-0"
            >
                {{ __('talenma.home.company_search_submit') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>

    <x-company-search-drawer />
</div>
