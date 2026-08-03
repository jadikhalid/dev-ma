<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.title') }}</h2>
            </div>
            <a href="{{ route('company.jobs.create') }}" class="inline-flex justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                {{ __('talenma.jobs.create') }}
            </a>
        </div>
    </x-slot>

    <div
        class="py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="companyJobsIndex({
            searchUrl: @js(route('company.jobs.index')),
            initialScope: @js($scope),
            initialSector: @js($sectorSlug),
            defaultSector: @js($defaultSectorSlug),
            initialQuery: @js($query),
            initialCounts: @js($counts),
            initialJobs: @js($jobs),
            labels: {
                all: @js(__('talenma.jobs.filter_all')),
                mine: @js(__('talenma.jobs.filter_mine')),
                closed: @js(__('talenma.jobs.filter_closed')),
                empty: @js(__('talenma.jobs.empty')),
                emptyMine: @js(__('talenma.jobs.empty_mine')),
                emptyClosed: @js(__('talenma.jobs.empty_closed')),
                emptyFiltered: @js(__('talenma.jobs.empty_filtered')),
                navNew: @js(__('talenma.jobs.nav_new')),
                networkError: @js(__('talenma.jobs.network_error')),
            },
        })"
    >
        <div class="grid grid-cols-1 lg:grid-cols-[15rem_minmax(0,1fr)] gap-4 lg:gap-5 lg:items-start">
            {{-- Colonne gauche : tri --}}
            <aside class="space-y-3 min-w-0 lg:sticky lg:top-20">
                <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50/70 via-white to-white p-3.5 sm:p-4">
                    <nav aria-label="{{ __('talenma.jobs.filter_all') }}">
                        <button
                            type="button"
                            @click="setScope('all')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
                            :class="scope === 'all'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-900'"
                            :aria-current="scope === 'all' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_all') }}</span>
                            <span
                                class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                :class="scope === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                                x-text="counts.all"
                            ></span>
                        </button>
                    </nav>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50/70 via-white to-white p-3.5 sm:p-4">
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-wider text-emerald-800/80">
                        {{ __('talenma.jobs.filter_mine_group') }}
                    </p>
                    <nav class="space-y-1.5" aria-label="{{ __('talenma.jobs.filter_mine_group') }}">
                        <button
                            type="button"
                            @click="setScope('mine')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
                            :class="scope === 'mine'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-900'"
                            :aria-current="scope === 'mine' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_mine') }}</span>
                            <span
                                class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                :class="scope === 'mine' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                                x-text="counts.mine"
                            ></span>
                        </button>
                        <button
                            type="button"
                            @click="setScope('closed')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition"
                            :class="scope === 'closed'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-900'"
                            :aria-current="scope === 'closed' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_closed') }}</span>
                            <span
                                class="inline-flex min-w-[1.5rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[11px] font-bold tabular-nums"
                                :class="scope === 'closed' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                                x-text="counts.closed"
                            ></span>
                        </button>
                    </nav>
                </div>
            </aside>

            {{-- Colonne droite : recherche + étiquettes --}}
            <section class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 min-w-0">
                <div x-show="scope === 'all'" x-cloak class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="sm:w-56 min-w-0">
                        <label for="company-jobs-sector" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            {{ __('talenma.jobs.field_sector') }}
                        </label>
                        <select
                            id="company-jobs-sector"
                            x-model="sectorSlug"
                            @change="onSectorChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="">{{ __('talenma.jobs.filter_sector_all') }}</option>
                            @foreach ($professionSectors as $sectorOption)
                                <option value="{{ $sectorOption['slug'] }}">{{ $sectorOption['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-0 flex-1">
                        <label for="company-jobs-q" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            {{ __('talenma.jobs.filter_keywords') }}
                        </label>
                        <div class="relative mt-1">
                            <input
                                id="company-jobs-q"
                                type="search"
                                x-model="query"
                                @input.debounce.300ms="refresh()"
                                placeholder="{{ __('talenma.jobs.filter_keywords_placeholder') }}"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pr-9"
                                autocomplete="off"
                            >
                            <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m21 21-4.35-4.35m1.6-4.4a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mb-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        <span x-text="scope === 'mine' ? labels.mine : (scope === 'closed' ? labels.closed : labels.all)"></span>
                        <span class="ml-1.5 text-xs font-semibold text-emerald-700" x-text="total"></span>
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
                                class="group relative flex overflow-hidden rounded-lg bg-white ring-1 ring-slate-200/90 shadow-sm transition duration-150 hover:-translate-y-px hover:shadow"
                                :class="job.tone.hover"
                            >
                                <span class="absolute inset-y-0 left-0 w-1" :class="job.tone.bar" aria-hidden="true"></span>
                                <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 py-2.5 pl-3.5 pr-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="inline-flex min-w-0 items-center gap-2 text-sm font-semibold text-slate-900 group-hover:text-emerald-800">
                                            <span class="truncate" x-text="job.title"></span>
                                            <span x-show="job.unseen" class="relative flex h-2 w-2 shrink-0" :title="labels.navNew">
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                            </span>
                                        </p>
                                        <span
                                            class="inline-flex shrink-0 items-center rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1"
                                            :class="job.tone.badge"
                                            x-text="job.status_label"
                                        ></span>
                                    </div>
                                    <p class="truncate text-xs text-slate-600" x-text="job.summary"></p>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>
