@php
    $kpiColsClass = match (count($dashboard['kpis'])) {
        7 => 'xl:grid-cols-7',
        6 => 'xl:grid-cols-6',
        5 => 'xl:grid-cols-5',
        4 => 'xl:grid-cols-4',
        default => 'xl:grid-cols-3',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.dashboard.admin.title') }}</h2>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- En-tête admin --}}
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center gap-6">
                <x-user-avatar :user="Auth::user()" size="md" class="mx-auto lg:mx-0 ring-1 ring-gray-200" />
                <div class="flex-1 min-w-0 text-center lg:text-left">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-lg font-semibold text-gray-900">{{ __('talenma.dashboard.welcome', ['name' => $dashboard['actor']['name']]) }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $dashboard['actor']['role'] === 'admin' ? 'bg-violet-100 text-violet-700' : 'bg-purple-100 text-purple-700' }}">
                            {{ $dashboard['actor']['role_label'] }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $dashboard['actor']['email'] }}</p>
                </div>
            </div>
        </div>

        <div @class([
            'grid gap-6 items-start',
            'lg:grid-cols-3' => Auth::user()->isAdmin(),
        ])>
            {{-- Colonne principale --}}
            <div @class([
                'space-y-6 min-w-0',
                'lg:col-span-2' => Auth::user()->isAdmin(),
            ])>
                {{-- Statistiques --}}
                <section class="bg-white rounded-xl border overflow-hidden">
                    <div class="px-3 py-2 border-b bg-slate-50">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-700">{{ __('talenma.dashboard.admin.section_stats') }}</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 {{ $kpiColsClass }} divide-x divide-y divide-slate-100">
                        @foreach ($dashboard['kpis'] as $kpi)
                            @php
                                $valueClass = match ($kpi['tone'] ?? 'slate') {
                                    'amber' => 'text-amber-700',
                                    'indigo' => 'text-indigo-700',
                                    'emerald' => 'text-emerald-700',
                                    'sky' => 'text-sky-700',
                                    'violet' => 'text-violet-700',
                                    default => 'text-slate-900',
                                };
                                $cellClass = ($kpi['tone'] ?? 'slate') === 'amber' && (int) $kpi['value'] > 0
                                    ? 'bg-amber-50/60'
                                    : (($kpi['tone'] ?? '') === 'violet' && (int) $kpi['value'] > 0 ? 'bg-violet-50/50' : 'bg-white');
                            @endphp
                            <div class="flex flex-col gap-1 px-3 py-3 {{ $cellClass }}">
                                <span class="text-xs leading-snug text-slate-600">{{ $kpi['label'] }}</span>
                                <span class="text-xl font-bold tabular-nums {{ $valueClass }}">{{ $kpi['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Sourcing — demandes ouvertes --}}
                <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </span>
                            <div class="min-w-0">
                                <p class="inline-flex items-center gap-2 text-sm font-bold tracking-tight text-slate-900">
                                    {{ __('talenma.dashboard.admin.activity_title') }}
                                    @if ($sourcingUnseen ?? false)
                                        @foreach (range(1, 3) as $dot)
                                            <span class="relative flex h-2.5 w-2.5" @if ($loop->first) title="{{ __('talenma.recruitment.nav_new') }}" @endif>
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                            </span>
                                        @endforeach
                                        <span class="sr-only">{{ __('talenma.recruitment.nav_new') }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">{{ __('talenma.dashboard.admin.activity_subtitle') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.recruitment.index') }}" class="shrink-0 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ __('talenma.dashboard.admin.activity_all') }}
                        </a>
                    </div>

                    @if (($sourcingRequests ?? collect())->isEmpty())
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.admin.activity_empty') }}</p>
                        </div>
                    @else
                        <ul class="mt-4 max-h-[22rem] space-y-2.5 overflow-y-auto overscroll-contain pr-1">
                            @foreach ($sourcingRequests as $req)
                                @php
                                    $tone = match ($req->status) {
                                        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
                                        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'completed_successful', 'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                        'completed_unsuccessful', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    $reqUnseen = $req->hasUnseenChangesForStaff();
                                @endphp
                                <li>
                                    <a
                                        href="{{ route('admin.recruitment.show', $req) }}"
                                        class="group flex items-start gap-3 rounded-xl bg-white/90 px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-1.5 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $req->displayTitle() }}</span>
                                                        <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $tone }}">
                                                            {{ $req->statusLabel() }}
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-xs text-slate-500">{{ $req->companyDisplayName() }}</p>
                                                </div>
                                                <time class="shrink-0 text-xs font-medium text-slate-400 sm:pt-0.5" datetime="{{ $req->created_at?->toIso8601String() }}">{{ $req->created_at?->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                        @if ($reqUnseen)
                                            <span class="relative flex h-2.5 w-2.5 shrink-0 self-center" title="{{ __('talenma.recruitment.nav_new') }}">
                                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                            </span>
                                            <span class="sr-only">{{ __('talenma.recruitment.nav_new') }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Activité récente --}}
                <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.dashboard.admin.recent_activity_title') }}</p>
                        </div>
                    </div>

                    @if (empty($recentActivity ?? []))
                        <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                            <p class="text-sm text-slate-500">{{ __('talenma.dashboard.admin.recent_activity_empty') }}</p>
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
                                        'recruitment_message' => __('talenma.dashboard.admin.activity_recruitment_message_'.$mode, [
                                            'actor' => $item['actor'],
                                            'subject' => $item['subject'] ?? '',
                                        ]),
                                        'recruitment_message_sent' => __(
                                            'talenma.dashboard.admin.activity_recruitment_message_sent_'.$mode.($self ? '_self' : ''),
                                            ['actor' => $item['actor'], 'subject' => $item['subject'] ?? ''],
                                        ),
                                        'inbox_message' => __('talenma.dashboard.admin.activity_inbox_message', [
                                            'actor' => $item['actor'],
                                        ]),
                                        'inbox_message_sent' => __(
                                            'talenma.dashboard.admin.activity_inbox_message_sent'.($self ? '_self' : ''),
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
                                        'recruitment_message', 'recruitment_message_sent', 'inbox_message', 'inbox_message_sent' => 'bg-violet-500',
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
            </div>

            {{-- Colonne droite --}}
            @if (Auth::user()->isAdmin())
                <aside class="min-w-0 lg:sticky lg:top-24">
                    <section class="bg-white rounded-2xl border overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.dashboard.admin.section_moderation') }}</h3>
                        </div>
                        @if ($dashboard['pending_moderation_requests']->isEmpty())
                            <p class="px-6 py-6 text-sm text-gray-500">{{ __('talenma.dashboard.admin.moderation_empty') }}</p>
                        @else
                            <div class="divide-y max-h-[32rem] overflow-y-auto">
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
                </aside>
            @endif
        </div>
    </div>
</x-app-layout>
