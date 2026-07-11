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
                    <span x-text="resultsCount"></span>
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

            <div x-show="!searchLoading && results.length" class="space-y-4">
                <template x-for="company in results" :key="company.id">
                    <article class="rounded-2xl border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="inline-flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-sm font-bold text-indigo-700">
                                <img x-show="company.logo_url" :src="company.logo_url" alt="" class="h-full w-full object-cover">
                                <span x-show="!company.logo_url" x-text="company.initials"></span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900" x-text="company.name"></p>
                                <p class="mt-0.5 text-sm font-medium text-indigo-600" x-text="company.sector"></p>
                                <p class="mt-1 text-xs text-gray-500">
                                    <span x-text="company.city"></span>
                                    <span x-show="company.city && company.country"> · </span>
                                    <span x-text="company.country"></span>
                                </p>
                                <p class="mt-2 text-sm text-gray-600" x-show="company.excerpt" x-text="company.excerpt"></p>

                                <div class="mt-2 flex flex-wrap gap-1" x-show="company.matched_keywords?.length">
                                    <template x-for="keyword in company.matched_keywords" :key="`${company.id}-${keyword}`">
                                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700" x-text="keyword"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </div>
    </div>
</div>
