@php
    /** @var \App\Models\CompanyProfile|null $profile */
    $publicCompany = $profile?->user;
    $publicCompanyUrl = $publicCompany
        ? route('companies.public.show', $publicCompany)
        : null;
@endphp

<aside class="order-3 min-w-0 space-y-3 lg:order-none" data-company-actions-column>
    @if ($publicCompanyUrl)
        <a
            href="{{ $publicCompanyUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="group relative isolate flex items-center gap-3.5 overflow-hidden rounded-2xl border border-indigo-200/70 bg-white px-3.5 py-3.5 shadow-sm shadow-indigo-500/5 transition duration-200 ease-out hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400/60 focus-visible:ring-offset-2"
            data-company-public-page-link
        >
            <span class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(120%_80%_at_0%_0%,rgba(99,102,241,0.14),transparent_55%),linear-gradient(135deg,rgba(238,242,255,0.95)_0%,rgba(255,255,255,0.92)_48%,rgba(248,250,252,0.98)_100%)]" aria-hidden="true"></span>
            <span class="pointer-events-none absolute -right-6 -top-8 -z-10 h-24 w-24 rounded-full bg-indigo-400/10 blur-2xl transition duration-300 group-hover:bg-indigo-400/20" aria-hidden="true"></span>
            <span class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-px bg-gradient-to-r from-transparent via-indigo-300/70 to-transparent" aria-hidden="true"></span>

            <span class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-indigo-100/90 transition duration-200 group-hover:ring-indigo-200 group-hover:shadow-md">
                @if ($profile?->logoUrl())
                    <img src="{{ $profile->logoUrl() }}" alt="" class="h-full w-full object-contain p-1.5">
                @else
                    <span class="text-xs font-bold tracking-wide text-indigo-700" aria-hidden="true">{{ $profile?->initials() }}</span>
                @endif
            </span>

            <span class="min-w-0 flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-700/85">{{ __('talenma.dashboard.company.public_page_label') }}</span>
                @if (filled($profile?->displayName()))
                    <span class="mt-0.5 block truncate text-sm font-bold tracking-tight text-slate-900 transition group-hover:text-indigo-900">{{ $profile->displayName() }}</span>
                @endif
                <span class="mt-0.5 flex items-center gap-1 text-xs font-medium text-slate-500 transition group-hover:text-indigo-600">
                    {{ __('talenma.dashboard.company.public_page_cta') }}
                    <svg class="h-3 w-3 shrink-0 opacity-70 transition duration-200 group-hover:translate-x-0.5 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </span>
            </span>

            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 transition duration-200 group-hover:bg-indigo-700 group-hover:shadow-md group-hover:shadow-indigo-600/30" aria-hidden="true">
                <svg class="h-4 w-4 transition duration-200 group-hover:-translate-y-px group-hover:translate-x-px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </span>
        </a>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between gap-2 border-b border-indigo-100 bg-indigo-50/80 px-3.5 py-3">
            <h2 class="min-w-0 truncate text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.nav.sourcing') }}</h2>
            <a href="{{ route('sourcing.index') }}" class="shrink-0 text-[11px] font-semibold text-indigo-700 hover:text-indigo-900">
                {{ __('talenma.dashboard.company.sourcing_all') }}
            </a>
        </div>
        <div @class([
            'activity-scroll h-[calc(4*2.75rem+3*0.5rem)] overflow-y-auto overscroll-y-auto px-3.5 py-3',
            'flex items-center justify-center' => $recentRequests->isEmpty(),
        ])>
            @if ($recentRequests->isEmpty())
                <p class="text-center text-xs text-slate-500">{{ __('talenma.dashboard.company.ongoing_empty') }}</p>
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
        <div @class([
            'activity-scroll h-[calc(4*2.75rem+3*0.5rem)] overflow-y-auto overscroll-y-auto px-3.5 py-3',
            'flex items-center justify-center' => $directHires->isEmpty(),
        ])>
            @if ($directHires->isEmpty())
                <p class="text-center text-xs text-slate-500">{{ __('talenma.dashboard.company.direct_hires_empty') }}</p>
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
