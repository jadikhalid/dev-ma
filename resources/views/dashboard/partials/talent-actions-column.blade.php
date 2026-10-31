<aside class="order-3 min-w-0 space-y-4 lg:order-none" data-talent-actions-column>
    {{-- Mes Activités (indicateurs) --}}
    <section class="space-y-3">
        <div class="flex min-h-[3.25rem] items-center rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
            <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.stats.title') }}</h2>
        </div>

        <div class="space-y-2.5">
            <div class="rounded-2xl border border-slate-200/90 bg-white p-3.5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.views') }}</p>
                        <p class="mt-1 text-xl font-bold tabular-nums text-slate-900">{{ number_format($stats['profile_views_7d']) }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                    </div>
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/90 bg-white p-3.5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.cv_downloads') }}</p>
                        <p class="mt-1 text-xl font-bold tabular-nums text-slate-900">{{ number_format($stats['cv_downloads_7d']) }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ __('talenma.dashboard.talent.stats.views_7d') }}</p>
                    </div>
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </span>
                </div>
            </div>

            <a
                href="{{ route('inbox.index') }}"
                @class([
                    'group block rounded-2xl border p-3.5 transition duration-150 hover:-translate-y-0.5 hover:shadow-md',
                    'border-indigo-200 bg-indigo-50/60 hover:bg-indigo-50 hover:ring-1 hover:ring-indigo-200' => $stats['unread_messages'] > 0,
                    'border-slate-200/90 bg-white hover:border-indigo-200 hover:bg-indigo-50/40' => $stats['unread_messages'] === 0,
                ])
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.unread_messages') }}</p>
                        <p class="mt-1 text-xl font-bold tabular-nums text-slate-900">{{ number_format($stats['unread_messages']) }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-indigo-600 group-hover:text-indigo-800">{{ __('talenma.dashboard.talent.stats.open_inbox') }}</p>
                    </div>
                    <span @class([
                        'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                        'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' => $stats['unread_messages'] > 0,
                        'bg-blue-50 text-blue-600' => $stats['unread_messages'] === 0,
                    ]) aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                    </span>
                </div>
            </a>

            <a
                href="{{ route('talent.direct-hire.index') }}"
                @class([
                    'group block rounded-2xl border p-3.5 transition duration-150 hover:-translate-y-0.5 hover:shadow-md',
                    'border-indigo-200 bg-indigo-50/60 hover:bg-indigo-50 hover:ring-1 hover:ring-indigo-200' => $openDirectHires > 0,
                    'border-slate-200/90 bg-white hover:border-indigo-200 hover:bg-indigo-50/40' => $openDirectHires === 0,
                ])
            >
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.dashboard.talent.stats.direct_hire') }}</p>
                        <p class="mt-1 text-xl font-bold tabular-nums text-slate-900">{{ number_format($openDirectHires) }}</p>
                        <p class="mt-0.5 text-[11px] font-semibold text-indigo-600 group-hover:text-indigo-800">{{ __('talenma.dashboard.talent.stats.open_direct_hire') }}</p>
                    </div>
                    <span @class([
                        'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                        'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' => $openDirectHires > 0,
                        'bg-indigo-50 text-indigo-600' => $openDirectHires === 0,
                    ]) aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                    </span>
                </div>
            </a>
        </div>
    </section>

    {{-- Mes applications --}}
    <section class="space-y-3" data-dashboard-apps>
        <div class="flex min-h-[3.25rem] items-center rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4">
            <h2 class="text-sm font-bold uppercase tracking-[0.11em] text-indigo-900">{{ __('talenma.dashboard.talent.apps_section_title') }}</h2>
        </div>
        <p class="-mt-1 text-xs text-slate-500" data-dashboard-apps-subtitle>{{ __('talenma.dashboard.talent.apps_section_subtitle') }}</p>

        <div class="space-y-2.5">
            <a
                href="{{ route('talent.cv-builder.index') }}"
                class="group flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 transition duration-150 hover:-translate-y-0.5 hover:border-indigo-200 hover:bg-indigo-50/40 hover:shadow-md"
                data-dashboard-app="cv-builder"
            >
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 group-hover:bg-indigo-200 transition" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-800">{{ __('talenma.nav.apps_launcher_cv_builder') }}</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ __('talenma.dashboard.talent.apps_tile_cv_desc') }}</p>
                </div>
            </a>

            <a
                href="{{ route('talent.ats-score.index') }}"
                class="group flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 transition duration-150 hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50/40 hover:shadow-md"
                data-dashboard-app="ats-score"
            >
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-emerald-200 transition" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900 group-hover:text-emerald-800">{{ __('talenma.nav.apps_launcher_ats_score') }}</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ __('talenma.dashboard.talent.apps_tile_ats_desc') }}</p>
                </div>
            </a>

            <a
                href="{{ route('talent.library.index') }}"
                class="group flex items-start gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 transition duration-150 hover:-translate-y-0.5 hover:border-amber-200 hover:bg-amber-50/40 hover:shadow-md"
                data-dashboard-app="library"
            >
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 group-hover:bg-amber-200 transition" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900 group-hover:text-amber-900">{{ __('talenma.nav.apps_launcher_library') }}</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-slate-500">{{ __('talenma.dashboard.talent.apps_tile_library_desc') }}</p>
                </div>
            </a>
        </div>
    </section>
</aside>
