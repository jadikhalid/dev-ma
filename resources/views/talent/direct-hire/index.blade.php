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
            <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.talent_index_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.talent_index_subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="direct_hire" />

    <div
        class="py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6"
        x-data="directHireIndexWorkspace({
            canCompose: false,
            csrf: @js(csrf_token()),
            labels: @js([
                'error' => __('talenma.direct_hire.create_error'),
                'networkError' => __('talenma.direct_hire.network_error'),
                'sending' => __('talenma.direct_hire.compose_sending'),
                'deleted' => __('talenma.direct_hire.deleted_closed'),
                'deleteBadge' => __('talenma.direct_hire.delete_closed_badge'),
                'deleteTitle' => __('talenma.direct_hire.delete_closed_title'),
                'deleteBody' => __('talenma.direct_hire.delete_closed_body_talent'),
                'deleteConfirm' => __('talenma.direct_hire.delete_closed_confirm'),
                'deleteCancel' => __('talenma.direct_hire.delete_closed_cancel'),
            ]),
        })"
        @keydown.escape.window="onEscape()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 lg:items-start">
            {{-- Colonne : en cours --}}
            <section class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/70 via-white to-white p-3.5 sm:p-4 min-w-0">
                <div class="mb-3 flex items-baseline justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ __('talenma.direct_hire.talent_index_open') }}
                        <span class="ml-1.5 text-xs font-semibold text-indigo-600">{{ $openRequests->count() }}</span>
                    </h3>
                </div>

                @if ($openRequests->isEmpty())
                    <p class="rounded-lg border border-dashed border-slate-200 bg-white/80 px-3 py-4 text-center text-sm text-slate-500">
                        {{ __('talenma.direct_hire.talent_index_open_empty') }}
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
                                    href="{{ route('talent.direct-hire.show', $hire) }}"
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
                                                @if ($hire->hasUnseenChangesForTalent())
                                                    <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                        <p class="truncate text-xs text-slate-600">
                                            <span class="font-medium text-slate-800">{{ $hire->talentFacingCompanyName() }}</span>
                                            <span class="text-slate-300"> · </span>
                                            {{ __('talenma.direct_hire.talent_index_opened', ['date' => $hire->created_at?->translatedFormat('d M Y') ?? '—']) }}
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
                        {{ __('talenma.direct_hire.talent_index_closed') }}
                        <span id="direct-hire-closed-count" class="ml-1.5 text-xs font-semibold text-slate-500">{{ $closedRequests->count() }}</span>
                    </h3>
                </div>

                <p
                    id="direct-hire-closed-empty"
                    @class([
                        'rounded-lg border border-dashed border-slate-200 bg-slate-50/80 px-3 py-4 text-center text-sm text-slate-500',
                        'hidden' => $closedRequests->isNotEmpty(),
                    ])
                >
                    {{ __('talenma.direct_hire.talent_index_closed_empty') }}
                </p>

                <ul
                    id="direct-hire-closed-list"
                    @class([
                        'space-y-2',
                        'hidden' => $closedRequests->isEmpty(),
                    ])
                >
                    @foreach ($closedRequests as $hire)
                        @php
                            $tones = $toneClasses($hire->statusTone());
                            $progress = $hire->progressLabel();
                            $closedAt = $hire->closed_at ?? $hire->updated_at;
                            $cardId = 'direct-hire-closed-'.$hire->id;
                        @endphp
                        <li id="{{ $cardId }}">
                            <div class="group relative overflow-hidden rounded-lg bg-slate-50/80 ring-1 ring-slate-200/90 transition duration-150 {{ $tones['hover'] }} hover:-translate-y-px hover:bg-white hover:shadow">
                                <span class="absolute inset-y-0 left-0 w-1 {{ $tones['bar'] }}" aria-hidden="true"></span>
                                <div class="flex flex-col justify-center gap-1 py-2.5 pl-3.5 pr-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <a
                                            href="{{ route('talent.direct-hire.show', $hire) }}"
                                            class="min-w-0 truncate text-sm font-semibold text-slate-800 group-hover:text-indigo-800"
                                        >
                                            {{ $hire->shortSubject() }}
                                        </a>
                                        <div class="inline-flex shrink-0 items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $tones['badge'] }}">
                                                {{ $hire->statusLabel() }}
                                                @if ($hire->hasUnseenChangesForTalent())
                                                    <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                            </span>
                                            <button
                                                type="button"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                                @click.stop="openDeleteConfirm(@js(route('talent.direct-hire.destroy', $hire)), @js($cardId))"
                                                aria-label="{{ __('talenma.direct_hire.delete_closed_aria') }}"
                                                title="{{ __('talenma.direct_hire.delete_closed_aria') }}"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <a href="{{ route('talent.direct-hire.show', $hire) }}" class="block space-y-1">
                                        <p class="truncate text-xs text-slate-600">
                                            <span class="font-medium text-slate-800">{{ $hire->talentFacingCompanyName() }}</span>
                                        </p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ __('talenma.direct_hire.talent_index_opened', ['date' => $hire->created_at?->translatedFormat('d M Y') ?? '—']) }}
                                            <span class="text-slate-300"> · </span>
                                            {{ __('talenma.direct_hire.talent_index_closed_on', ['date' => $closedAt?->translatedFormat('d M Y') ?? '—']) }}
                                        </p>
                                        @if ($progress)
                                            <p class="truncate text-[11px] font-medium text-slate-600">{{ $progress }}</p>
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        <template x-teleport="body">
            <div
                x-show="deleteConfirming"
                x-cloak
                class="fixed inset-0 z-[80] flex items-center justify-center p-4"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="direct-hire-delete-title-talent"
            >
                <div class="absolute inset-0 bg-slate-900/50" @click="closeDeleteConfirm()" aria-hidden="true"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl ring-2 ring-rose-200">
                    <p class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-rose-700 ring-1 ring-rose-100" x-text="labels.deleteBadge"></p>
                    <p id="direct-hire-delete-title-talent" class="mt-3 text-base font-semibold text-slate-900" x-text="labels.deleteTitle"></p>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="labels.deleteBody"></p>
                    <div class="mt-5 flex flex-wrap justify-end gap-3">
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-60"
                            @click="closeDeleteConfirm()"
                            :disabled="deleteSending"
                            x-text="labels.deleteCancel"
                        ></button>
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-rose-700 disabled:opacity-60"
                            @click="confirmDelete()"
                            :disabled="deleteSending"
                            x-text="deleteSending ? labels.sending : labels.deleteConfirm"
                        ></button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
