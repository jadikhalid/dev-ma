<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.talents.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.talents.subtitle') }}</p>
        </div>
    </x-slot>

    @php
        $selectClass = 'mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
        $initialTalents = $talents->getCollection()->map(function ($talent) {
            $profile = $talent->profile;
            $experienceYears = $profile?->experience_years;
            $isPublic = $profile?->isPublic() ?? false;

            return [
                'id' => $talent->id,
                'name' => $profile?->visibleDisplayName($talent) ?? $talent->publicDisplayName(),
                'avatar_url' => $profile?->visibleAvatarUrl($talent),
                'initials' => $talent->initials(),
                'is_public' => $isPublic,
                'employer_label' => $profile?->employerLabel(),
                'profession_label' => $profile?->professionLabel(),
                'sector_label' => $profile?->sectorLabel(),
                'specialization' => $profile?->specialization,
                'city' => $isPublic ? $profile?->city : null,
                'country' => $profile?->country,
                'skills' => $profile?->skills ?? [],
                'experience_years' => $experienceYears,
                'experience_label' => $experienceYears !== null
                    ? __('talenma.talents.experience', ['years' => $experienceYears])
                    : null,
                'profile_url' => route('company.talent.show', $talent),
                'recruitment_url' => route('recruitment.create', $talent).'?mode=intermediary',
            ];
        })->values();
    @endphp

    <div
        class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8"
        x-data="companyTalentCatalog({
            sectors: @js($sectors),
            searchUrl: @js(route('company.search')),
            initialTalents: @js($initialTalents),
            initialMeta: @js([
                'total' => $talents->total(),
                'current_page' => $talents->currentPage(),
                'last_page' => $talents->lastPage(),
                'per_page' => $talents->perPage(),
                'from' => $talents->firstItem(),
                'to' => $talents->lastItem(),
            ]),
            initialSector: @js($filters['sector']),
            initialProfession: @js($filters['profession']),
            initialExperience: @js($filters['experience']),
            initialStatus: @js($filters['status']),
            initialKeyword: @js($filters['keyword']),
            labels: @js([
                'found' => __('talenma.talents.found', ['count' => ':count']),
                'loading' => __('talenma.home.search_drawer_loading'),
                'empty' => __('talenma.talents.empty'),
                'emptyDesc' => __('talenma.talents.empty_desc'),
                'error' => __('talenma.home.search_drawer_error'),
                'profileError' => __('talenma.home.search_drawer_error'),
                'view' => __('talenma.talents.view'),
                'intermediary' => __('talenma.talents.intermediary'),
                'professionAll' => __('talenma.home.search_profession_all'),
                'professionBlocked' => __('talenma.home.search_profession_blocked'),
                'keywordPlaceholder' => __('talenma.home.search_skills_add_placeholder'),
                'keywordBlocked' => __('talenma.home.search_skills_blocked_placeholder'),
                'keywordEmpty' => __('talenma.home.search_skills_no_match'),
                'keywordsMax' => __('talenma.home.search_skills_max_reached'),
                'prev' => __('talenma.common.previous'),
                'next' => __('talenma.common.next'),
                'composeError' => __('talenma.inbox.error'),
                'composeMinBody' => __('talenma.inbox.compose_min_body'),
            ]),
            composeUrl: @js(route('inbox.store')),
            csrf: @js(csrf_token()),
        })"
    >
        <div class="bg-white rounded-2xl border p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <x-input-label for="catalog-sector" :value="__('talenma.home.search_sector')" />
                    <select
                        id="catalog-sector"
                        x-model="sectorSlug"
                        @change="onSectorChange()"
                        class="{{ $selectClass }}"
                    >
                        <option value="">{{ __('talenma.home.search_sector_all') }}</option>
                        @foreach ($sectors as $sectorOption)
                            <option value="{{ $sectorOption['slug'] }}">{{ $sectorOption['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="catalog-profession" :value="__('talenma.home.search_profession')" />
                    <select
                        id="catalog-profession"
                        x-model="professionSlug"
                        @change="onProfessionChange()"
                        :disabled="! professionsEnabled"
                        class="{{ $selectClass }} disabled:bg-gray-50 disabled:text-gray-400"
                    >
                        <option value="" x-text="professionPlaceholder"></option>
                        <template x-for="profession in filteredProfessions" :key="profession.slug">
                            <option :value="profession.slug" x-text="profession.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <x-input-label for="catalog-experience" :value="__('talenma.home.search_drawer_filter_experience')" />
                    <select
                        id="catalog-experience"
                        x-model="experience"
                        @change="refreshResults()"
                        class="{{ $selectClass }}"
                    >
                        <option value="all">{{ __('talenma.home.search_drawer_filter_all') }}</option>
                        <option value="0-1">{{ __('talenma.home.search_drawer_filter_exp_0_1') }}</option>
                        <option value="1-5">{{ __('talenma.home.search_drawer_filter_exp_1_5') }}</option>
                        <option value="5-10">{{ __('talenma.home.search_drawer_filter_exp_5_10') }}</option>
                        <option value="10+">{{ __('talenma.home.search_drawer_filter_exp_10_plus') }}</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="catalog-status" :value="__('talenma.home.search_drawer_filter_status')" />
                    <select
                        id="catalog-status"
                        x-model="status"
                        @change="refreshResults()"
                        class="{{ $selectClass }}"
                    >
                        <option value="all">{{ __('talenma.home.search_drawer_filter_all') }}</option>
                        <option value="{{ \App\Models\Profile::STATUS_AVAILABLE }}">{{ __('talenma.talent.available') }}</option>
                        <option value="{{ \App\Models\Profile::STATUS_BUSY }}">{{ __('talenma.talent.busy') }}</option>
                        <option value="{{ \App\Models\Profile::STATUS_LISTENING }}">{{ __('talenma.talent.listening') }}</option>
                    </select>
                </div>

                <div class="relative">
                    <x-input-label for="catalog-keywords" :value="__('talenma.home.search_skills')" />
                    <div class="mt-1 min-h-[42px] rounded-lg border border-gray-300 bg-white px-2 py-1.5 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500"
                         :class="{ 'bg-gray-50': ! keywordsEnabled }"
                    >
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="(keyword, index) in selectedKeywords" :key="keyword">
                                <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">
                                    <span x-text="keyword"></span>
                                    <button
                                        type="button"
                                        class="text-indigo-400 hover:text-indigo-700"
                                        @click="removeKeyword(index)"
                                        :aria-label="@js(__('talenma.talent.specialization_remove_keyword'))"
                                    >&times;</button>
                                </span>
                            </template>
                            <input
                                id="catalog-keywords"
                                type="text"
                                x-model="keywordInput"
                                @focus="keywordSuggestionsOpen = true"
                                @input="keywordSuggestionsOpen = true"
                                @keydown.enter.prevent="addFirstKeywordSuggestion()"
                                @keydown.escape="keywordSuggestionsOpen = false"
                                @blur="hideKeywordSuggestionsSoon()"
                                :disabled="! keywordsEnabled || keywordsAtMax"
                                :placeholder="keywordsEnabled ? (keywordsAtMax ? labels.keywordsMax : labels.keywordPlaceholder) : labels.keywordBlocked"
                                class="min-w-[8rem] flex-1 border-0 bg-transparent p-1 text-sm focus:ring-0 disabled:cursor-not-allowed disabled:text-gray-400"
                            />
                        </div>
                    </div>
                    <div
                        x-show="keywordSuggestionsOpen && keywordsEnabled && ! keywordsAtMax && keywordInput.trim()"
                        x-cloak
                        class="absolute z-20 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                    >
                        <template x-if="filteredAvailableKeywords.length === 0">
                            <p class="px-3 py-2 text-sm text-gray-500" x-text="labels.keywordEmpty"></p>
                        </template>
                        <template x-for="suggestion in filteredAvailableKeywords" :key="suggestion">
                            <button
                                type="button"
                                class="block w-full px-3 py-2 text-left text-sm hover:bg-indigo-50"
                                @mousedown.prevent="addKeyword(suggestion)"
                                x-text="suggestion"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <p class="text-sm text-gray-600" x-text="foundLabel"></p>
            <div x-show="loading" class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="h-4 w-4 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="labels.loading"></span>
            </div>
        </div>

        <div
            x-show="error"
            x-cloak
            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
            x-text="error"
        ></div>

        <div class="grid md:grid-cols-2 gap-6" :class="{ 'opacity-60 pointer-events-none': loading }">
            <template x-if="! loading && ! error && talents.length === 0">
                <div class="col-span-2 text-center py-16 text-gray-500">
                    <p class="text-lg font-medium" x-text="labels.empty"></p>
                    <p class="text-sm mt-1" x-text="labels.emptyDesc"></p>
                </div>
            </template>

            <template x-for="talent in talents" :key="talent.id">
                <div class="bg-white rounded-2xl border p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="font-bold text-lg text-gray-900" x-text="talent.name"></h3>
                                <p class="mt-0.5 text-sm font-medium text-indigo-600">
                                    <span x-text="talent.profession_label"></span>
                                    <span x-show="talent.profession_label && talent.sector_label"> - </span>
                                    <span x-text="talent.sector_label"></span>
                                </p>
                                <p
                                    class="mt-1 text-xs text-gray-500"
                                    x-show="talent.employer_label"
                                    x-text="'{{ __('talenma.talent.employer') }} : ' + talent.employer_label"
                                ></p>
                            </div>
                            <template x-if="talent.avatar_url">
                                <img
                                    :src="talent.avatar_url"
                                    :alt="talent.name"
                                    class="h-16 w-16 shrink-0 rounded-full object-cover ring-1 ring-gray-200"
                                >
                            </template>
                            <template x-if="!talent.avatar_url">
                                <span
                                    class="inline-flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700"
                                    x-text="talent.initials"
                                    aria-hidden="true"
                                ></span>
                            </template>
                        </div>
                        <p
                            class="mt-2 text-xs text-gray-500"
                            x-show="talent.city || talent.country"
                            x-text="locationLine(talent)"
                        ></p>
                        <p
                            class="mt-2 inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700"
                            x-show="talent.experience_label"
                            x-text="talent.experience_label"
                        ></p>
                        <div class="mt-3 flex flex-wrap gap-1" x-show="keySkills(talent).length">
                            <template x-for="skill in keySkills(talent)" :key="skill">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded" x-text="skill"></span>
                            </template>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t">
                        <a
                            :href="talent.profile_url"
                            @click.prevent="openProfile(talent.profile_url)"
                            class="block w-full text-center px-3 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50"
                            x-text="labels.view"
                        ></a>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="meta.last_page > 1" class="flex items-center justify-center gap-2" x-cloak>
            <button
                type="button"
                class="rounded-lg border px-3 py-1.5 text-sm disabled:opacity-40"
                :disabled="meta.current_page <= 1 || loading"
                @click="goToPage(meta.current_page - 1)"
                x-text="labels.prev"
            ></button>
            <span class="text-sm text-gray-600" x-text="meta.current_page + ' / ' + meta.last_page"></span>
            <button
                type="button"
                class="rounded-lg border px-3 py-1.5 text-sm disabled:opacity-40"
                :disabled="meta.current_page >= meta.last_page || loading"
                @click="goToPage(meta.current_page + 1)"
                x-text="labels.next"
            ></button>
        </div>

        <div
            x-show="profileDrawerOpen"
            x-cloak
            class="fixed inset-0 z-[70] h-screen"
            style="margin: 0; height: 100vh; min-height: 100vh; max-height: 100vh;"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('talenma.talent.profile_title') }}"
            @keydown.escape.window="closeProfile()"
        >
            <div
                x-show="profileDrawerOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/45"
                @click="closeProfile()"
            ></div>

            <aside
                x-show="profileDrawerOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed right-0 top-0 bottom-0 flex h-screen min-h-screen max-h-screen w-full max-w-2xl flex-col bg-white shadow-2xl"
                style="height: 100vh; min-height: 100vh; max-height: 100vh;"
                @click.stop
            >
                <div class="flex shrink-0 items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                        {{ __('talenma.talent.profile_title') }}
                    </p>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                        aria-label="{{ __('talenma.common.close') }}"
                        @click="closeProfile()"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-7">
                    <div x-show="profileLoading" class="flex items-center justify-center gap-3 py-20 text-sm text-gray-500">
                        <svg class="h-5 w-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 3.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('talenma.home.search_drawer_loading') }}</span>
                    </div>

                    <div
                        x-show="!profileLoading && profileError"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                        x-text="profileError"
                    ></div>

                    <template x-if="!profileLoading && selectedProfile">
                        <div>
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <h2 class="text-2xl font-bold text-gray-900" x-text="selectedProfile.name"></h2>
                                    <p class="mt-1 text-sm font-medium text-indigo-600">
                                        <span x-text="selectedProfile.profession_label"></span>
                                        <span x-show="selectedProfile.profession_label && selectedProfile.sector_label"> - </span>
                                        <span x-text="selectedProfile.sector_label"></span>
                                    </p>
                                    <p
                                        class="mt-1 text-sm text-gray-500"
                                        x-show="selectedProfile.employer_label"
                                        x-text="'{{ __('talenma.talent.employer') }} : ' + selectedProfile.employer_label"
                                    ></p>
                                </div>
                                <div class="flex shrink-0 flex-col items-end gap-2">
                                    <template x-if="selectedProfile.avatar_url">
                                        <img
                                            :src="selectedProfile.avatar_url"
                                            :alt="selectedProfile.name"
                                            class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200"
                                        >
                                    </template>
                                    <template x-if="!selectedProfile.avatar_url">
                                        <span
                                            class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-xl font-bold text-indigo-700"
                                            x-text="selectedProfile.initials"
                                            aria-hidden="true"
                                        ></span>
                                    </template>
                                    <span
                                        x-show="selectedProfile.availability_label"
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="profileStatusClass(selectedProfile.availability_tone)"
                                        x-text="selectedProfile.availability_label"
                                    ></span>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap gap-2 text-sm">
                                <span
                                    x-show="selectedProfile.city || selectedProfile.country"
                                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-gray-700"
                                    x-text="'📍 ' + locationLine(selectedProfile)"
                                ></span>
                                <span
                                    x-show="selectedProfile.experience_label"
                                    class="rounded-lg bg-emerald-50 px-3 py-1.5 font-bold text-emerald-700"
                                    x-text="selectedProfile.experience_label"
                                ></span>
                            </div>

                            <section x-show="selectedProfile.keywords?.length" class="mt-7">
                                <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.talent.specialty_skills') }}</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <template x-for="keyword in selectedProfile.keywords" :key="keyword">
                                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700" x-text="keyword"></span>
                                    </template>
                                </div>
                            </section>

                            <section
                                x-show="selectedProfile.work_modes?.length || selectedProfile.languages?.length"
                                class="mt-7 grid gap-5 sm:grid-cols-2"
                            >
                                <div x-show="selectedProfile.work_modes?.length">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.work_modes') }}</h3>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <template x-for="mode in selectedProfile.work_modes" :key="mode">
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700" x-text="mode"></span>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedProfile.languages?.length">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.languages') }}</h3>
                                    <p class="mt-2 text-sm text-gray-600" x-text="selectedProfile.languages.join(', ')"></p>
                                </div>
                            </section>

                            <section
                                x-show="selectedProfile.education_label || selectedProfile.certifications"
                                class="mt-7 grid gap-5 sm:grid-cols-2"
                            >
                                <div x-show="selectedProfile.education_label">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.education') }}</h3>
                                    <p class="mt-2 text-sm text-gray-600" x-text="selectedProfile.education_label"></p>
                                </div>
                                <div x-show="selectedProfile.certifications">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.certifications') }}</h3>
                                    <p class="mt-2 whitespace-pre-line text-sm text-gray-600" x-text="selectedProfile.certifications"></p>
                                </div>
                            </section>

                            <section x-show="selectedProfile.bio" class="mt-7">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.talents.presentation') }}</h3>
                                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700" x-text="selectedProfile.bio"></p>
                            </section>

                            <div class="mt-8 flex flex-wrap gap-3 border-t pt-6">
                                <a
                                    x-show="selectedProfile.cv_url"
                                    :href="selectedProfile.cv_url"
                                    target="_blank"
                                    class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                >{{ __('talenma.talents.view_cv') }}</a>
                                <a
                                    x-show="selectedProfile.linkedin_url"
                                    :href="selectedProfile.linkedin_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                >LinkedIn</a>
                                <a
                                    x-show="selectedProfile.github_url"
                                    :href="selectedProfile.github_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                >GitHub</a>
                                <a
                                    x-show="selectedProfile.portfolio_url"
                                    :href="selectedProfile.portfolio_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                >Portfolio</a>
                            </div>

                            <div class="mt-7 rounded-xl border bg-gray-50 p-5 space-y-4">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ __('talenma.inbox.compose_title') }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ __('talenma.inbox.compose_desc') }}</p>
                                </div>

                                <template x-if="composeSuccessUrl">
                                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 space-y-2">
                                        <p>{{ __('talenma.inbox.compose_success') }}</p>
                                        <a :href="composeSuccessUrl" class="inline-flex font-semibold text-emerald-900 underline">
                                            {{ __('talenma.inbox.compose_open_thread') }}
                                        </a>
                                    </div>
                                </template>

                                <form x-show="!composeSuccessUrl" class="space-y-3" @submit.prevent="sendCompose()">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600" for="compose-subject">{{ __('talenma.inbox.compose_subject') }}</label>
                                        <input
                                            id="compose-subject"
                                            type="text"
                                            x-model="composeSubject"
                                            maxlength="255"
                                            required
                                            class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="{{ __('talenma.inbox.compose_subject_placeholder') }}"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600" for="compose-body">{{ __('talenma.inbox.compose_body') }}</label>
                                        <textarea
                                            id="compose-body"
                                            x-model="composeBody"
                                            rows="5"
                                            required
                                            minlength="20"
                                            maxlength="5000"
                                            class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="{{ __('talenma.inbox.compose_body_placeholder') }}"
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-gray-600">
                                            <input type="file" class="hidden" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" @change="onComposeFiles($event)">
                                            <span class="rounded-lg border bg-white px-3 py-1.5 hover:bg-gray-50">{{ __('talenma.inbox.attach') }}</span>
                                        </label>
                                        <p class="mt-1 text-xs text-gray-400">{{ __('talenma.inbox.attachments_hint') }}</p>
                                        <ul class="mt-1 space-y-1 text-xs text-gray-600" x-show="composeFiles.length">
                                            <template x-for="(file, index) in composeFiles" :key="file.name + index">
                                                <li class="flex items-center gap-2">
                                                    <span x-text="file.name"></span>
                                                    <button type="button" class="text-red-600" @click="removeComposeFile(index)">×</button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <p x-show="composeError" class="text-sm text-red-600" x-text="composeError"></p>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="submit"
                                            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
                                            :disabled="composeSending"
                                            x-text="composeSending ? @js(__('talenma.inbox.sending')) : @js(__('talenma.inbox.compose_send'))"
                                        ></button>
                                        <button
                                            type="button"
                                            class="rounded-lg border bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                            @click="resetCompose()"
                                        >{{ __('talenma.inbox.compose_cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
