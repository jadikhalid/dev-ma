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
                                        <p class="font-medium text-gray-900">{{ $talent['name'] }}</p>
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
                                {{ __('talenma.dashboard.admin.platform_recruitment_pending', ['pending' => $platform['recruitment_pending'], 'total' => $platform['recruitment_total']]) }}
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
