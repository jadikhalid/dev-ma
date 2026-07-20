<div
    x-show="drawerOpen"
    x-cloak
    class="fixed inset-0 z-[60]"
    role="dialog"
    aria-modal="true"
    :aria-label="drawerLabels.title"
    @keydown.escape.window="closeDrawer()"
>
    <div
        x-show="drawerOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-gray-900/40"
        @click="closeDrawer()"
    ></div>

    <div
        x-show="drawerOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute inset-y-0 right-0 flex w-full max-w-xl flex-col bg-white shadow-2xl"
        @click.stop
    >
        <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600" x-text="drawerLabels.title"></p>
                <h3 class="mt-1 text-lg font-bold text-gray-900">
                    <span x-text="canViewProfiles ? displayedResultsCount : resultsCount"></span>
                    <span x-text="drawerLabels.resultsSuffix"></span>
                </h3>
                <p class="mt-1 text-sm text-gray-500" x-text="drawerLabels.subtitle"></p>
            </div>
            <button
                type="button"
                @click="closeDrawer()"
                class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                :aria-label="drawerLabels.close"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div
            x-show="canViewProfiles && !searchLoading && results.length"
            x-cloak
            class="shrink-0 border-b border-gray-100 px-5 py-3 sm:px-6"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label for="talent-drawer-filter-experience" class="mb-1 block text-xs font-medium text-gray-500" x-text="drawerLabels.filterExperience"></label>
                    <select
                        id="talent-drawer-filter-experience"
                        x-model="filterExperience"
                        class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="all" x-text="drawerLabels.filterAll"></option>
                        <option value="0-1" x-text="drawerLabels.filterExp01"></option>
                        <option value="1-5" x-text="drawerLabels.filterExp15"></option>
                        <option value="5-10" x-text="drawerLabels.filterExp510"></option>
                        <option value="10+" x-text="drawerLabels.filterExp10Plus"></option>
                    </select>
                </div>
                <div>
                    <label for="talent-drawer-filter-status" class="mb-1 block text-xs font-medium text-gray-500" x-text="drawerLabels.filterStatus"></label>
                    <select
                        id="talent-drawer-filter-status"
                        x-model="filterStatus"
                        class="w-full rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="all" x-text="drawerLabels.filterAll"></option>
                        <option value="{{ \App\Models\Profile::STATUS_AVAILABLE }}" x-text="drawerLabels.statusAvailable"></option>
                        <option value="{{ \App\Models\Profile::STATUS_BUSY }}" x-text="drawerLabels.statusBusy"></option>
                        <option value="{{ \App\Models\Profile::STATUS_LISTENING }}" x-text="drawerLabels.statusListening"></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
            <div x-show="searchLoading" class="flex items-center justify-center gap-3 py-16 text-sm text-gray-500">
                <svg class="h-5 w-5 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="drawerLabels.loading"></span>
            </div>

            <div
                x-show="!searchLoading && searchError"
                x-cloak
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                x-text="searchError"
            ></div>

            <div
                x-show="!searchLoading && !searchError && results.length === 0"
                x-cloak
                class="py-16 text-center"
            >
                <p class="text-lg font-medium text-gray-900" x-text="drawerLabels.empty"></p>
                <p class="mt-2 text-sm text-gray-500" x-text="drawerLabels.emptyDesc"></p>
            </div>

            <div
                x-show="!searchLoading && !searchError && results.length && displayedResults.length === 0"
                x-cloak
                class="py-16 text-center"
            >
                <p class="text-lg font-medium text-gray-900" x-text="drawerLabels.empty"></p>
                <p class="mt-2 text-sm text-gray-500" x-text="drawerLabels.filterEmpty"></p>
            </div>

            <div x-show="!searchLoading && displayedResults.length" class="space-y-4">
                <template x-for="talent in displayedResults" :key="talent.id">
                    <article class="rounded-2xl border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <template x-if="canViewProfiles && talent.avatar_url">
                                <img
                                    :src="talent.avatar_url"
                                    :alt="talent.name || talent.display_name"
                                    class="h-12 w-12 shrink-0 rounded-full object-cover ring-1 ring-gray-200"
                                >
                            </template>
                            <template x-if="!canViewProfiles || !talent.avatar_url">
                                <span
                                    class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700"
                                    x-text="talent.initials"
                                    aria-hidden="true"
                                ></span>
                            </template>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <p
                                        class="text-sm font-semibold text-gray-900"
                                        x-text="canViewProfiles ? (talent.name || talent.display_name) : talent.display_name"
                                    ></p>
                                    <span
                                        x-show="talent.availability_label"
                                        class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="{
                                            'bg-emerald-100 text-emerald-800': talent.availability_tone === 'available',
                                            'bg-amber-100 text-amber-800': talent.availability_tone === 'listening',
                                            'bg-gray-200 text-gray-700': talent.availability_tone === 'busy',
                                        }"
                                        x-text="talent.availability_label"
                                    ></span>
                                </div>
                                <p class="mt-0.5 truncate text-sm font-medium text-indigo-600">
                                    <span x-text="talent.profession"></span>
                                    <span x-show="talent.profession && talent.sector"> - </span>
                                    <span x-text="talent.sector"></span>
                                </p>
                                <p class="mt-1 truncate text-xs text-gray-500" x-show="talent.specialization" x-text="talent.specialization"></p>
                                <p
                                    class="mt-1 inline-flex rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700"
                                    x-show="talent.experience_label"
                                    x-text="talent.experience_label"
                                ></p>

                                <div class="mt-3 flex w-full items-center justify-between gap-2" x-show="canViewProfiles">
                                    <div>
                                        <template x-if="talent.cv_url">
                                            <a
                                                :href="talent.cv_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-indigo-700"
                                                x-text="drawerLabels.viewCv"
                                            ></a>
                                        </template>
                                        <template x-if="!talent.cv_url">
                                            <button
                                                type="button"
                                                disabled
                                                class="inline-flex cursor-not-allowed items-center justify-center rounded-md bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-400"
                                                x-text="drawerLabels.viewCv"
                                            ></button>
                                        </template>
                                    </div>
                                    <a
                                        x-show="talent.profile_url"
                                        :href="talent.profile_url"
                                        class="inline-flex items-center justify-center rounded-lg border border-indigo-200 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-50"
                                        x-text="drawerLabels.viewProfile"
                                    ></a>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>

                <template x-if="!canViewProfiles">
                    <div class="space-y-4">
                        <article class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 p-4" aria-hidden="true">
                            <div class="pointer-events-none select-none blur-[2px] opacity-50">
                                <div class="flex items-start gap-3">
                                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500">??</span>
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="h-4 w-32 rounded bg-gray-300"></div>
                                        <div class="h-3 w-48 rounded bg-gray-300"></div>
                                        <div class="h-3 w-40 rounded bg-gray-300"></div>
                                        <div class="flex gap-1 pt-1">
                                            <span class="h-5 w-16 rounded bg-gray-300"></span>
                                            <span class="h-5 w-16 rounded bg-gray-300"></span>
                                            <span class="h-5 w-16 rounded bg-gray-300"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 px-5 py-5 text-center sm:px-6">
                            <p class="text-sm font-semibold text-gray-900" x-text="drawerLabels.lockedTitle"></p>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600" x-text="drawerLabels.lockedDesc"></p>
                            <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:justify-center">
                                <a
                                    href="{{ route('register', ['role' => 'company']) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                                    x-text="drawerLabels.registerCompany"
                                ></a>
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                    x-text="drawerLabels.loginCompany"
                                ></a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
