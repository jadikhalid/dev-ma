@php
    $canComposeNamed = (bool) ($canComposeNamed ?? false);
    $composeNamedDisabledReason = $composeNamedDisabledReason ?? null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.index_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.recruitment.index_subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="sourcing" />

    <div
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="sourcingIndex({
            canComposeNamed: @js($canComposeNamed),
            talentSearchUrl: @js($canComposeNamed ? route('sourcing.talent-suggestions') : null),
            storeUrl: @js($canComposeNamed ? route('recruitment.store') : null),
            csrf: @js(csrf_token()),
            labels: @js([
                'error' => __('talenma.recruitment.error'),
                'networkError' => __('talenma.recruitment.network_error'),
                'sent' => __('talenma.recruitment.sent_dashboard_named'),
                'sending' => __('talenma.recruitment.compose_named_sending'),
                'composeTalentRequired' => __('talenma.recruitment.compose_named_talent_required'),
                'roleTitleRequired' => __('talenma.recruitment.role_title_required'),
                'roleTitleMin' => __('talenma.recruitment.role_title_min'),
                'needRequired' => __('talenma.recruitment.need_named_required'),
                'needMin' => __('talenma.recruitment.need_min'),
                'roleTitleNamedDefault' => __('talenma.recruitment.role_title_named_default', ['name' => ':name']),
                'talentSearchLoading' => __('talenma.recruitment.compose_named_talent_search_loading'),
                'talentSearchEmpty' => __('talenma.recruitment.compose_named_talent_search_empty'),
            ]),
        })"
        @keydown.escape.window="closeAnySheet()"
        @sourcing-open-created.window="closeCreate()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 items-start">
            <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.column_open') }}</p>
                            <p class="text-xs text-slate-500">{{ __('talenma.recruitment.column_open_hint') }}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="openCreate()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-indigo-200 bg-white text-indigo-700 shadow-sm transition hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        aria-label="{{ __('talenma.recruitment.create_open_aria') }}"
                        title="{{ __('talenma.recruitment.create_open_aria') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>

                <div
                    id="sourcing-open-empty"
                    @class([
                        'mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center',
                        'hidden' => $openRequests->isNotEmpty(),
                    ])
                >
                    <p class="text-sm text-slate-500">{{ __('talenma.recruitment.column_empty') }}</p>
                </div>

                <ul
                    id="sourcing-open-list"
                    @class([
                        'mt-4 space-y-2.5',
                        'hidden' => $openRequests->isEmpty(),
                    ])
                >
                    @foreach ($openRequests as $req)
                        <li>@include('sourcing._request-card', ['recruitment' => $req])</li>
                    @endforeach
                </ul>
            </section>

            <section class="rounded-2xl border border-violet-100 bg-gradient-to-br from-violet-50/80 via-white to-slate-50 p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-white shadow-sm shadow-violet-600/20" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.column_named') }}</p>
                            <p class="text-xs text-slate-500">{{ __('talenma.recruitment.column_named_hint') }}</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @if ($canComposeNamed)
                            @click="openNamedCreate()"
                        @endif
                        @disabled(! $canComposeNamed)
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-violet-200 bg-white text-violet-700 shadow-sm transition hover:bg-violet-50 hover:border-violet-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-white disabled:hover:border-violet-200"
                        aria-label="{{ __('talenma.recruitment.create_named_aria') }}"
                        title="{{ $canComposeNamed ? __('talenma.recruitment.create_named_aria') : ($composeNamedDisabledReason ?? __('talenma.recruitment.create_named_aria')) }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>

                <div
                    id="sourcing-named-empty"
                    @class([
                        'mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center',
                        'hidden' => $namedRequests->isNotEmpty(),
                    ])
                >
                    <p class="text-sm text-slate-500">{{ __('talenma.recruitment.column_empty') }}</p>
                </div>

                <ul
                    id="sourcing-named-list"
                    @class([
                        'mt-4 space-y-2.5',
                        'hidden' => $namedRequests->isEmpty(),
                    ])
                >
                    @foreach ($namedRequests as $req)
                        <li>@include('sourcing._request-card', ['recruitment' => $req])</li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Bottom sheet: open sourcing request --}}
        <div
            x-show="createOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sourcing-create-title"
        >
            <div
                x-show="createOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeCreate()"
            ></div>

            <div
                x-show="createOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,40rem)] sm:max-w-lg sm:rounded-2xl"
                @click.stop
            >
                <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.recruitment.column_open') }}</p>
                        <h3 id="sourcing-create-title" class="mt-1 text-lg font-bold text-gray-900">{{ __('talenma.recruitment.title_open') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.recruitment.subtitle_open') }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeCreate()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <div id="sourcing-drawer-request-card" class="relative">
                        @include('recruitment._request-form', [
                            'talent' => null,
                            'embed' => true,
                            'formId' => 'sourcing-drawer-request-form',
                            'loadingTarget' => 'sourcing-drawer-request-card',
                        ])
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom sheet: named intermediation --}}
        <div
            x-show="namedCreateOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sourcing-named-create-title"
        >
            <div
                x-show="namedCreateOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeNamedCreate()"
            ></div>

            <div
                x-show="namedCreateOpen"
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">{{ __('talenma.recruitment.column_named') }}</p>
                        <h3 id="sourcing-named-create-title" class="mt-1 text-lg font-bold text-gray-900">{{ __('talenma.recruitment.compose_named_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.recruitment.compose_named_sheet_desc') }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeNamedCreate()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="sourcing-named-compose-sheet" class="relative min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <form class="space-y-3" @submit.prevent="sendNamedCompose()" novalidate>
                        <div class="relative" @click.outside="closeTalentSuggestions()">
                            <label class="block text-xs font-medium text-gray-600" for="sourcing-named-compose-talent">{{ __('talenma.recruitment.compose_named_talent') }}</label>
                            <input
                                id="sourcing-named-compose-talent"
                                type="search"
                                x-model="talentQuery"
                                x-ref="namedTalentInput"
                                @input="onTalentInput()"
                                @keydown="onTalentKeydown($event)"
                                @focus="onTalentFocus()"
                                maxlength="100"
                                autocomplete="off"
                                role="combobox"
                                aria-controls="sourcing-named-talent-listbox"
                                :aria-expanded="talentSuggestionsOpen"
                                aria-autocomplete="list"
                                :disabled="namedSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.recruitment.compose_named_talent_placeholder') }}"
                            >
                            <input type="hidden" name="developer_user_id" :value="selectedTalentId ?? ''">

                            <div
                                x-show="talentSuggestionsOpen"
                                x-cloak
                                id="sourcing-named-talent-listbox"
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
                                        :class="index === talentActiveIndex ? 'bg-violet-50 text-violet-900' : 'text-slate-800 hover:bg-slate-50'"
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
                            <label class="block text-xs font-medium text-gray-600" for="sourcing-named-compose-role">{{ __('talenma.recruitment.role_title_named') }}</label>
                            <input
                                id="sourcing-named-compose-role"
                                name="role_title"
                                type="text"
                                x-model="namedRoleTitle"
                                maxlength="120"
                                :disabled="namedSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.recruitment.role_title_named_placeholder') }}"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600" for="sourcing-named-compose-need">{{ __('talenma.recruitment.need_named') }}</label>
                            <textarea
                                id="sourcing-named-compose-need"
                                name="need"
                                x-model="namedNeed"
                                rows="5"
                                maxlength="5000"
                                :disabled="namedSending"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-violet-500 focus:ring-violet-500 disabled:opacity-60"
                                placeholder="{{ __('talenma.recruitment.need_named_placeholder') }}"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">{{ __('talenma.recruitment.need_hint') }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-500 disabled:opacity-60"
                                :disabled="namedSending"
                                x-text="namedSending ? labels.sending : @js(__('talenma.recruitment.send_named'))"
                            ></button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60"
                                @click="closeNamedCreate()"
                                :disabled="namedSending"
                            >{{ __('talenma.recruitment.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
