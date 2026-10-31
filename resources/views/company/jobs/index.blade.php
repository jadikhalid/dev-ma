<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.company_index_subtitle') }}</p>
            </div>
            <a href="{{ route('company.jobs.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg shadow-sm shadow-emerald-600/20 hover:bg-emerald-700 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                {{ __('talenma.jobs.create') }}
            </a>
        </div>
    </x-slot>

    <x-process-help topic="jobs" />

    <div
        class="relative py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="companyJobsIndex({
            searchUrl: @js(route('company.jobs.index')),
            aiFiltersUrl: @js(route('company.jobs.ai-filters')),
            csrf: @js(csrf_token()),
            initialScope: @js($scope),
            initialFilters: @js($filters),
            initialCounts: @js($counts),
            initialJobs: @js($jobs),
            labels: {
                company: @js(__('talenma.jobs.filter_company')),
                companyClosed: @js(__('talenma.jobs.filter_company_closed')),
                mine: @js(__('talenma.jobs.filter_mine')),
                closed: @js(__('talenma.jobs.filter_closed')),
                empty: @js(__('talenma.jobs.empty')),
                emptyCompany: @js(__('talenma.jobs.empty_company')),
                emptyCompanyClosed: @js(__('talenma.jobs.empty_company_closed')),
                emptyMine: @js(__('talenma.jobs.empty_mine')),
                emptyClosed: @js(__('talenma.jobs.empty_closed')),
                emptyFiltered: @js(__('talenma.jobs.empty_filtered')),
                navNew: @js(__('talenma.jobs.nav_new')),
                byAuthor: @js(__('talenma.jobs.card_by_author')),
                networkError: @js(__('talenma.jobs.network_error')),
                aiFiltersLoading: @js(__('talenma.jobs.ai_filters_loading')),
                aiFiltersError: @js(__('talenma.jobs.ai_filters_error')),
                aiFiltersSummary: @js(__('talenma.jobs.ai_filters_summary', ['summary' => ':summary'])),
            },
        })"
    >
        <div class="pointer-events-none absolute inset-x-0 -top-2 h-40 bg-gradient-to-b from-emerald-50/80 via-emerald-50/30 to-transparent" aria-hidden="true"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-[15.5rem_minmax(0,1fr)] gap-4 lg:gap-5 lg:items-start">
            <aside class="min-w-0 lg:sticky lg:top-20">
                <div class="rounded-2xl border border-emerald-100/90 bg-white/90 p-3.5 sm:p-4 shadow-sm shadow-emerald-900/[0.03] backdrop-blur-sm">
                    <p class="mb-2 text-sm font-bold tracking-tight text-slate-900">
                        {{ __('talenma.jobs.filter_company_group') }}
                    </p>
                    <nav class="space-y-0.5" aria-label="{{ __('talenma.jobs.filter_company_group') }}">
                        <button
                            type="button"
                            @click="setScope('company')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-2.5 py-2 text-xs font-medium transition"
                            :class="scope === 'company'
                                ? 'bg-emerald-50 text-emerald-800'
                                : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-800'"
                            :aria-current="scope === 'company' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_company') }}</span>
                            <span
                                class="inline-flex min-w-[1.35rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums"
                                :class="scope === 'company' ? 'bg-emerald-100/80 text-emerald-700' : 'bg-slate-100/90 text-slate-500'"
                                x-text="counts.company"
                            ></span>
                        </button>
                        <button
                            type="button"
                            @click="setScope('company_closed')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-2.5 py-2 text-xs font-medium transition"
                            :class="scope === 'company_closed'
                                ? 'bg-emerald-50 text-emerald-800'
                                : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-800'"
                            :aria-current="scope === 'company_closed' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_company_closed') }}</span>
                            <span
                                class="inline-flex min-w-[1.35rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums"
                                :class="scope === 'company_closed' ? 'bg-emerald-100/80 text-emerald-700' : 'bg-slate-100/90 text-slate-500'"
                                x-text="counts.company_closed"
                            ></span>
                        </button>
                    </nav>

                    <div class="my-3.5 h-px bg-gradient-to-r from-transparent via-emerald-100 to-transparent" aria-hidden="true"></div>

                    <p class="mb-2 text-sm font-bold tracking-tight text-slate-900">
                        {{ auth()->user()->isCompanyOwner()
                            ? __('talenma.jobs.filter_mine_group')
                            : __('talenma.jobs.filter_mine_group_member') }}
                    </p>
                    <nav class="space-y-0.5" aria-label="{{ auth()->user()->isCompanyOwner()
                            ? __('talenma.jobs.filter_mine_group')
                            : __('talenma.jobs.filter_mine_group_member') }}">
                        <button
                            type="button"
                            @click="setScope('mine')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-2.5 py-2 text-xs font-medium transition"
                            :class="scope === 'mine'
                                ? 'bg-emerald-50 text-emerald-800'
                                : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-800'"
                            :aria-current="scope === 'mine' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_mine') }}</span>
                            <span
                                class="inline-flex min-w-[1.35rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums"
                                :class="scope === 'mine' ? 'bg-emerald-100/80 text-emerald-700' : 'bg-slate-100/90 text-slate-500'"
                                x-text="counts.mine"
                            ></span>
                        </button>
                        <button
                            type="button"
                            @click="setScope('closed')"
                            class="flex w-full items-center justify-between gap-2 rounded-lg px-2.5 py-2 text-xs font-medium transition"
                            :class="scope === 'closed'
                                ? 'bg-emerald-50 text-emerald-800'
                                : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-800'"
                            :aria-current="scope === 'closed' ? 'page' : null"
                        >
                            <span>{{ __('talenma.jobs.filter_closed') }}</span>
                            <span
                                class="inline-flex min-w-[1.35rem] items-center justify-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums"
                                :class="scope === 'closed' ? 'bg-emerald-100/80 text-emerald-700' : 'bg-slate-100/90 text-slate-500'"
                                x-text="counts.closed"
                            ></span>
                        </button>
                    </nav>
                </div>
            </aside>

            <section class="relative rounded-2xl border border-slate-200/90 bg-white p-3.5 sm:p-5 min-w-0 shadow-sm shadow-slate-900/[0.03]">
                <div
                    class="mb-4 rounded-xl border bg-white px-3 py-2.5 sm:px-4"
                    :class="isAiSearchActive ? 'border-indigo-200 ring-1 ring-indigo-100' : 'border-gray-200'"
                >
                    <div class="relative min-w-0">
                        <label class="sr-only" for="company-jobs-ai-prompt">{{ __('talenma.jobs.ai_filters_title') }}</label>
                        <input
                            id="company-jobs-ai-prompt"
                            type="text"
                            x-model="aiPrompt"
                            maxlength="500"
                            :disabled="aiLoading"
                            placeholder="{{ __('talenma.jobs.ai_filters_placeholder') }}"
                            class="block w-full rounded-lg border-gray-300 py-1.5 pl-2.5 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-400"
                            @keydown.enter.prevent="applyAiFilters()"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0.5 right-0.5 inline-flex w-8 items-center justify-center rounded-md bg-indigo-600 text-white hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="aiLoading || ! aiPrompt.trim()"
                            :aria-label="aiLoading ? labels.aiFiltersLoading : @js(__('talenma.jobs.ai_filters_apply'))"
                            :title="aiLoading ? labels.aiFiltersLoading : @js(__('talenma.jobs.ai_filters_apply'))"
                            @click="applyAiFilters()"
                        >
                            <svg
                                x-show="! aiLoading"
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    fill="currentColor"
                                    d="M12 2.2c.35 2.85 1.55 5.05 3.55 7.05S20.95 12.65 23.8 13c-2.85.35-5.05 1.55-7.05 3.55S12.35 20.95 12 23.8c-.35-2.85-1.55-5.05-3.55-7.05S3.05 13.35.2 13c2.85-.35 5.05-1.55 7.05-3.55S11.65 5.05 12 2.2z"
                                />
                            </svg>
                            <svg
                                x-show="aiLoading"
                                x-cloak
                                class="h-4 w-4 animate-spin"
                                fill="none"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                    <div
                        x-show="isAiSearchActive && aiSummary"
                        x-cloak
                        class="mt-2 flex items-start gap-2 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs text-indigo-900"
                    >
                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-indigo-600" viewBox="0 0 24 24" aria-hidden="true">
                            <path
                                fill="currentColor"
                                d="M12 2.2c.35 2.85 1.55 5.05 3.55 7.05S20.95 12.65 23.8 13c-2.85.35-5.05 1.55-7.05 3.55S12.35 20.95 12 23.8c-.35-2.85-1.55-5.05-3.55-7.05S3.05 13.35.2 13c2.85-.35 5.05-1.55 7.05-3.55S11.65 5.05 12 2.2z"
                            />
                        </svg>
                        <p class="min-w-0 flex-1 leading-snug" x-text="aiSummaryLabel"></p>
                        <button
                            type="button"
                            class="shrink-0 rounded px-1 text-indigo-500 hover:bg-indigo-100 hover:text-indigo-800"
                            @click="clearAiSearchAndRefresh()"
                            :aria-label="@js(__('talenma.jobs.ai_filters_clear'))"
                            :title="@js(__('talenma.jobs.ai_filters_clear'))"
                        >&times;</button>
                    </div>
                    <p
                        x-show="aiError"
                        x-cloak
                        class="mt-2 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs text-red-700"
                        x-text="aiError"
                    ></p>
                </div>

                <div class="mb-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        <span x-text="scopeLabel"></span>
                        <span class="ml-1.5 inline-flex items-center rounded-md bg-emerald-50 px-1.5 py-0.5 text-xs font-semibold text-emerald-700 tabular-nums" x-text="total"></span>
                    </h3>
                    <span x-show="loading" class="text-[11px] font-medium text-slate-400" x-cloak>{{ __('talenma.jobs.filter_loading') }}</span>
                </div>

                <div x-show="error" x-cloak class="mb-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700" x-text="error"></div>

                <template x-if="!loading && jobs.length === 0">
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/80 px-4 py-12 text-center">
                        <p class="text-sm text-slate-500" x-text="emptyMessage"></p>
                        <a
                            href="{{ route('company.jobs.create') }}"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800"
                            x-show="['company', 'mine'].includes(scope) && ! isAiSearchActive"
                        >
                            {{ __('talenma.jobs.create') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </template>

                <ul x-show="jobs.length > 0" class="space-y-2.5" :class="{ 'opacity-60': loading }">
                    <template x-for="job in jobs" :key="job.id">
                        <li>
                            <a
                                :href="job.url"
                                class="group relative flex overflow-hidden rounded-xl bg-white ring-1 ring-slate-200/90 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                                :class="job.tone.hover"
                            >
                                <span class="absolute inset-y-0 left-0 w-1" :class="job.tone.bar" aria-hidden="true"></span>
                                <div class="flex min-w-0 flex-1 flex-col justify-center gap-1.5 py-3 pl-4 pr-3.5">
                                    <div class="flex items-start justify-between gap-3">
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
                                    <div
                                        x-show="job.creator || job.published_label || job.closed_label"
                                        class="flex flex-wrap items-center gap-1.5"
                                    >
                                        <span
                                            x-show="job.creator"
                                            class="inline-flex max-w-full items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100"
                                        >
                                            <svg class="h-3.5 w-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                            </svg>
                                            <span class="truncate">
                                                <span class="font-medium text-emerald-700/80" x-text="labels.byAuthor"></span>
                                                <span x-text="' ' + job.creator"></span>
                                            </span>
                                        </span>
                                        <span
                                            x-show="job.published_label"
                                            class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200"
                                            x-text="job.published_label"
                                        ></span>
                                        <span
                                            x-show="job.closed_label"
                                            class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200"
                                            x-text="job.closed_label"
                                        ></span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>
