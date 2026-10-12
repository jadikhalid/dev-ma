<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.talent_title') }}</h2>
            <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.talent_subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="jobs" />

    <div
        class="py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="talentJobsIndex({
            searchUrl: @js(route('talent.jobs.index')),
            initialScope: @js($scope),
            initialSector: @js($sectorSlug),
            initialProfession: @js($professionSlug),
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
                navNew: @js(__('talenma.jobs.nav_new')),
                networkError: @js(__('talenma.jobs.network_error')),
            },
        })"
    >
        <div class="grid grid-cols-1 lg:grid-cols-[15rem_minmax(0,1fr)] gap-4 lg:gap-5 lg:items-start">
            <aside class="space-y-3 min-w-0 lg:sticky lg:top-20">
                <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/70 via-white to-white p-3.5 sm:p-4">
                    <nav aria-label="{{ __('talenma.jobs.talent_filter_all') }}">
                        <button
                            type="button"
                            @click="setScope('all')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
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
                    </nav>
                </div>

                <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/70 via-white to-white p-3.5 sm:p-4">
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-indigo-800/80">
                        {{ __('talenma.jobs.talent_filter_applied') }}
                    </p>
                    <nav class="space-y-1.5" aria-label="{{ __('talenma.jobs.talent_filter_applied') }}">
                        <button
                            type="button"
                            @click="setScope('applied')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
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
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
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
                    </nav>
                </div>
            </aside>

            <section class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 min-w-0">
                <div x-show="scope === 'all'" x-cloak class="mb-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="min-w-0">
                        <label for="talent-jobs-sector" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            {{ __('talenma.jobs.field_sector') }}
                        </label>
                        <select
                            id="talent-jobs-sector"
                            x-model="sectorSlug"
                            @change="onSectorChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        <label for="talent-jobs-profession" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            {{ __('talenma.jobs.field_profession') }}
                        </label>
                        <select
                            id="talent-jobs-profession"
                            x-model="professionSlug"
                            @change="onProfessionChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-50"
                            :disabled="!filteredProfessions.length"
                        >
                            <option value="">{{ __('talenma.jobs.filter_profession_all') }}</option>
                            <template x-for="profession in filteredProfessions" :key="profession.slug">
                                <option :value="profession.slug" x-text="profession.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div x-ref="jobsList" class="mb-3 flex scroll-mt-24 items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        <span x-text="scopeHeading"></span>
                        <span class="ml-1.5 text-xs font-semibold text-indigo-700" x-text="total"></span>
                    </h3>
                    <span x-show="loading" class="text-[11px] font-medium text-slate-400" x-cloak>{{ __('talenma.jobs.filter_loading') }}</span>
                </div>

                <div x-show="error" x-cloak class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="error"></div>

                <template x-if="!loading && jobs.length === 0">
                    <p class="rounded-lg border border-dashed border-slate-200 bg-slate-50/80 px-3 py-8 text-center text-sm text-slate-500" x-text="emptyMessage"></p>
                </template>

                <ul x-show="jobs.length > 0" class="space-y-3" :class="{ 'opacity-60': loading }">
                    <template x-for="job in jobs" :key="job.id">
                        <li>
                            <a
                                :href="job.url"
                                class="group relative flex gap-3 sm:gap-4 rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-sm transition duration-150 hover:-translate-y-px hover:border-indigo-200 hover:shadow-md"
                                data-job-card
                            >
                                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white ring-1 ring-slate-200">
                                    <template x-if="job.company_logo">
                                        <img :src="job.company_logo" :alt="job.company" class="h-full w-full object-contain p-1" loading="lazy">
                                    </template>
                                    <template x-if="!job.company_logo">
                                        <span class="flex h-full w-full items-center justify-center bg-indigo-50 text-sm font-bold text-indigo-700" x-text="job.company_initials"></span>
                                    </template>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="inline-flex min-w-0 items-center gap-2 text-[15px] font-semibold leading-snug text-slate-900 group-hover:text-indigo-700">
                                            <span class="line-clamp-2" x-text="job.title"></span>
                                            <span x-show="job.unseen" class="relative flex h-2 w-2 shrink-0" :title="labels.navNew">
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                            </span>
                                        </p>
                                        <span
                                            x-show="job.applied && job.application_status_label"
                                            class="inline-flex shrink-0 items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700 ring-1 ring-indigo-100"
                                            x-text="job.application_status_label"
                                        ></span>
                                    </div>

                                    <p class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-sm text-slate-600">
                                        <span class="font-medium text-slate-700" x-text="job.company"></span>
                                        <template x-if="job.location && job.location !== '—'">
                                            <span class="inline-flex items-center gap-1 text-slate-500">
                                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                                </svg>
                                                <span x-text="job.location"></span>
                                            </span>
                                        </template>
                                    </p>

                                    <div x-show="job.tags && job.tags.length" class="mt-2 flex flex-wrap gap-1.5">
                                        <template x-for="tag in job.tags" :key="tag">
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700" x-text="tag"></span>
                                        </template>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-slate-100 pt-2.5 text-[11px] text-slate-500">
                                        <span x-show="job.published_label" class="inline-flex items-center gap-1" data-job-card-published>
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                            </svg>
                                            <span x-text="job.published_label"></span>
                                        </span>
                                        <span x-show="job.updated_label" x-cloak class="inline-flex items-center gap-1 font-medium text-amber-700" data-job-card-updated>
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                            </svg>
                                            <span x-text="job.updated_label"></span>
                                        </span>
                                    </div>
                                </div>

                                <svg class="hidden sm:block h-5 w-5 shrink-0 self-center text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </li>
                    </template>
                </ul>

                <nav
                    x-show="pagination.total > 0"
                    x-cloak
                    class="mt-4 flex flex-col items-center gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-between"
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
            </section>
        </div>
    </div>
</x-app-layout>
