@php
    $toneClasses = [
        'amber' => 'bg-amber-50 border-amber-200 text-amber-900',
        'indigo' => 'bg-indigo-50 border-indigo-200 text-indigo-900',
        'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'sky' => 'bg-sky-50 border-sky-200 text-sky-900',
        'violet' => 'bg-violet-50 border-violet-200 text-violet-900',
        'slate' => 'bg-white border-gray-200 text-gray-900',
    ];

    $kpiValueClasses = [
        'amber' => 'text-amber-700',
        'indigo' => 'text-indigo-700',
        'emerald' => 'text-emerald-700',
        'sky' => 'text-sky-700',
        'violet' => 'text-violet-700',
        'slate' => 'text-gray-900',
    ];

    $alertToneClasses = [
        'amber' => 'bg-amber-50 border-amber-200 text-amber-900',
        'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'violet' => 'bg-violet-50 border-violet-200 text-violet-900',
    ];

    $breakdown = $dashboard['user_breakdown'];
    $platform = $dashboard['platform'];
    $talentTotal = max(1, $breakdown['talents_pending'] + $breakdown['talents_approved'] + $breakdown['talents_rejected']);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.dashboard.admin.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.dashboard.admin.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- En-tête admin --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                <div class="w-14 h-14 rounded-2xl bg-violet-100 text-violet-700 flex items-center justify-center text-xl font-bold shrink-0">
                    {{ strtoupper(substr($dashboard['actor']['name'], 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-lg font-semibold text-gray-900">{{ __('talenma.dashboard.welcome', ['name' => $dashboard['actor']['name']]) }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $dashboard['actor']['role'] === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $dashboard['actor']['role_label'] }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $dashboard['actor']['email'] }}</p>
                    <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-500">
                        <span>{{ __('talenma.dashboard.admin.member_since', ['date' => $dashboard['actor']['member_since']]) }}</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dashboard['actor']['email_verified'] ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                            {{ $dashboard['actor']['email_verified'] ? __('talenma.dashboard.admin.email_verified') : __('talenma.dashboard.admin.email_unverified') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertes --}}
        @if (count($dashboard['alerts']) > 0)
            <div class="space-y-3">
                @foreach ($dashboard['alerts'] as $alert)
                    <a
                        href="{{ $alert['href'] }}"
                        class="block rounded-xl border px-4 py-3 text-sm font-medium transition hover:opacity-90 {{ $alertToneClasses[$alert['tone']] ?? $alertToneClasses['amber'] }}"
                    >
                        {{ $alert['message'] }} →
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Activité sourcing / intermédiation --}}
        <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.dashboard.admin.activity_title') }}</p>
                        <p class="text-xs text-slate-500">{{ __('talenma.dashboard.admin.activity_subtitle') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.recruitment.index') }}" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    {{ __('talenma.dashboard.admin.activity_all') }}
                </a>
            </div>

            @if (empty($recentActivity ?? []))
                <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                    <p class="text-sm text-slate-500">{{ __('talenma.dashboard.admin.activity_empty') }}</p>
                </div>
            @else
                <ol class="relative mt-4 max-h-[22rem] space-y-2 overflow-y-auto overscroll-contain pr-1 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-indigo-100">
                    @foreach ($recentActivity as $item)
                        @php
                            $mode = (($item['detail'] ?? 'open') === 'named') ? 'named' : 'open';
                            $self = ($item['self'] ?? false) === true;
                            $label = match ($item['type']) {
                                'recruitment_submitted' => __('talenma.dashboard.admin.activity_recruitment_submitted_'.$mode, [
                                    'actor' => $item['actor'],
                                    'subject' => $item['subject'] ?? '',
                                ]),
                                'recruitment_message' => __('talenma.dashboard.admin.activity_recruitment_message', [
                                    'actor' => $item['actor'],
                                ]),
                                'recruitment_message_sent' => __(
                                    'talenma.dashboard.admin.activity_recruitment_message_sent'.($self ? '_self' : ''),
                                    ['actor' => $item['actor']],
                                ),
                                'recruitment_comment' => __(
                                    'talenma.dashboard.admin.activity_recruitment_comment'.($self ? '_self' : ''),
                                    ['actor' => $item['actor']],
                                ),
                                'recruitment_status' => match ($item['result'] ?? '') {
                                    'in_progress' => __(
                                        'talenma.dashboard.admin.activity_recruitment_taken_'.$mode.($self ? '_self' : ''),
                                        ['actor' => $item['actor'], 'subject' => $item['subject'] ?? ''],
                                    ),
                                    'completed_successful', 'completed' => __(
                                        'talenma.dashboard.admin.activity_recruitment_closed_successful_'.$mode.($self ? '_self' : ''),
                                        ['actor' => $item['actor'], 'subject' => $item['subject'] ?? ''],
                                    ),
                                    'completed_unsuccessful', 'cancelled' => __(
                                        'talenma.dashboard.admin.activity_recruitment_closed_unsuccessful_'.$mode.($self ? '_self' : ''),
                                        ['actor' => $item['actor'], 'subject' => $item['subject'] ?? ''],
                                    ),
                                    'pending' => __(
                                        'talenma.dashboard.admin.activity_recruitment_reopened_'.$mode.($self ? '_self' : ''),
                                        ['actor' => $item['actor'], 'subject' => $item['subject'] ?? ''],
                                    ),
                                    default => __('talenma.dashboard.admin.activity_recruitment_status', [
                                        'actor' => $item['actor'],
                                        'subject' => $item['subject'] ?? '',
                                        'result' => $item['result'] ?? '',
                                    ]),
                                },
                                default => $item['actor'],
                            };
                            $dotClass = match ($item['type'] ?? '') {
                                'recruitment_message', 'recruitment_message_sent' => 'bg-violet-500',
                                'recruitment_submitted' => 'bg-sky-500',
                                default => 'bg-amber-500',
                            };
                        @endphp
                        <li class="relative pl-10">
                            <span class="absolute left-3 top-4 z-10 flex h-3.5 w-3.5 items-center justify-center">
                                <span class="absolute inline-flex h-full w-full rounded-full {{ $dotClass }} opacity-25"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full {{ $dotClass }} ring-2 ring-white"></span>
                            </span>
                            @if (! empty($item['href']))
                                <a href="{{ $item['href'] }}" class="group flex flex-col gap-1.5 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200 hover:bg-sky-50/80 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                    <p class="text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $label }}</p>
                                    <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                </a>
                            @else
                                <div class="flex flex-col gap-1.5 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                    <p class="text-sm font-medium leading-snug text-slate-900">{{ $label }}</p>
                                    <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $item['at']?->toIso8601String() }}">{{ $item['at']?->diffForHumans() }}</time>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach ($dashboard['kpis'] as $kpi)
                @if ($kpi['href'])
                    <a href="{{ $kpi['href'] }}" class="rounded-2xl border p-4 sm:p-5 transition hover:shadow-sm {{ $toneClasses[$kpi['tone']] ?? $toneClasses['slate'] }}">
                        <p class="text-xs font-medium uppercase tracking-wide opacity-80">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-2xl sm:text-3xl font-bold {{ $kpiValueClasses[$kpi['tone']] ?? $kpiValueClasses['slate'] }}">{{ $kpi['value'] }}</p>
                    </a>
                @else
                    <div class="rounded-2xl border p-4 sm:p-5 {{ $toneClasses[$kpi['tone']] ?? $toneClasses['slate'] }}">
                        <p class="text-xs font-medium uppercase tracking-wide opacity-80">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-2xl sm:text-3xl font-bold {{ $kpiValueClasses[$kpi['tone']] ?? $kpiValueClasses['slate'] }}">{{ $kpi['value'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Répartition --}}
                <section class="bg-white rounded-2xl border p-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_users') }}</h3>
                    <div class="mt-5 space-y-4">
                        @foreach ([
                            ['label' => __('talenma.dashboard.admin.users_talents_pending'), 'value' => $breakdown['talents_pending'], 'color' => 'bg-amber-500', 'pct' => round($breakdown['talents_pending'] / $talentTotal * 100)],
                            ['label' => __('talenma.dashboard.admin.users_talents_approved'), 'value' => $breakdown['talents_approved'], 'color' => 'bg-indigo-500', 'pct' => round($breakdown['talents_approved'] / $talentTotal * 100)],
                            ['label' => __('talenma.dashboard.admin.users_talents_rejected'), 'value' => $breakdown['talents_rejected'], 'color' => 'bg-red-400', 'pct' => round($breakdown['talents_rejected'] / $talentTotal * 100)],
                        ] as $row)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1.5">
                                    <span class="text-gray-600">{{ $row['label'] }}</span>
                                    <span class="font-semibold text-gray-900">{{ $row['value'] }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full {{ $row['color'] }} rounded-full transition-all" style="width: {{ max($row['pct'], $row['value'] > 0 ? 4 : 0) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div class="rounded-xl bg-gray-50 px-3 py-3">
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.users_companies_pending') }}</dt>
                            <dd class="mt-1 text-lg font-bold {{ $breakdown['companies_pending'] > 0 ? 'text-amber-700' : 'text-gray-900' }}">{{ $breakdown['companies_pending'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3">
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.users_companies') }}</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">{{ $breakdown['companies'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3">
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.users_moderators') }}</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">{{ $breakdown['moderators'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3">
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.users_registrations_7d') }}</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">{{ $breakdown['registrations_7d'] }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-3 py-3">
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.users_registrations_30d') }}</dt>
                            <dd class="mt-1 text-lg font-bold text-gray-900">{{ $breakdown['registrations_30d'] }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- Talents en attente --}}
                <section class="bg-white rounded-2xl border overflow-hidden">
                    <div class="px-6 py-4 border-b flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_recent_pending') }}</h3>
                        <a href="{{ route('admin.users.index', ['filter' => 'pending']) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.admin.view_all_pending') }} →
                        </a>
                    </div>
                    @if ($dashboard['recent_pending_talents']->isEmpty())
                        <p class="px-6 py-8 text-sm text-gray-500">{{ __('talenma.dashboard.admin.recent_pending_empty') }}</p>
                    @else
                        <div class="divide-y">
                            @foreach ($dashboard['recent_pending_talents'] as $talent)
                                <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium text-gray-900">{{ $talent['name'] }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $talent['role'] === 'company' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }}">
                                                {{ $talent['role_label'] }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate">{{ $talent['email'] }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $talent['sector'] }} · {{ $talent['registered_at'] }}</p>
                                    </div>
                                    <a href="{{ route('admin.users.index', ['filter' => 'pending']) }}" class="shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                                        {{ __('talenma.dashboard.admin.review_registration') }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                {{-- Dernières inscriptions --}}
                <section class="bg-white rounded-2xl border overflow-hidden">
                    <div class="px-6 py-4 border-b flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_recent_registrations') }}</h3>
                        <a href="{{ route('admin.users.index', ['filter' => 'all']) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.admin.view_all_users') }} →
                        </a>
                    </div>
                    @if ($dashboard['recent_registrations']->isEmpty())
                        <p class="px-6 py-8 text-sm text-gray-500">{{ __('talenma.dashboard.admin.recent_registrations_empty') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3 font-medium">{{ __('talenma.auth.full_name') }}</th>
                                        <th class="px-6 py-3 font-medium">{{ __('talenma.admin.users.role') }}</th>
                                        <th class="px-6 py-3 font-medium">{{ __('talenma.admin.users.registration_registered_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($dashboard['recent_registrations'] as $registration)
                                        <tr>
                                            <td class="px-6 py-3">
                                                <p class="font-medium text-gray-900">{{ $registration['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $registration['email'] }}</p>
                                            </td>
                                            <td class="px-6 py-3 text-gray-600">
                                                {{ $registration['role'] === 'company' ? __('talenma.dashboard.admin.role_company') : __('talenma.dashboard.admin.role_talent') }}
                                                @if ($registration['role'] === 'dev' && $registration['approval_status'])
                                                    <span class="text-xs text-gray-400">· {{ __('talenma.dashboard.admin.status_'.$registration['approval_status']) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-3 text-gray-500 whitespace-nowrap">{{ $registration['registered_at'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-6">
                {{-- Actions rapides --}}
                <section class="bg-white rounded-2xl border p-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_quick_actions') }}</h3>
                    <div class="mt-4 space-y-2">
                        @foreach ($dashboard['quick_actions'] as $action)
                            <a
                                href="{{ $action['href'] }}"
                                class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                            >
                                <span>{{ $action['label'] }}</span>
                                @if ($action['badge'])
                                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 rounded-full bg-indigo-600 text-white text-xs font-bold">{{ $action['badge'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- Plateforme --}}
                <section class="bg-white rounded-2xl border p-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_platform') }}</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.platform_recruitment') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900">
                                <a href="{{ route('admin.recruitment.index') }}" class="text-indigo-600 hover:text-indigo-800">
                                    {{ __('talenma.dashboard.admin.platform_recruitment_pending', ['pending' => $platform['recruitment_pending'], 'total' => $platform['recruitment_total']]) }} →
                                </a>
                            </dd>
                        </div>
                        @if (Auth::user()->isAdmin())
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.platform_news') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900">{{ $platform['news_items'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.platform_social') }}</dt>
                                <dd class="mt-1 font-medium text-gray-900">{{ $platform['social_posts'] }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-gray-500">{{ __('talenma.dashboard.admin.platform_catalog') }}</dt>
                            <dd class="mt-1 font-medium text-gray-900">
                                {{ __('talenma.dashboard.admin.platform_catalog_detail', ['sectors' => $platform['sectors'], 'professions' => $platform['professions']]) }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Actions modérateur (admin) --}}
                @if (Auth::user()->isAdmin())
                    <section class="bg-white rounded-2xl border overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_moderation') }}</h3>
                        </div>
                        @if ($dashboard['pending_moderation_requests']->isEmpty())
                            <p class="px-6 py-6 text-sm text-gray-500">{{ __('talenma.dashboard.admin.moderation_empty') }}</p>
                        @else
                            <div class="divide-y">
                                @foreach ($dashboard['pending_moderation_requests'] as $request)
                                    <div class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $request['action'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $request['requester'] }}
                                            @if ($request['target'])
                                                — {{ $request['target'] }}
                                            @endif
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
