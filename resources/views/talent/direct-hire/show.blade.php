@php
    $canDecide = in_array($directHire->status, [
        \App\Models\DirectHireRequest::STATUS_PENDING_RESPONSE,
        \App\Models\DirectHireRequest::STATUS_DEFERRED,
    ], true);
    $showRounds = $directHire->rounds->isNotEmpty()
        || $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.show_title') }}</h2>
                <p class="text-sm text-gray-500 truncate">{{ __('talenma.direct_hire.title_prefix') }} {{ $directHire->shortSubject() }}</p>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.with_company', ['name' => $directHire->companyDisplayName()]) }}</p>
            </div>
            <div class="inline-flex items-center gap-2 shrink-0">
                <span class="text-sm text-gray-500">{{ __('talenma.direct_hire.status_prefix') }}</span>
                @include('talent.direct-hire._status-badge', ['directHire' => $directHire])
            </div>
        </div>
        <div class="mt-2">
            <a
                href="{{ route('talent.direct-hire.index') }}"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
            >
                {{ __('talenma.direct_hire.show_back_to_list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div id="direct-hire-main-column" class="space-y-5 min-w-0">
                {{-- Proposition --}}
                <section class="bg-white rounded-2xl border overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.proposal') }}</h3>
                        <p class="text-xs text-slate-400">{{ $directHire->created_at?->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <div id="direct-hire-proposal-body" class="p-5 space-y-4">
                        <blockquote class="relative overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 px-5 py-5 sm:px-6 sm:py-6 shadow-sm ring-1 ring-slate-900/5">
                            <span class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500" aria-hidden="true"></span>
                            <p class="select-none pointer-events-none absolute top-3 right-4 text-6xl leading-none font-serif text-indigo-200/80" aria-hidden="true">„</p>
                            <p class="relative text-[0.95rem] sm:text-base text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $directHire->message }}</p>
                        </blockquote>

                        @include('talent.direct-hire._decision-note', ['directHire' => $directHire])

                        @include('talent.direct-hire._company-deferral-note', ['directHire' => $directHire])

                        @if (filled($directHire->closure_note) && $directHire->isTerminal())
                            <div class="rounded-lg bg-slate-50/80 border border-slate-100 px-3.5 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
                                <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $directHire->closure_note }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Décision --}}
                @if ($canDecide)
                    <section
                        id="direct-hire-decide"
                        class="relative bg-white rounded-2xl border overflow-hidden"
                        x-data="directHireTalentDecide({
                            url: @js(route('talent.direct-hire.decide', $directHire)),
                            deferLocked: @js($directHire->status === \App\Models\DirectHireRequest::STATUS_DEFERRED),
                            messages: @js([
                                'error' => __('talenma.direct_hire.decide_error'),
                                'networkError' => __('talenma.direct_hire.network_error'),
                                'noteMax' => __('talenma.direct_hire.chat_max'),
                                'decisionRequired' => __('talenma.direct_hire.decision_required'),
                                'chatClosedBadge' => __('talenma.direct_hire.chat_closed_badge'),
                                'chatClosed' => __('talenma.direct_hire.chat_closed'),
                                'deferConfirmTitle' => __('talenma.direct_hire.defer_confirm_title'),
                                'deferConfirmBody' => __('talenma.direct_hire.defer_confirm_body'),
                                'deferConfirmBtn' => __('talenma.direct_hire.defer_confirm_btn'),
                                'deferConfirmCancel' => __('talenma.direct_hire.defer_confirm_cancel'),
                            ]),
                        })"
                    >
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.decide_title') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('talenma.direct_hire.decide_subtitle') }}</p>
                        </div>
                        <form
                            method="POST"
                            action="{{ route('talent.direct-hire.decide', $directHire) }}"
                            class="p-5 space-y-4"
                            @submit.prevent
                        >
                            @csrf
                            <div>
                                <x-input-label for="talent_decision_note" :value="__('talenma.direct_hire.decision_note')" />
                                <textarea
                                    id="talent_decision_note"
                                    name="talent_decision_note"
                                    rows="3"
                                    maxlength="2000"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('talenma.direct_hire.decision_note_placeholder') }}"
                                    x-ref="note"
                                    x-bind:disabled="loading"
                                >{{ old('talent_decision_note') }}</textarea>
                            </div>
                            <div
                                id="direct-hire-decide-actions"
                                class="relative grid grid-cols-1 sm:grid-cols-3 gap-2"
                            >
                                <button
                                    type="button"
                                    class="inline-flex justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 disabled:opacity-60"
                                    x-bind:disabled="loading"
                                    @click="submitDecision('accept')"
                                >
                                    {{ __('talenma.direct_hire.decide_accept') }}
                                </button>
                                <button
                                    type="button"
                                    id="direct-hire-decide-defer"
                                    class="inline-flex justify-center px-4 py-2.5 text-sm font-semibold rounded-lg disabled:cursor-not-allowed"
                                    x-bind:class="deferLocked
                                        ? 'bg-amber-100 text-amber-700/70 opacity-60'
                                        : 'bg-amber-500 text-white hover:bg-amber-600 disabled:opacity-60'"
                                    x-bind:disabled="loading || deferLocked"
                                    @click="requestDefer()"
                                >
                                    {{ __('talenma.direct_hire.decide_defer') }}
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex justify-center px-4 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700 disabled:opacity-60"
                                    x-bind:disabled="loading"
                                    @click="submitDecision('decline')"
                                >
                                    {{ __('talenma.direct_hire.decide_decline') }}
                                </button>
                            </div>
                        </form>

                        <div
                            x-show="confirmingDefer"
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            role="dialog"
                            aria-modal="true"
                            x-on:keydown.escape.window="closeDeferConfirm"
                        >
                            <div class="absolute inset-0 bg-slate-900/40" x-on:click="closeDeferConfirm" aria-hidden="true"></div>
                            <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200">
                                <p class="text-base font-semibold text-slate-900" x-text="messages.deferConfirmTitle"></p>
                                <p class="mt-2 text-sm text-slate-600" x-text="messages.deferConfirmBody"></p>
                                <div class="mt-5 flex flex-wrap justify-end gap-3">
                                    <x-secondary-button type="button" x-on:click="closeDeferConfirm" x-bind:disabled="loading">
                                        <span x-text="messages.deferConfirmCancel"></span>
                                    </x-secondary-button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-60"
                                        x-on:click="confirmDefer"
                                        x-bind:disabled="loading"
                                        x-text="messages.deferConfirmBtn"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Étapes --}}
                @if ($showRounds)
                    @include('talent.direct-hire._rounds', ['directHire' => $directHire])
                @endif
            </div>

            <aside class="min-w-0 w-full lg:self-stretch">
                @include('direct-hire._chat', ['directHire' => $directHire, 'sidebar' => true])
            </aside>
        </div>
    </div>
</x-app-layout>
