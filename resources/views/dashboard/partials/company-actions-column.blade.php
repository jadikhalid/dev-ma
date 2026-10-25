<aside class="order-3 min-w-0 space-y-3 lg:order-none" data-company-actions-column>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between gap-2 border-b border-indigo-100 bg-indigo-50/80 px-3.5 py-3">
            <h2 class="min-w-0 truncate text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.nav.sourcing') }}</h2>
            <a href="{{ route('sourcing.index') }}" class="shrink-0 text-[11px] font-semibold text-indigo-700 hover:text-indigo-900">
                {{ __('talenma.dashboard.company.sourcing_all') }}
            </a>
        </div>
        <div class="activity-scroll h-[calc(4*2.75rem+3*0.5rem)] overflow-y-auto overscroll-contain px-3.5 py-3">
            @if ($recentRequests->isEmpty())
                <p class="text-xs text-slate-500">{{ __('talenma.dashboard.company.ongoing_empty') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach ($recentRequests as $req)
                        <li class="min-w-0">
                            <a href="{{ route('sourcing.show', $req) }}" class="block min-w-0 rounded-lg px-1 py-1 hover:bg-indigo-50">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $req->displayTitle() }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $req->statusLabel() }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between gap-2 border-b border-indigo-100 bg-indigo-50/80 px-3.5 py-3">
            <h2 class="inline-flex min-w-0 items-center gap-1.5 text-sm font-bold tracking-tight text-indigo-900">
                <span class="truncate">{{ __('talenma.nav.direct_hire') }}</span>
                @if ($directHireUnseen ?? false)
                    <span class="relative flex h-2 w-2 shrink-0" title="{{ __('talenma.direct_hire.nav_new') }}">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                    </span>
                    <span class="sr-only">{{ __('talenma.direct_hire.nav_new') }}</span>
                @endif
            </h2>
            <a href="{{ route('company.direct-hire.index') }}" class="shrink-0 text-[11px] font-semibold text-indigo-700 hover:text-indigo-900">
                {{ __('talenma.dashboard.company.direct_hires_all') }}
            </a>
        </div>
        <div class="activity-scroll h-[calc(4*2.75rem+3*0.5rem)] overflow-y-auto overscroll-contain px-3.5 py-3">
            @if ($directHires->isEmpty())
                <p class="text-xs text-slate-500">{{ __('talenma.dashboard.company.direct_hires_empty') }}</p>
            @else
                <ul class="space-y-2">
                    @foreach ($directHires as $hire)
                        <li class="min-w-0">
                            <a href="{{ route('company.direct-hire.show', $hire) }}" class="flex items-center gap-2 rounded-lg px-1 py-1 hover:bg-indigo-50">
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-semibold text-slate-900">{{ $hire->shortSubject() }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $hire->talentDisplayName() }}</span>
                                </span>
                                @if ($hire->hasUnseenChangesForCompany())
                                    <span class="relative flex h-2 w-2 shrink-0" title="{{ __('talenma.direct_hire.nav_new') }}">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <a
        href="{{ route('company.search') }}"
        class="inline-flex w-full items-center justify-center rounded-[5px] border border-indigo-600 bg-indigo-600 px-3 py-2.5 text-center text-xs font-semibold text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-700"
    >
        {{ __('talenma.dashboard.company.browse') }}
    </a>

    <a
        href="{{ route('company.jobs.index') }}"
        class="inline-flex w-full items-center justify-center rounded-[5px] border border-emerald-600 bg-emerald-600 px-3 py-2.5 text-center text-xs font-semibold text-white shadow-sm shadow-emerald-600/20 hover:bg-emerald-700"
    >
        {{ __('talenma.dashboard.company.jobs_manage') }}
    </a>
</aside>
