<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.talent_title') }}</h2>
            <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.talent_subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="jobs" />

    <div
        class="home-shell py-5 sm:py-6"
        x-data="talentJobsIndex({
            searchUrl: @js(route('talent.jobs.index')),
            initialScope: @js($scope),
            initialSector: @js($sectorSlug),
            initialProfession: @js($professionSlug),
            initialQuery: @js($keyword),
            sectors: @js($professionSectors),
            initialCounts: @js($counts),
            initialJobs: @js($jobs),
            initialPagination: @js($pagination),
            labels: {
                paginationSummary: @js(__('talenma.jobs.pagination_summary')),
                all: @js(__('talenma.jobs.talent_filter_all')),
                applied: @js(__('talenma.jobs.talent_filter_applied_open')),
                closed: @js(__('talenma.jobs.talent_filter_applied_closed')),
                empty: @js(__('talenma.jobs.talent_empty')),
                emptyApplied: @js(__('talenma.jobs.talent_empty_applied_open')),
                emptyClosed: @js(__('talenma.jobs.talent_empty_applied_closed')),
                emptyFiltered: @js(__('talenma.jobs.empty_filtered')),
                emptySearch: @js(__('talenma.career_pages.offers_search_empty')),
                queryEmpty: @js(__('talenma.home.search_validation_empty')),
                queryShort: @js(__('talenma.home.search_validation_short')),
                queryLong: @js(__('talenma.home.search_validation_long')),
                queryChars: @js(__('talenma.home.search_validation_chars')),
                navNew: @js(__('talenma.jobs.nav_new')),
                networkError: @js(__('talenma.jobs.network_error')),
                offersView: @js(__('talenma.career_pages.offers_view')),
            },
        })"
    >
        <section class="space-y-3">
            <nav
                class="flex flex-col gap-2 rounded-2xl border border-indigo-100 bg-white p-2 sm:flex-row sm:flex-wrap sm:items-center sm:gap-2 sm:p-2.5"
                aria-label="{{ __('talenma.jobs.talent_filter_applied') }}"
            >
                <button
                    type="button"
                    @click="setScope('all')"
                    class="inline-flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition sm:justify-center"
                    :class="scope === 'all'
                        ? 'bg-indigo-600 text-white shadow-sm'
                        : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-900'"
                    :aria-current="scope === 'all' ? 'page' : null"
                >
                    <span>{{ __('talenma.jobs.talent_filter_all') }}</span>
                    <span
                        class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                        :class="scope === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                        x-text="counts.all"
                    ></span>
                </button>

                <div class="hidden h-6 w-px shrink-0 bg-slate-200 sm:block" aria-hidden="true"></div>

                <div class="flex min-w-0 flex-1 flex-col gap-1.5 sm:flex-row sm:items-center sm:gap-2">
                    <p class="shrink-0 px-1 text-[11px] font-bold uppercase tracking-wider text-indigo-800/80 sm:px-0">
                        {{ __('talenma.jobs.talent_filter_applied') }}
                    </p>
                    <div class="grid grid-cols-2 gap-1.5 sm:flex sm:flex-wrap">
                        <button
                            type="button"
                            @click="setScope('applied')"
                            class="inline-flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition sm:justify-center"
                            :class="scope === 'applied'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-900'"
                            :aria-current="scope === 'applied' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.talent_filter_applied_open') }}</span>
                            <span
                                class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                :class="scope === 'applied' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                                x-text="counts.applied"
                            ></span>
                        </button>
                        <button
                            type="button"
                            @click="setScope('closed')"
                            class="inline-flex items-center justify-between gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition sm:justify-center"
                            :class="scope === 'closed'
                                ? 'bg-indigo-600 text-white shadow-sm'
                                : 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-900'"
                            :aria-current="scope === 'closed' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.talent_filter_applied_closed') }}</span>
                            <span
                                class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                :class="scope === 'closed' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                                x-text="counts.closed"
                            ></span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="rounded-2xl bg-gradient-to-br from-indigo-50 via-white to-violet-50 p-3.5 ring-1 ring-indigo-100 sm:p-4" role="search">
                <div class="space-y-2">
                    <div>
                        <label for="talent-jobs-query" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            {{ __('talenma.jobs.search_free') }}
                        </label>
                        <div class="relative min-w-0 max-w-2xl">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
                            </svg>
                            <input
                                id="talent-jobs-query"
                                x-ref="query"
                                x-model="query"
                                name="q"
                                type="search"
                                value="{{ $keyword }}"
                                placeholder="{{ __('talenma.career_pages.offers_search_placeholder') }}"
                                @input="onQueryInput()"
                                @keydown.enter.prevent="commitQuery()"
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100"
                            >
                        </div>
                    </div>

                    <div>
                        <p class="mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('talenma.jobs.search_manual') }}</p>
                        <div class="grid max-w-2xl grid-cols-1 gap-1.5 sm:grid-cols-2">
                            <div class="min-w-0">
                                <label for="talent-jobs-sector" class="sr-only">
                                    {{ __('talenma.jobs.field_sector') }}
                                </label>
                                <select
                                    id="talent-jobs-sector"
                                    x-model="sectorSlug"
                                    @change="onSectorChange()"
                                    class="block w-full rounded-lg border-slate-200 py-2 text-sm shadow-sm focus:border-indigo-300 focus:ring-indigo-100"
                                >
                                    <option value="">{{ __('talenma.jobs.filter_sector_all') }}</option>
                                    @foreach ($professionSectors as $sectorOption)
                                        <option
                                            value="{{ $sectorOption['slug'] }}"
                                            @selected($sectorSlug === ($sectorOption['slug'] ?? null))
                                        >{{ $sectorOption['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="min-w-0">
                                <label for="talent-jobs-profession" class="sr-only">
                                    {{ __('talenma.jobs.field_profession') }}
                                </label>
                                <select
                                    id="talent-jobs-profession"
                                    x-model="professionSlug"
                                    @change="onProfessionChange()"
                                    class="block w-full rounded-lg border-slate-200 py-2 text-sm shadow-sm focus:border-indigo-300 focus:ring-indigo-100 disabled:bg-slate-50"
                                    :disabled="!filteredProfessions.length"
                                >
                                    <option value="">{{ __('talenma.jobs.filter_profession_all') }}</option>
                                    <template x-for="profession in filteredProfessions" :key="profession.slug">
                                        <option :value="profession.slug" x-text="profession.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mt-5">
            <div x-ref="jobsList" class="mb-3 flex scroll-mt-24 items-baseline justify-between gap-2">
                <h3 class="text-sm font-bold text-slate-900">
                    <span x-text="scopeHeading"></span>
                    <span class="ml-1.5 text-xs font-semibold text-indigo-700" x-text="total"></span>
                </h3>
                <span x-show="loading" class="text-[11px] font-medium text-slate-400" x-cloak>{{ __('talenma.jobs.filter_loading') }}</span>
            </div>

            <div x-show="error" x-cloak class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" x-text="error"></div>

            <template x-if="!loading && jobs.length === 0">
                <p class="rounded-2xl bg-gray-50 px-6 py-10 text-center text-gray-600" x-text="emptyMessage"></p>
            </template>

            <ul x-show="jobs.length > 0" class="grid gap-4 md:grid-cols-2" :class="{ 'opacity-60': loading }">
                <template x-for="job in jobs" :key="job.id">
                    <li class="min-w-0">
                        <a
                            :href="job.url"
                            class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md"
                            data-job-card
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white ring-1 ring-slate-200">
                                    <template x-if="job.company_logo">
                                        <img :src="job.company_logo" :alt="job.company" class="h-full w-full object-contain p-1" loading="lazy" decoding="async">
                                    </template>
                                    <template x-if="!job.company_logo">
                                        <span class="flex h-full w-full items-center justify-center bg-indigo-50 text-sm font-bold text-indigo-700" x-text="job.company_initials" aria-hidden="true"></span>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1 pt-0.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="truncate text-sm font-semibold text-slate-900" x-text="job.company"></p>
                                        <span
                                            x-show="job.applied && job.application_status_label"
                                            class="inline-flex shrink-0 items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700 ring-1 ring-indigo-100"
                                            x-text="job.application_status_label"
                                        ></span>
                                    </div>
                                    <p x-show="job.sector" class="mt-0.5 truncate text-xs text-slate-500" x-text="job.sector"></p>
                                </div>
                            </div>

                            <h2 class="mt-4 inline-flex items-start gap-2 text-lg font-bold leading-snug text-slate-900 group-hover:text-indigo-700">
                                <span class="line-clamp-2 min-w-0" x-text="job.title"></span>
                                <span x-show="job.unseen" class="relative mt-1.5 flex h-2 w-2 shrink-0" :title="labels.navNew">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                </span>
                            </h2>

                            <p x-show="job.excerpt" class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500" x-text="job.excerpt"></p>

                            <div
                                x-show="job.location || (job.work_modes && job.work_modes.length) || job.experience"
                                class="mt-4 flex flex-wrap gap-1.5"
                            >
                                <template x-if="job.location">
                                    <span class="inline-flex max-w-full items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                        </svg>
                                        <span class="truncate" x-text="job.location"></span>
                                    </span>
                                </template>
                                <template x-for="mode in (job.work_modes || [])" :key="mode">
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700" x-text="mode"></span>
                                </template>
                                <template x-if="job.experience">
                                    <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800" x-text="job.experience"></span>
                                </template>
                            </div>

                            <div class="mt-auto pt-4">
                                <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                                    <div class="min-w-0 flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <time x-show="job.published_label" class="inline-flex min-w-0 items-center gap-1.5 text-xs text-slate-500" data-job-card-published>
                                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                            </svg>
                                            <span class="truncate" x-text="job.published_label"></span>
                                        </time>
                                        <span x-show="job.updated_label" x-cloak class="inline-flex items-center gap-1 text-xs font-medium text-amber-700" data-job-card-updated>
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                            </svg>
                                            <span x-text="job.updated_label"></span>
                                        </span>
                                    </div>
                                    <span class="shrink-0 text-xs font-semibold text-indigo-700" x-text="labels.offersView"></span>
                                </div>
                            </div>
                        </a>
                    </li>
                </template>
            </ul>

            <nav
                x-show="pagination.total > 0"
                x-cloak
                class="mt-8 flex flex-col items-center gap-3 sm:flex-row sm:justify-between"
                aria-label="{{ __('talenma.jobs.pagination_label') }}"
                data-jobs-pagination
            >
                <p class="text-sm text-slate-600" x-text="paginationSummary" data-jobs-pagination-summary></p>

                <div x-show="pagination.last_page > 1" class="flex items-center gap-1">
                    <button
                        type="button"
                        @click="goToPage(pagination.page - 1)"
                        :disabled="pagination.page <= 1 || loading"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 ring-1 ring-slate-200 transition hover:bg-indigo-50 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent disabled:hover:text-slate-700"
                    >{{ __('talenma.jobs.pagination_previous') }}</button>

                    <template x-for="(item, index) in pageNumbers" :key="`${item}-${index}`">
                        <span>
                            <span x-show="item === '…'" class="px-1.5 text-sm text-slate-400">…</span>
                            <button
                                x-show="item !== '…'"
                                type="button"
                                @click="goToPage(item)"
                                :disabled="loading"
                                class="min-w-[2.25rem] rounded-lg px-2.5 py-1.5 text-sm font-semibold transition"
                                :class="item === pagination.page
                                    ? 'bg-indigo-600 text-white shadow-sm'
                                    : 'text-slate-700 ring-1 ring-slate-200 hover:bg-indigo-50 hover:text-indigo-700'"
                                :aria-current="item === pagination.page ? 'page' : null"
                                x-text="item"
                            ></button>
                        </span>
                    </template>

                    <button
                        type="button"
                        @click="goToPage(pagination.page + 1)"
                        :disabled="pagination.page >= pagination.last_page || loading"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-700 ring-1 ring-slate-200 transition hover:bg-indigo-50 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent disabled:hover:text-slate-700"
                    >{{ __('talenma.jobs.pagination_next') }}</button>
                </div>
            </nav>
        </div>
    </div>
</x-app-layout>
