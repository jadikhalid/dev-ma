@php
    $statusTone = [
        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
        'completed_successful' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'completed_unsuccessful' => 'bg-rose-50 text-rose-800 border-rose-200',
        'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
    ];
    $modeTone = [
        'named' => 'bg-violet-50 text-violet-800 border-violet-200',
        'open' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.admin_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.recruitment.admin_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-wrap gap-2">
            @foreach ([
                'all' => __('talenma.recruitment.mode_filter_all'),
                'named' => __('talenma.recruitment.mode_named'),
                'open' => __('talenma.recruitment.mode_open'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.recruitment.index', ['filter' => $filter, 'mode' => $key]) }}"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold border transition',
                        'bg-violet-600 text-white border-violet-600' => $mode === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $mode !== $key,
                    ])
                >
                    {{ $label }}
                    <span @class([
                        'text-xs px-1.5 py-0.5 rounded-full',
                        'bg-white/20' => $mode === $key,
                        'bg-gray-100 text-gray-600' => $mode !== $key,
                    ])>{{ $modeCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'pending' => __('talenma.recruitment.status_pending'),
                'in_progress' => __('talenma.recruitment.status_in_progress'),
                'completed_successful' => __('talenma.recruitment.status_completed_successful'),
                'completed_unsuccessful' => __('talenma.recruitment.status_completed_unsuccessful'),
                'all' => __('talenma.recruitment.filter_all'),
            ] as $key => $label)
                <a
                    href="{{ route('admin.recruitment.index', ['filter' => $key, 'mode' => $mode]) }}"
                    @class([
                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold border transition',
                        'bg-indigo-600 text-white border-indigo-600' => $filter === $key,
                        'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $filter !== $key,
                    ])
                >
                    {{ $label }}
                    <span @class([
                        'text-xs px-1.5 py-0.5 rounded-full',
                        'bg-white/20' => $filter === $key,
                        'bg-gray-100 text-gray-600' => $filter !== $key,
                    ])>{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        @if ($requests->isEmpty())
            <div class="bg-white rounded-2xl border p-8 text-center text-sm text-gray-500">
                {{ __('talenma.recruitment.admin_empty') }}
            </div>
        @else
            <ul class="space-y-2.5">
                @foreach ($requests as $req)
                    @php
                        $reqUnseen = $req->hasUnseenChangesForStaff();
                    @endphp
                    <li>
                        <a
                            href="{{ route('admin.recruitment.show', $req) }}"
                            class="group flex items-start gap-3 rounded-xl bg-white px-3.5 py-3 ring-1 ring-slate-200/80 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:ring-indigo-200"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-1.5 sm:flex-row sm:items-start sm:justify-between sm:gap-3">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-medium leading-snug text-slate-900 group-hover:text-indigo-800">{{ $req->displayTitle() }}</span>
                                            <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $modeTone[$req->mode] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                {{ $req->modeLabel() }}
                                            </span>
                                            <span class="inline-flex items-center rounded-md border px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $statusTone[$req->status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                {{ $req->statusLabel() }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $req->companyDisplayName() }}
                                            · {{ $req->created_at?->translatedFormat('d M Y, H:i') }}
                                        </p>
                                    </div>
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

            <div>
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
