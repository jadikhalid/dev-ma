<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.talents.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.talents.subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="talents" />

    @php
        $selectClass = 'mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
        $canProposeDirectHire = $canProposeDirectHire ?? true;
        $hiredTalentIds = $hiredTalentIds ?? [];
        $blockedNamedTalentIds = $blockedNamedTalentIds ?? [];
        $lockedNamedTalentIds = $lockedNamedTalentIds ?? [];
        $revealedTalentIds = $revealedTalentIds ?? [];
        $initialTalents = $talents->getCollection()->map(function ($talent) use ($canProposeDirectHire, $hiredTalentIds, $blockedNamedTalentIds, $lockedNamedTalentIds, $revealedTalentIds) {
            $profile = $talent->profile;
            $experienceYears = $profile?->experience_years;
            $forceReveal = in_array($talent->id, $revealedTalentIds, true);
            $isPublic = $profile?->isRevealedAsPublic($forceReveal) ?? false;
            $alreadyHired = in_array($talent->id, $hiredTalentIds, true);
            $namedLocked = in_array($talent->id, $lockedNamedTalentIds, true);
            $talentLocked = $namedLocked || $alreadyHired;
            $canPropose = $canProposeDirectHire && ! $talentLocked;
            $disabledHint = $namedLocked
                ? __('talenma.direct_hire.cta_disabled_locked_intermediation_hint')
                : ($alreadyHired
                    ? __('talenma.direct_hire.cta_disabled_locked_hint')
                    : ($canPropose ? null : __('talenma.direct_hire.cta_disabled_hint')));
            $canRequestNamed = ! in_array($talent->id, $blockedNamedTalentIds, true) && ! $alreadyHired;
            $namedHint = $canRequestNamed
                ? null
                : ($namedLocked
                    ? __('talenma.recruitment.named_blocked_locked')
                    : ($alreadyHired
                        ? __('talenma.recruitment.named_blocked_locked_direct_hire')
                        : __('talenma.recruitment.named_blocked_open')));

            return [
                'id' => $talent->id,
                'name' => $profile?->visibleDisplayName($talent, $forceReveal) ?? $talent->publicDisplayName(),
                'avatar_url' => $profile?->visibleAvatarUrl($talent, $forceReveal),
                'initials' => $talent->initials(),
                'is_public' => $isPublic,
                'employer_label' => $profile?->employerLabel($forceReveal),
                'profession_label' => $profile?->professionLabel(),
                'sector_label' => $profile?->sectorLabel(),
                'specialization' => $profile?->specialization,
                'experience_years' => $experienceYears,
                'experience_label' => $experienceYears !== null
                    ? __('talenma.talents.experience', ['years' => $experienceYears])
                    : null,
                'availability_label' => $profile?->statusLabel(),
                'availability_tone' => $profile?->statusTone(),
                'talent_locked' => $talentLocked,
                'presentation_video_url' => ($isPublic && filled($profile?->presentation_video_url))
                    ? $profile->presentation_video_url
                    : null,
                'cv_url' => ($isPublic && $profile?->cvDocument())
                    ? route('company.talent.cv', $talent)
                    : null,
                'profile_url' => route('company.talent.show', $talent),
                'recruitment_url' => $canRequestNamed ? route('recruitment.create', $talent) : null,
                'can_request_named' => $canRequestNamed,
                'named_request_disabled_hint' => $namedHint,
                'named_unlock_url' => null,
                'direct_hire_url' => route('company.direct-hire.create', $talent),
                'can_propose_direct_hire' => $canPropose,
                'direct_hire_disabled_hint' => $disabledHint,
                'direct_hire_unlock_url' => null,
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
                'composeSubjectRequired' => __('talenma.inbox.compose_subject_required'),
                'directHireDisabled' => __('talenma.direct_hire.cta_disabled_hint'),
                'namedDisabled' => __('talenma.recruitment.named_blocked_open'),
                'talentLocked' => __('talenma.recruitment.talent_lock_badge'),
                'unlockError' => __('talenma.recruitment.talent_unlock_error'),
            ]),
            composeUrl: @js(route('inbox.store')),
            csrf: @js(csrf_token()),
            canProposeDirectHire: @js($canProposeDirectHire),
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
                <div class="bg-white rounded-2xl border p-5 flex flex-col hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <template x-if="talent.avatar_url">
                            <img
                                :src="talent.avatar_url"
                                :alt="talent.name"
                                class="h-14 w-14 shrink-0 rounded-full object-cover ring-1 ring-gray-200"
                            >
                        </template>
                        <template x-if="!talent.avatar_url">
                            <span
                                class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-base font-bold text-indigo-700"
                                x-text="talent.initials"
                                aria-hidden="true"
                            ></span>
                        </template>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-base text-gray-900 truncate" x-text="talent.name"></h3>
                            <p class="mt-0.5 text-sm font-medium text-indigo-600 truncate">
                                <span x-text="talent.profession_label"></span>
                                <span x-show="talent.profession_label && talent.sector_label"> · </span>
                                <span x-text="talent.sector_label"></span>
                            </p>
                            <p
                                class="mt-0.5 text-xs text-gray-500 truncate"
                                x-show="talent.employer_label"
                                x-text="talent.employer_label"
                            ></p>
                        </div>
                        <span
                            x-show="talent.availability_label"
                            class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-semibold"
                            :class="profileStatusClass(talent.availability_tone)"
                            x-text="talent.availability_label"
                        ></span>
                        <span
                            x-show="talent.talent_locked"
                            class="shrink-0 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-amber-900 ring-1 ring-amber-200"
                            :title="labels.talentLocked || ''"
                        >
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                            {{ __('talenma.recruitment.talent_lock_badge') }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-1.5">
                        <span
                            class="inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700"
                            x-show="talent.experience_label"
                            x-text="talent.experience_label"
                        ></span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-1 min-h-[1.5rem]" x-show="keySkills(talent).length">
                        <template x-for="skill in keySkills(talent).slice(0, 4)" :key="skill">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded" x-text="skill"></span>
                        </template>
                        <span
                            class="px-2 py-0.5 text-xs text-gray-400"
                            x-show="keySkills(talent).length > 4"
                            x-text="'+' + (keySkills(talent).length - 4)"
                        ></span>
                    </div>

                    <div class="mt-auto pt-4 flex gap-2">
                        <a
                            :href="talent.profile_url"
                            @click.prevent="openProfile(talent.profile_url)"
                            class="flex-1 text-center px-3 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50"
                            x-text="labels.view"
                        ></a>
                        <a
                            :href="talent.cv_url || '#'"
                            :target="talent.cv_url ? '_blank' : null"
                            :rel="talent.cv_url ? 'noopener' : null"
                            :aria-disabled="!talent.cv_url"
                            :tabindex="talent.cv_url ? 0 : -1"
                            @click="if (! talent.cv_url) { $event.preventDefault() }"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold"
                            :class="talent.cv_url
                                ? 'bg-slate-700 text-white hover:bg-slate-800'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed pointer-events-none'"
                        >{{ __('talenma.talents.view_cv') }}</a>
                        <button
                            type="button"
                            :disabled="!talent.presentation_video_url"
                            @click="openVideoModal(talent)"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold"
                            :class="talent.presentation_video_url
                                ? 'bg-violet-600 text-white hover:bg-violet-700'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.34-5.89a1.5 1.5 0 0 0 0-2.54L6.3 2.84Z"/></svg>
                            {{ __('talenma.talents.view_video') }}
                        </button>
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
        @include('company._talent-profile-drawer')
    </div>
</x-app-layout>
