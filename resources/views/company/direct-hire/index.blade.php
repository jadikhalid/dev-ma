@php
    $toneClasses = fn (string $tone) => match ($tone) {
        'amber' => [
            'bar' => 'bg-amber-500',
            'badge' => 'bg-amber-50 text-amber-900 ring-amber-200',
            'hover' => 'hover:ring-amber-200 hover:bg-amber-50/40',
        ],
        'violet' => [
            'bar' => 'bg-violet-500',
            'badge' => 'bg-violet-50 text-violet-900 ring-violet-200',
            'hover' => 'hover:ring-violet-200 hover:bg-violet-50/40',
        ],
        'sky' => [
            'bar' => 'bg-sky-500',
            'badge' => 'bg-sky-50 text-sky-900 ring-sky-200',
            'hover' => 'hover:ring-sky-200 hover:bg-sky-50/40',
        ],
        'emerald' => [
            'bar' => 'bg-emerald-500',
            'badge' => 'bg-emerald-50 text-emerald-900 ring-emerald-200',
            'hover' => 'hover:ring-emerald-200 hover:bg-emerald-50/40',
        ],
        'rose' => [
            'bar' => 'bg-rose-500',
            'badge' => 'bg-rose-50 text-rose-900 ring-rose-200',
            'hover' => 'hover:ring-rose-200 hover:bg-rose-50/40',
        ],
        default => [
            'bar' => 'bg-slate-400',
            'badge' => 'bg-slate-100 text-slate-800 ring-slate-200',
            'hover' => 'hover:ring-slate-300 hover:bg-slate-50',
        ],
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.company_index_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.company_index_subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="direct_hire" />

    <div class="py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 lg:items-start">
            {{-- Colonne : en cours --}}
            <section class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/70 via-white to-white p-3.5 sm:p-4 min-w-0">
                <div class="mb-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('talenma.direct_hire.company_index_open') }}
                        <span class="ml-1.5 text-xs font-semibold text-indigo-600">{{ $openRequests->count() }}</span>
                    </h3>
                </div>

                @if ($openRequests->isEmpty())
                    <p class="rounded-lg border border-dashed border-slate-200 bg-white/80 px-3 py-4 text-center text-sm text-slate-500">
                        {{ __('talenma.direct_hire.company_index_open_empty') }}
                    </p>
                @else
                    <ul class="space-y-2">
                        @foreach ($openRequests as $hire)
                            @php
                                $tones = $toneClasses($hire->statusTone());
                                $progress = $hire->progressLabel();
                            @endphp
                            <li>
                                <a
                                    href="{{ route('company.direct-hire.show', $hire) }}"
                                    class="group relative flex overflow-hidden rounded-lg bg-white ring-1 ring-slate-200/90 shadow-sm transition duration-150 {{ $tones['hover'] }} hover:-translate-y-px hover:shadow"
                                >
                                    <span class="absolute inset-y-0 left-0 w-1 {{ $tones['bar'] }}" aria-hidden="true"></span>
                                    <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 py-2.5 pl-3.5 pr-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="min-w-0 truncate text-sm font-semibold text-slate-900 group-hover:text-indigo-800">
                                                {{ $hire->shortSubject() }}
                                            </p>
                                            <span class="inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $tones['badge'] }}">
                                                {{ $hire->statusLabel() }}
                                                @if ($hire->hasUnseenChangesForCompany())
                                                    <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        <p class="truncate text-xs text-slate-600">
                                            <span class="font-medium text-slate-800">{{ $hire->talentDisplayName() }}</span>
                                            <span class="text-slate-300"> · </span>
                                            {{ __('talenma.direct_hire.company_index_opened', ['date' => $hire->created_at?->translatedFormat('d M Y') ?? '—']) }}
                                        </p>
                                        @if ($progress)
                                            <p class="truncate text-[11px] font-medium text-indigo-700">{{ $progress }}</p>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- Colonne : clôturés --}}
            <section class="rounded-xl border border-slate-200 bg-white p-3.5 sm:p-4 min-w-0">
                <div class="mb-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('talenma.direct_hire.company_index_closed') }}
                        <span class="ml-1.5 text-xs font-semibold text-slate-500">{{ $closedRequests->count() }}</span>
                    </h3>
                </div>

                @if ($closedRequests->isEmpty())
                    <p class="rounded-lg border border-dashed border-slate-200 bg-slate-50/80 px-3 py-4 text-center text-sm text-slate-500">
                        {{ __('talenma.direct_hire.company_index_closed_empty') }}
                    </p>
                @else
                    <ul class="space-y-2">
                        @foreach ($closedRequests as $hire)
                            @php
                                $tones = $toneClasses($hire->statusTone());
                                $progress = $hire->progressLabel();
                                $closedAt = $hire->closed_at ?? $hire->updated_at;
                            @endphp
                            <li>
                                <a
                                    href="{{ route('company.direct-hire.show', $hire) }}"
                                    class="group relative flex overflow-hidden rounded-lg bg-slate-50/80 ring-1 ring-slate-200/90 transition duration-150 {{ $tones['hover'] }} hover:-translate-y-px hover:bg-white hover:shadow"
                                >
                                    <span class="absolute inset-y-0 left-0 w-1 {{ $tones['bar'] }}" aria-hidden="true"></span>
                                    <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 py-2.5 pl-3.5 pr-3">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="min-w-0 truncate text-sm font-semibold text-slate-800 group-hover:text-indigo-800">
                                                {{ $hire->shortSubject() }}
                                            </p>
                                            <span class="inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $tones['badge'] }}">
                                                {{ $hire->statusLabel() }}
                                                @if ($hire->hasUnseenChangesForCompany())
                                                    <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        <p class="truncate text-xs text-slate-600">
                                            <span class="font-medium text-slate-800">{{ $hire->talentDisplayName() }}</span>
                                        </p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ __('talenma.direct_hire.company_index_opened', ['date' => $hire->created_at?->translatedFormat('d M Y') ?? '—']) }}
                                            <span class="text-slate-300"> · </span>
                                            {{ __('talenma.direct_hire.company_index_closed_on', ['date' => $closedAt?->translatedFormat('d M Y') ?? '—']) }}
                                        </p>
                                        @if ($progress)
                                            <p class="truncate text-[11px] font-medium text-slate-600">{{ $progress }}</p>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
