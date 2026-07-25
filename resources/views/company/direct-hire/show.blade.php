@php
    $tone = match ($directHire->statusTone()) {
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
    $talent = $directHire->talent;
    $canManageRounds = $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS;
    $canWithdraw = $directHire->isOpen();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div
            x-data="companyTalentProfileDrawer({
                composeUrl: @js(route('inbox.store')),
                csrf: @js(csrf_token()),
                labels: @js([
                    'profileError' => __('talenma.home.search_drawer_error'),
                    'error' => __('talenma.home.search_drawer_error'),
                    'composeError' => __('talenma.inbox.error'),
                    'composeMinBody' => __('talenma.inbox.compose_min_body'),
                    'directHireDisabled' => __('talenma.direct_hire.cta_disabled_hint'),
                ]),
            })"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.show_title') }}</h2>
                    <p class="text-sm text-gray-500 truncate">{{ __('talenma.direct_hire.title_prefix') }} {{ $directHire->shortSubject() }}</p>
                    <p class="text-sm text-gray-500">
                        {{ __('talenma.direct_hire.with_talent', ['name' => $talent?->name]) }}
                        @if ($talent)
                            —
                            <button
                                type="button"
                                class="text-indigo-600 hover:text-indigo-800 underline underline-offset-2"
                                @click="openProfile(@js(route('company.talent.show', $talent)))"
                            >{{ __('talenma.direct_hire.view_talent') }}</button>
                        @endif
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 shrink-0">
                    <span class="text-sm text-gray-500">{{ __('talenma.direct_hire.status_prefix') }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $tone }}">
                        {{ $directHire->statusLabel() }}
                    </span>
                </div>
            </div>

            @include('company._talent-profile-drawer', ['hideHireActions' => true])
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div class="space-y-5 min-w-0">
                <div class="bg-white rounded-2xl border p-6 space-y-5">
                    <div>
                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.proposal') }}</h3>
                            <p class="text-xs text-slate-400">{{ $directHire->created_at?->translatedFormat('d M Y H:i') }}</p>
                        </div>

                        <blockquote class="relative mt-3 overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 px-5 py-5 sm:px-6 sm:py-6 shadow-sm ring-1 ring-slate-900/5">
                            <span class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500" aria-hidden="true"></span>
                            <p class="select-none pointer-events-none absolute top-3 right-4 text-6xl leading-none font-serif text-indigo-200/80" aria-hidden="true">„</p>
                            <p class="relative text-[0.95rem] sm:text-base text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $directHire->message }}</p>
                        </blockquote>
                    </div>

                    @if ($directHire->talent_decision_at)
                        <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-3.5 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.talent_decision') }}</p>
                            <p class="mt-1 text-sm text-slate-900">
                                @php
                                    $decisionLabel = match ($directHire->status) {
                                        \App\Models\DirectHireRequest::STATUS_IN_PROCESS => __('talenma.direct_hire.decision_label_accepted'),
                                        \App\Models\DirectHireRequest::STATUS_DECLINED => __('talenma.direct_hire.decision_label_declined'),
                                        \App\Models\DirectHireRequest::STATUS_DEFERRED => __('talenma.direct_hire.decision_label_deferred'),
                                        default => $directHire->statusLabel(),
                                    };
                                @endphp
                                {{ $decisionLabel }} — {{ $directHire->talent_decision_at->translatedFormat('d M Y H:i') }}
                            </p>
                            @if (filled($directHire->talent_decision_note))
                                <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                                    <span class="font-medium text-slate-900">{{ __('talenma.direct_hire.decision_comment_label') }} :</span>
                                    {{ $directHire->talent_decision_note }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if (filled($directHire->closure_note) && $directHire->isTerminal())
                        <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-3.5 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
                            <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $directHire->closure_note }}</p>
                        </div>
                    @endif
                </div>

                @if ($canManageRounds || $directHire->rounds->isNotEmpty())
                    <div id="direct-hire-rounds-block" class="bg-white rounded-2xl border p-6 space-y-4 relative">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.direct_hire.rounds_title') }}</h3>

                        <div id="direct-hire-rounds-list" class="space-y-4">
                            @forelse ($directHire->rounds as $round)
                                @include('company.direct-hire._round-card', [
                                    'directHire' => $directHire,
                                    'round' => $round,
                                    'canManageRounds' => $canManageRounds,
                                    'roundStatuses' => $roundStatuses,
                                ])
                            @empty
                                <p id="direct-hire-rounds-empty" class="text-sm text-gray-500">{{ __('talenma.direct_hire.rounds_empty') }}</p>
                            @endforelse
                        </div>

                        @if ($canManageRounds)
                            <form
                                method="POST"
                                action="{{ route('company.direct-hire.rounds.store', $directHire) }}"
                                class="relative mt-2 rounded-lg border border-dashed border-slate-200 bg-slate-50/40 px-3.5 py-3 space-y-2.5"
                                x-data="directHireRoundCreate({
                                    storeUrl: @js(route('company.direct-hire.rounds.store', $directHire)),
                                    messages: @js([
                                        'titleRequired' => __('talenma.direct_hire.round_title_required'),
                                        'titleMin' => __('talenma.direct_hire.round_title_min'),
                                        'titleMax' => __('talenma.direct_hire.round_title_max'),
                                        'scheduledRequired' => __('talenma.direct_hire.round_scheduled_required'),
                                        'meetingUrlInvalid' => __('talenma.direct_hire.round_meeting_url_invalid'),
                                        'meetingUrlMax' => __('talenma.direct_hire.round_meeting_url_max'),
                                        'noteMax' => __('talenma.direct_hire.round_note_max'),
                                        'success' => __('talenma.direct_hire.round_added'),
                                        'error' => __('talenma.direct_hire.round_create_error'),
                                        'networkError' => __('talenma.direct_hire.network_error'),
                                    ]),
                                })"
                                @submit.prevent="submit"
                            >
                                @csrf
                                <div
                                    x-show="loading"
                                    x-cloak
                                    class="absolute inset-0 z-20 flex items-center justify-center rounded-lg bg-white/70 backdrop-blur-[1px]"
                                    aria-hidden="true"
                                >
                                    <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-5 py-4 shadow-sm ring-1 ring-gray-200">
                                        <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.round_add') }}</p>
                                <div>
                                    <x-input-label for="new-round-title" :value="__('talenma.direct_hire.round_title')" />
                                    <x-text-input
                                        id="new-round-title"
                                        name="title"
                                        class="mt-1 block w-full"
                                        maxlength="120"
                                        placeholder="{{ __('talenma.direct_hire.round_title_placeholder') }}"
                                        x-model="title"
                                        x-on:input="clearError('title')"
                                        x-bind:aria-invalid="Boolean(errors.title)"
                                        x-bind:disabled="loading"
                                    />
                                    <p x-show="errors.title" x-cloak class="mt-2 text-sm text-red-600" x-text="errors.title"></p>
                                </div>
                                <div>
                                    <x-input-label for="new-round-scheduled" :value="__('talenma.direct_hire.round_scheduled_at')" />
                                    <input
                                        id="new-round-scheduled"
                                        type="datetime-local"
                                        name="scheduled_at"
                                        class="mt-1 block w-full border-gray-300 rounded-lg text-sm"
                                        x-model="scheduledAt"
                                        x-on:input="clearError('scheduled_at')"
                                        x-bind:aria-invalid="Boolean(errors.scheduled_at)"
                                        x-bind:disabled="loading"
                                    >
                                    <p x-show="errors.scheduled_at" x-cloak class="mt-2 text-sm text-red-600" x-text="errors.scheduled_at"></p>
                                </div>
                                <div>
                                    <x-input-label for="new-round-meeting-url" :value="__('talenma.direct_hire.round_meeting_url')" />
                                    <x-text-input
                                        id="new-round-meeting-url"
                                        name="meeting_url"
                                        type="url"
                                        class="mt-1 block w-full"
                                        maxlength="2048"
                                        placeholder="{{ __('talenma.direct_hire.round_meeting_url_placeholder') }}"
                                        x-model="meetingUrl"
                                        x-on:input="clearError('meeting_url')"
                                        x-bind:aria-invalid="Boolean(errors.meeting_url)"
                                        x-bind:disabled="loading"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.round_meeting_url_hint') }}</p>
                                    <p x-show="errors.meeting_url" x-cloak class="mt-2 text-sm text-red-600" x-text="errors.meeting_url"></p>
                                </div>
                                <div>
                                    <x-input-label for="new-round-note" :value="__('talenma.direct_hire.round_note')" />
                                    <textarea
                                        id="new-round-note"
                                        name="company_note"
                                        rows="2"
                                        maxlength="2000"
                                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                        x-model="companyNote"
                                        x-on:input="clearError('company_note')"
                                        x-bind:aria-invalid="Boolean(errors.company_note)"
                                        x-bind:disabled="loading"
                                    ></textarea>
                                    <p x-show="errors.company_note" x-cloak class="mt-2 text-sm text-red-600" x-text="errors.company_note"></p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <x-secondary-button type="button" x-on:click="cancel" x-bind:disabled="loading">
                                        {{ __('talenma.direct_hire.cancel') }}
                                    </x-secondary-button>
                                    <x-primary-button type="submit" x-bind:disabled="loading">
                                        {{ __('talenma.direct_hire.round_add_btn') }}
                                    </x-primary-button>
                                </div>
                            </form>

                            <div class="grid sm:grid-cols-2 gap-4 pt-2">
                                <form method="POST" action="{{ route('company.direct-hire.close', $directHire) }}" class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 space-y-3">
                                    @csrf
                                    <input type="hidden" name="outcome" value="hired">
                                    <p class="text-sm font-semibold text-emerald-900">{{ __('talenma.direct_hire.close_hired') }}</p>
                                    <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                                    <button type="submit" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">{{ __('talenma.direct_hire.close_hired_btn') }}</button>
                                </form>
                                <form method="POST" action="{{ route('company.direct-hire.close', $directHire) }}" class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 space-y-3">
                                    @csrf
                                    <input type="hidden" name="outcome" value="closed_negative">
                                    <p class="text-sm font-semibold text-rose-900">{{ __('talenma.direct_hire.close_negative') }}</p>
                                    <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                                    <button type="submit" class="inline-flex px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700">{{ __('talenma.direct_hire.close_negative_btn') }}</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($canWithdraw)
                    <div class="bg-white rounded-2xl border p-6">
                        <form method="POST" action="{{ route('company.direct-hire.withdraw', $directHire) }}" class="space-y-3" onsubmit="return confirm(@js(__('talenma.direct_hire.withdraw_confirm')))">
                            @csrf
                            <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.withdraw_title') }}</p>
                            <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                            <button type="submit" class="inline-flex px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">{{ __('talenma.direct_hire.withdraw_btn') }}</button>
                        </form>
                    </div>
                @endif
            </div>

            <aside class="min-w-0 w-full self-start">
                @include('direct-hire._chat', ['directHire' => $directHire, 'sidebar' => true])
            </aside>
        </div>
    </div>
</x-app-layout>
