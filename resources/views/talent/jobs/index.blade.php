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
            defaultSector: @js($defaultSectorSlug),
            sectors: @js($professionSectors),
            initialCounts: @js($counts),
            initialJobs: @js($jobs),
            labels: {
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

                <div class="mb-3 flex items-baseline justify-between gap-2">
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

                <ul x-show="jobs.length > 0" class="space-y-2" :class="{ 'opacity-60': loading }">
                    <template x-for="job in jobs" :key="job.id">
                        <li>
                            <a
                                :href="job.url"
                                class="group relative flex overflow-hidden rounded-lg bg-white ring-1 ring-slate-200/90 shadow-sm transition duration-150 hover:-translate-y-px hover:shadow hover:ring-indigo-200 hover:bg-indigo-50/40"
                            >
                                <span class="absolute inset-y-0 left-0 w-1 bg-indigo-500" aria-hidden="true"></span>
                                <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 py-2.5 pl-3.5 pr-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="inline-flex min-w-0 items-center gap-2 text-sm font-semibold text-slate-900 group-hover:text-indigo-800">
                                            <span class="truncate" x-text="job.title"></span>
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
                                        <span
                                            x-show="!job.applied"
                                            class="shrink-0 text-xs text-slate-500"
                                            x-text="job.location"
                                        ></span>
                                    </div>
                                    <p class="truncate text-xs text-slate-600">
                                        <span x-text="job.company"></span>
                                        <span
                                            x-show="job.external"
                                            class="ml-1 inline-flex px-1.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-amber-50 text-amber-800"
                                            x-text="job.external_badge"
                                        ></span>
                                    </p>
                                    <p class="truncate text-xs font-medium text-indigo-700" x-show="job.summary" x-text="job.summary"></p>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>
