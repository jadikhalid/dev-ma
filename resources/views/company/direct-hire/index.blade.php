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

    $canCompose = (bool) ($canCompose ?? false);
    $composeDisabledReason = $composeDisabledReason ?? null;
    $storeUrlTemplate = str_replace(
        '999999001',
        '__TALENT_ID__',
        route('company.direct-hire.store', ['talent' => 999999001]),
    );
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.company_index_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.company_index_subtitle') }}</p>
            </div>
            <button
                type="button"
                @if ($canCompose)
                    onclick="window.dispatchEvent(new CustomEvent('direct-hire-open-compose'))"
                @endif
                @disabled(! $canCompose)
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-indigo-200 bg-white text-indigo-700 shadow-sm transition hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:border-indigo-200"
                aria-label="{{ __('talenma.direct_hire.compose_open_aria') }}"
                title="{{ $canCompose ? __('talenma.direct_hire.compose_open_aria') : ($composeDisabledReason ?? __('talenma.direct_hire.compose_open_aria')) }}"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
        </div>
    </x-slot>

    <x-process-help topic="direct_hire" />

    <div
        class="py-5 sm:py-6 max-w-6xl mx-auto px-4 sm:px-6"
        x-data="directHireIndexWorkspace({
            canCompose: @js($canCompose),
            talentSearchUrl: @js($canCompose ? route('company.direct-hire.talent-suggestions') : null),
            storeUrlTemplate: @js($canCompose ? $storeUrlTemplate : ''),
            csrf: @js(csrf_token()),
            labels: @js([
                'error' => __('talenma.direct_hire.create_error'),
                'networkError' => __('talenma.direct_hire.network_error'),
                'sent' => __('talenma.direct_hire.sent'),
                'sending' => __('talenma.direct_hire.compose_sending'),
                'composeTalentRequired' => __('talenma.direct_hire.compose_talent_required'),
                'composeSubjectRequired' => __('talenma.direct_hire.subject_required'),
                'composeSubjectMin' => __('talenma.direct_hire.subject_min'),
                'composeMessageRequired' => __('talenma.direct_hire.message_required'),
                'composeMessageMin' => __('talenma.direct_hire.message_min'),
                'talentSearchLoading' => __('talenma.direct_hire.compose_talent_search_loading'),
                'talentSearchEmpty' => __('talenma.direct_hire.compose_talent_search_empty'),
                'deleted' => __('talenma.direct_hire.deleted_closed'),
                'deleteBadge' => __('talenma.direct_hire.delete_closed_badge'),
                'deleteTitle' => __('talenma.direct_hire.delete_closed_title'),
                'deleteBody' => __('talenma.direct_hire.delete_closed_body'),
                'deleteConfirm' => __('talenma.direct_hire.delete_closed_confirm'),
                'deleteCancel' => __('talenma.direct_hire.delete_closed_cancel'),
            ]),
        })"
        @direct-hire-open-compose.window="openCompose()"
        @keydown.escape.window="onEscape()"
    >
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
                    {{ __('talenma.direct_hire.company_index_closed_empty') }}
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
                                            href="{{ route('company.direct-hire.show', $hire) }}"
                                            class="min-w-0 truncate text-sm font-semibold text-slate-800 group-hover:text-indigo-800"
                                        >
                                            {{ $hire->shortSubject() }}
                                        </a>
                                        <div class="inline-flex shrink-0 items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $tones['badge'] }}">
                                                {{ $hire->statusLabel() }}
                                                @if ($hire->hasUnseenChangesForCompany())
                                                    <span class="relative flex h-1.5 w-1.5" title="{{ __('talenma.direct_hire.nav_new') }}">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                                    </span>
                                                @endif
                                            </span>
                                            <button
                                                type="button"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                                @click.stop="openDeleteConfirm(@js(route('company.direct-hire.destroy', $hire)), @js($cardId))"
                                                aria-label="{{ __('talenma.direct_hire.delete_closed_aria') }}"
                                                title="{{ __('talenma.direct_hire.delete_closed_aria') }}"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <a href="{{ route('company.direct-hire.show', $hire) }}" class="block space-y-1">
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
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Bottom sheet: proposer une candidature --}}
        <div
            x-show="composeOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="direct-hire-compose-title"
            data-direct-hire-compose
        >
            <div
                x-show="composeOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeCompose()"
            ></div>

            <div
                x-show="composeOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,42rem)] sm:max-w-lg sm:rounded-2xl"
                @click.stop
            >
                <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="min-w-0">
                        <h3 id="direct-hire-compose-title" class="text-lg font-bold text-gray-900">{{ __('talenma.direct_hire.compose_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.direct_hire.compose_sheet_desc') }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeCompose()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="direct-hire-compose-sheet" class="relative min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <form
                        class="space-y-3"
                        @submit.prevent="sendCompose()"
                        novalidate
                    >
                        <div class="relative" @click.outside="closeTalentSuggestions()">
                            <label class="block text-xs font-medium text-gray-600" for="direct-hire-compose-talent">{{ __('talenma.direct_hire.compose_talent') }}</label>
                            <input
                                id="direct-hire-compose-talent"
                                type="search"
                                x-model="talentQuery"
                                x-ref="talentInput"
                                @input="onTalentInput()"
                                @keydown="onTalentKeydown($event)"
                                @focus="onTalentFocus()"
                                maxlength="100"
                                autocomplete="off"
                                role="combobox"
                                aria-controls="direct-hire-talent-listbox"
                                :aria-expanded="talentSuggestionsOpen"
                                aria-autocomplete="list"
                                :disabled="composeSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.direct_hire.compose_talent_placeholder') }}"
                            >
                            <input type="hidden" name="talent_id" :value="selectedTalentId ?? ''">

                            <div
                                x-show="talentSuggestionsOpen"
                                x-cloak
                                id="direct-hire-talent-listbox"
                                role="listbox"
                                class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
                            >
                                <template x-if="talentLoading">
                                    <p class="px-3 py-2 text-xs text-slate-500" x-text="labels.talentSearchLoading"></p>
                                </template>
                                <template x-if="! talentLoading && talentResults.length === 0">
                                    <p class="px-3 py-2 text-xs text-slate-500" x-text="labels.talentSearchEmpty"></p>
                                </template>
                                <template x-for="(item, index) in talentResults" :key="item.id">
                                    <button
                                        type="button"
                                        role="option"
                                        class="flex w-full flex-col items-start gap-0.5 px-3 py-2 text-left text-sm transition"
                                        :class="index === talentActiveIndex ? 'bg-indigo-50 text-indigo-900' : 'text-slate-800 hover:bg-slate-50'"
                                        @mousedown.prevent="selectTalent(item)"
                                        @mouseenter="talentActiveIndex = index"
                                    >
                                        <span class="font-medium" x-text="item.label"></span>
                                        <span class="text-xs text-slate-500" x-show="item.subtitle" x-text="item.subtitle"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600" for="direct-hire-compose-subject">{{ __('talenma.direct_hire.subject') }}</label>
                            <input
                                id="direct-hire-compose-subject"
                                name="subject"
                                type="text"
                                x-model="composeSubject"
                                maxlength="120"
                                :disabled="composeSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.direct_hire.subject_placeholder') }}"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600" for="direct-hire-compose-message">{{ __('talenma.direct_hire.message') }}</label>
                            <textarea
                                id="direct-hire-compose-message"
                                name="message"
                                x-model="composeMessage"
                                rows="5"
                                maxlength="5000"
                                :disabled="composeSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.direct_hire.message_placeholder') }}"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.message_hint') }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-60"
                                :disabled="composeSending"
                                x-text="composeSending ? labels.sending : @js(__('talenma.direct_hire.send'))"
                            ></button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60"
                                @click="closeCompose()"
                                :disabled="composeSending"
                            >{{ __('talenma.direct_hire.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div
                x-show="deleteConfirming"
                x-cloak
                class="fixed inset-0 z-[80] flex items-center justify-center p-4"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="direct-hire-delete-title"
            >
                <div class="absolute inset-0 bg-slate-900/50" @click="closeDeleteConfirm()" aria-hidden="true"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl ring-2 ring-rose-200">
                    <p class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-rose-700 ring-1 ring-rose-100" x-text="labels.deleteBadge"></p>
                    <p id="direct-hire-delete-title" class="mt-3 text-base font-semibold text-slate-900" x-text="labels.deleteTitle"></p>
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
