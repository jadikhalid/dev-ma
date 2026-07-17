@props([
    'sectors' => [],
    'keyword' => '',
    'sector' => '',
    'profession' => '',
    'canViewProfiles' => false,
])

@php
    $selectClass = 'w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 pl-3 pr-10 appearance-none bg-white';
@endphp

<div
    x-data="heroProgressiveSearch({
        sectors: @js($sectors),
        initialKeyword: @js($keyword),
        initialSector: @js($sector),
        initialProfession: @js($profession),
        keywordMode: true,
        freeKeywords: false,
        requireCompleteSearch: true,
        maxKeywords: 3,
        canViewProfiles: @js((bool) $canViewProfiles),
        searchUrl: @js(route('talent-search')),
        specializationSelectProfessionLabel: @js(__('talenma.home.search_skills_blocked_placeholder')),
        keywordPlaceholder: @js(__('talenma.home.search_skills_add_placeholder')),
        keywordEmptyLabel: @js(__('talenma.home.search_skills_no_match')),
        keywordsMaxLabel: @js(__('talenma.home.search_skills_max_reached')),
        validationMessages: @js([
            'incomplete' => __('talenma.home.search_validation_incomplete'),
            'keywordsMax' => __('talenma.home.search_validation_keywords_max'),
        ]),
        drawerLabels: @js([
            'title' => __('talenma.home.search_drawer_title'),
            'subtitle' => $canViewProfiles
                ? __('talenma.home.search_drawer_subtitle_company')
                : __('talenma.home.search_drawer_subtitle'),
            'resultsSuffix' => __('talenma.home.search_drawer_results_suffix'),
            'loading' => __('talenma.home.search_drawer_loading'),
            'empty' => __('talenma.talents.empty'),
            'emptyDesc' => __('talenma.talents.empty_desc'),
            'error' => __('talenma.home.search_drawer_error'),
            'close' => __('talenma.common.close'),
            'viewProfile' => __('talenma.talents.view'),
            'viewCv' => __('talenma.talents.view_cv'),
            'filterExperience' => __('talenma.home.search_drawer_filter_experience'),
            'filterStatus' => __('talenma.home.search_drawer_filter_status'),
            'filterAll' => __('talenma.home.search_drawer_filter_all'),
            'filterExp01' => __('talenma.home.search_drawer_filter_exp_0_1'),
            'filterExp15' => __('talenma.home.search_drawer_filter_exp_1_5'),
            'filterExp510' => __('talenma.home.search_drawer_filter_exp_5_10'),
            'filterExp10Plus' => __('talenma.home.search_drawer_filter_exp_10_plus'),
            'filterEmpty' => __('talenma.home.search_drawer_filter_empty'),
            'statusAvailable' => __('talenma.talent.available'),
            'statusBusy' => __('talenma.talent.busy'),
            'statusListening' => __('talenma.talent.listening'),
            'anonymousTalent' => __('talenma.home.search_drawer_anonymous'),
            'lockedTitle' => __('talenma.home.search_drawer_locked_title'),
            'lockedDesc' => __('talenma.home.search_drawer_locked_desc'),
            'registerCompany' => __('talenma.home.search_drawer_register_company'),
            'loginCompany' => __('talenma.home.search_drawer_login_company'),
        ]),
    })"
>
    <form
        method="GET"
        action="{{ route('company.search') }}"
        class="p-3 sm:p-4 space-y-2.5"
        novalidate
        @submit="onSearchSubmit($event)"
    >
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
            <div class="relative">
                <label for="hero-sector" class="sr-only">{{ __('talenma.home.search_sector') }}</label>
                <select
                    id="hero-sector"
                    name="sector"
                    x-model="sectorSlug"
                    @change="onSectorChange()"
                    class="{{ $selectClass }}"
                    required
                >
                    <option value="">{{ __('talenma.home.search_sector_required') }}</option>
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
                    :class="{ 'opacity-60 cursor-not-allowed bg-gray-50': !professionsEnabled }"
                    :disabled="!professionsEnabled"
                    required
                >
                    <option value="" x-text="professionsEnabled ? @js(__('talenma.home.search_profession_required')) : @js(__('talenma.home.search_profession_blocked'))"></option>
                    <template x-for="profession in filteredProfessions" :key="profession.slug">
                        <option :value="profession.slug" x-text="profession.name" :selected="profession.slug === professionSlug"></option>
                    </template>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2.5 w-full">
            <div class="relative flex-1 min-w-0">
                <label for="hero-keyword-input" class="sr-only">{{ __('talenma.home.search_skills') }}</label>
                <input type="hidden" name="keyword" :value="specializationValue">

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
                            id="hero-keyword-input"
                            type="text"
                            x-model="keywordInput"
                            @input="onKeywordInput()"
                            @keydown="onKeywordKeydown($event)"
                            @focus="onKeywordFocus()"
                            @blur="onKeywordBlur()"
                            class="min-w-[8rem] flex-1 border-0 bg-transparent p-0 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-0 disabled:cursor-not-allowed"
                            :placeholder="selectedKeywords.length ? '' : (keywordsEnabled ? keywordPlaceholder : specializationSelectProfessionLabel)"
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
                {{ __('talenma.home.search_submit') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </form>

    <x-talent-search-drawer />
</div>
