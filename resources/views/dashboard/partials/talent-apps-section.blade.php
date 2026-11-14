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
