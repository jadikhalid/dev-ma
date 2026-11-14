<div class="space-y-2.5" data-talent-stats-cards>
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
