@php
    /** @var \App\Models\CompanyProfile|null $profile */
    $canEditOrg = $canEditOrg ?? false;
    $owner = $profile?->user;
    $contactUrl = route('profile.edit', ['panel' => 'account']).'#account-contact-card';
    $usersUrl = route('profile.edit', ['panel' => 'account']).'#account-users-card';
    $ficheUrl = route('profile.edit', ['panel' => 'company']).'#company-identity-card';
    $members = $profile?->memberships ?? collect();
    $location = collect([$profile?->city, $profile?->countryLabel()])->filter()->implode(', ');

    $contactComplete = $profile
        && filled($profile->representative_name)
        && filled($profile->phone)
        && filled($profile->linkedin_url)
        && filled($profile->representative_photo_path);
    $ficheRows = [
        ['label' => __('talenma.company.sector'), 'value' => $profile?->sector],
        ['label' => __('talenma.company.employees'), 'value' => $profile?->employee_count],
        ['label' => __('talenma.company.check_location'), 'value' => $location !== '' ? $location : null],
        ['label' => __('talenma.company.website'), 'value' => $profile?->website],
    ];
@endphp

<aside class="order-2 min-w-0 space-y-3 lg:order-none" data-company-org-column>
    <div class="rounded-2xl border border-indigo-200/70 bg-indigo-100/70 px-4 py-3.5">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </span>
            <h2 class="text-sm font-bold tracking-tight text-indigo-900">{{ __('talenma.dashboard.company.my_company') }}</h2>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5">
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company.section_contact') }}</h3>
            @if ($canEditOrg)
                <a href="{{ $contactUrl }}" class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white" title="{{ $contactComplete ? __('talenma.dashboard.company.invite_change') : __('talenma.dashboard.company.invite_complete') }}">
                    <span class="sr-only">{{ $contactComplete ? __('talenma.dashboard.company.invite_change') : __('talenma.dashboard.company.invite_complete') }}</span>
                    @if ($contactComplete)
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    @else
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    @endif
                </a>
            @endif
        </div>
        <div class="mt-3 flex items-start gap-2.5">
            <x-company-contact-photo :profile="$profile" size="sm" class="shrink-0" />
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-900">{{ $profile?->representative_name ?: '—' }}</p>
                <p class="truncate text-xs text-slate-500">{{ $owner?->email ?: '—' }}</p>
                <p class="truncate text-xs text-slate-500">{{ $profile?->phone ?: '—' }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5">
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company_users.title') }}</h3>
            @if ($canEditOrg)
                <a href="{{ $usersUrl }}" class="inline-flex h-7 w-7 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white" title="{{ $members->isEmpty() ? __('talenma.company_users.add') : __('talenma.dashboard.company.invite_change') }}">
                    <span class="sr-only">{{ $members->isEmpty() ? __('talenma.company_users.add') : __('talenma.dashboard.company.invite_change') }}</span>
                    @if ($members->isEmpty())
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    @else
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    @endif
                </a>
            @endif
        </div>
            @if ($members->isEmpty())
            <p class="mt-3 text-xs text-slate-500">{{ __('talenma.company_users.empty') }}</p>
        @else
            <ul class="mt-3 space-y-2">
                @foreach ($members->take(4) as $membership)
                    @php
                        $member = $membership->user;
                        $memberName = trim((string) (($member?->first_name ?? '').' '.($member?->last_name ?? '')));
                    @endphp
                    <li class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $memberName !== '' ? $memberName : ($member?->name ?: '—') }}</p>
                        @if (filled($membership->job_title))
                            <p class="truncate text-xs text-indigo-700">{{ $membership->job_title }}</p>
                        @endif
                        <p class="truncate text-xs text-slate-500">{{ $member?->email }}</p>
                    </li>
                @endforeach
            </ul>
            @if ($members->count() > 4)
                <p class="mt-2 text-[11px] font-semibold text-indigo-700">+{{ $members->count() - 4 }}</p>
            @endif
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-3.5">
        <div>
            <h3 class="text-[11px] font-bold uppercase tracking-[0.08em] text-indigo-900">{{ __('talenma.company.section_identity') }}</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">{{ __('talenma.company.section_identity_desc') }}</p>
        </div>
        <ul class="mt-3 space-y-2.5">
            @foreach ($ficheRows as $row)
                @php $filled = filled($row['value']); @endphp
                <li class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ $row['label'] }}</p>
                        <p class="truncate text-xs font-medium text-slate-900" title="{{ $filled ? $row['value'] : '' }}">{{ $filled ? $row['value'] : '—' }}</p>
                    </div>
                    @if ($canEditOrg)
                        <a href="{{ $ficheUrl }}" class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-[5px] border border-indigo-300 bg-indigo-50 text-indigo-700 hover:border-indigo-600 hover:bg-indigo-600 hover:text-white" title="{{ $filled ? __('talenma.dashboard.company.invite_change') : __('talenma.dashboard.company.invite_complete') }}">
                            <span class="sr-only">{{ $filled ? __('talenma.dashboard.company.invite_change') : __('talenma.dashboard.company.invite_complete') }}</span>
                            @if ($filled)
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                            @else
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            @endif
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
</aside>
