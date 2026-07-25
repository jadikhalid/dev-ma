@php
    $roundTone = match ($round->statusTone()) {
        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
    $accentBar = match ($round->statusTone()) {
        'sky' => 'bg-sky-500',
        'emerald' => 'bg-emerald-500',
        'rose' => 'bg-rose-500',
        'amber' => 'bg-amber-500',
        default => 'bg-slate-400',
    };
    $canManageRounds = $canManageRounds ?? false;
    $roundStatuses = $roundStatuses ?? \App\Models\DirectHireRound::outcomeStatuses();
    $isCancelled = $round->isCancelled();
    $canManage = $canManageRounds && ! $isCancelled;
    $canCancel = $canManage && $round->isCancellable();
    $scheduledLocal = $round->scheduled_at?->timezone(config('app.timezone'))->format('Y-m-d\TH:i') ?? '';
@endphp

<div
    @class([
        'relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5',
        'opacity-80' => $isCancelled,
    ])
    data-round-id="{{ $round->id }}"
    @if ($canManage)
        x-data="directHireRoundManage({
            updateUrl: @js(route('company.direct-hire.rounds.update', [$directHire, $round])),
            cancelUrl: @js($canCancel ? route('company.direct-hire.rounds.cancel', [$directHire, $round]) : null),
            canCancel: @js($canCancel),
            initial: @js([
                'title' => $round->title,
                'scheduledAt' => $scheduledLocal,
                'meetingUrl' => $round->meeting_url ?? '',
                'companyNote' => $round->company_note ?? '',
                'status' => $round->status,
            ]),
            messages: @js([
                'titleRequired' => __('talenma.direct_hire.round_title_required'),
                'titleMin' => __('talenma.direct_hire.round_title_min'),
                'titleMax' => __('talenma.direct_hire.round_title_max'),
                'scheduledRequired' => __('talenma.direct_hire.round_scheduled_required'),
                'meetingUrlInvalid' => __('talenma.direct_hire.round_meeting_url_invalid'),
                'meetingUrlMax' => __('talenma.direct_hire.round_meeting_url_max'),
                'noteMax' => __('talenma.direct_hire.round_note_max'),
                'reasonRequired' => __('talenma.direct_hire.round_cancel_reason_required'),
                'reasonMin' => __('talenma.direct_hire.round_cancel_reason_min'),
                'reasonMax' => __('talenma.direct_hire.round_cancel_reason_max'),
                'updated' => __('talenma.direct_hire.round_updated'),
                'cancelled' => __('talenma.direct_hire.round_cancelled'),
                'error' => __('talenma.direct_hire.round_update_error'),
                'cancelError' => __('talenma.direct_hire.round_cancel_error'),
                'networkError' => __('talenma.direct_hire.network_error'),
            ]),
        })"
    @endif
>
    <span class="absolute inset-y-0 left-0 w-1 {{ $accentBar }}" aria-hidden="true"></span>

    @if ($canManage)
        <div
            x-show="loading"
            x-cloak
            class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-white/70 backdrop-blur-[1px]"
            aria-hidden="true"
        >
            <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-5 py-4 shadow-sm ring-1 ring-gray-200">
                <svg class="h-7 w-7 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    @endif

    <div class="pl-4 pr-4 py-4 sm:pl-5 sm:pr-5">
    <div @if ($canManage) x-show="!editing" @endif>
        <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
            <div class="flex flex-wrap items-center gap-2.5 min-w-0">
                <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-md bg-slate-900 px-2 text-xs font-bold text-white">
                    {{ $round->position }}
                </span>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('talenma.direct_hire.round_n', ['n' => $round->position]) }}
                    </p>
                    <p @class([
                        'text-base font-semibold leading-snug',
                        'text-gray-500 line-through' => $isCancelled,
                        'text-slate-900' => ! $isCancelled,
                    ])>{{ $round->title }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $roundTone }}">{{ $round->statusLabel() }}</span>
            </div>
            @if ($canManage)
                <button
                    type="button"
                    class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                    x-on:click="openEdit"
                    x-bind:disabled="loading"
                >{{ __('talenma.direct_hire.round_edit') }}</button>
            @endif
        </div>

        @if ($round->scheduled_at)
            <p class="text-xs text-gray-500">{{ __('talenma.direct_hire.round_scheduled_at') }} : {{ $round->scheduled_at->translatedFormat('d M Y H:i') }}</p>
        @endif
        @if (filled($round->meeting_url))
            <p class="mt-1 text-xs">
                <span class="text-gray-500">{{ __('talenma.direct_hire.round_meeting_url') }} :</span>
                <a href="{{ $round->meeting_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 underline underline-offset-2 break-all">
                    {{ __('talenma.direct_hire.round_meeting_url_open') }}
                </a>
            </p>
        @endif
        @if (filled($round->company_note))
            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $round->company_note }}</p>
        @endif

        @if ($isCancelled && filled($round->cancellation_reason))
            <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <p class="text-xs font-semibold text-gray-600">{{ __('talenma.direct_hire.round_cancellation_reason_label') }}</p>
                <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $round->cancellation_reason }}</p>
            </div>
        @endif

        @if ($canManage)
            <form class="mt-3 flex flex-wrap items-end gap-3" x-on:submit.prevent="saveStatus">
                <div class="min-w-[12rem] flex-1">
                    <x-input-label :for="'round-status-'.$round->id" :value="__('talenma.direct_hire.round_result')" />
                    <select
                        id="round-status-{{ $round->id }}"
                        class="mt-1 block w-full border-gray-300 rounded-lg text-sm"
                        x-model="status"
                        x-bind:disabled="loading"
                    >
                        @foreach ($roundStatuses as $status)
                            <option value="{{ $status }}">{{ __('talenma.direct_hire.round_status_'.$status) }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button type="submit" x-bind:disabled="loading">{{ __('talenma.direct_hire.round_save_result') }}</x-primary-button>
            </form>

            @if ($canCancel)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-semibold text-rose-700 hover:text-rose-800"
                        x-show="!cancelling"
                        x-on:click="openCancel"
                        x-bind:disabled="loading"
                    >
                        {{ __('talenma.direct_hire.round_cancel') }}
                    </button>

                    <div x-show="cancelling" x-cloak class="space-y-3">
                        <div>
                            <x-input-label :for="'round-cancel-reason-'.$round->id" :value="__('talenma.direct_hire.round_cancel_reason')" />
                            <textarea
                                id="round-cancel-reason-{{ $round->id }}"
                                rows="3"
                                maxlength="2000"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                                placeholder="{{ __('talenma.direct_hire.round_cancel_reason_placeholder') }}"
                                x-model="cancelReason"
                                x-on:input="cancelError = null"
                                x-bind:aria-invalid="Boolean(cancelError)"
                                x-bind:disabled="loading"
                            ></textarea>
                            <p x-show="cancelError" x-cloak class="mt-2 text-sm text-red-600" x-text="cancelError"></p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-secondary-button type="button" x-on:click="closeCancel" x-bind:disabled="loading">
                                {{ __('talenma.common.close') }}
                            </x-secondary-button>
                            <button
                                type="button"
                                class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-rose-700 disabled:opacity-50"
                                x-on:click="submitCancel"
                                x-bind:disabled="loading"
                            >
                                {{ __('talenma.direct_hire.round_cancel_confirm') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @if ($canManage)
        <div x-show="editing" x-cloak class="space-y-3">
            <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.round_edit') }}</p>
            <div>
                <x-input-label :for="'edit-round-title-'.$round->id" :value="__('talenma.direct_hire.round_title')" />
                <x-text-input
                    id="edit-round-title-{{ $round->id }}"
                    class="mt-1 block w-full"
                    maxlength="120"
                    x-model="title"
                    x-on:input="clearEditError('title')"
                    x-bind:aria-invalid="Boolean(editErrors.title)"
                    x-bind:disabled="loading"
                />
                <p x-show="editErrors.title" x-cloak class="mt-2 text-sm text-red-600" x-text="editErrors.title"></p>
            </div>
            <div>
                <x-input-label :for="'edit-round-scheduled-'.$round->id" :value="__('talenma.direct_hire.round_scheduled_at')" />
                <input
                    id="edit-round-scheduled-{{ $round->id }}"
                    type="datetime-local"
                    class="mt-1 block w-full border-gray-300 rounded-lg text-sm"
                    x-model="scheduledAt"
                    x-on:input="clearEditError('scheduled_at')"
                    x-bind:aria-invalid="Boolean(editErrors.scheduled_at)"
                    x-bind:disabled="loading"
                >
                <p x-show="editErrors.scheduled_at" x-cloak class="mt-2 text-sm text-red-600" x-text="editErrors.scheduled_at"></p>
            </div>
            <div>
                <x-input-label :for="'edit-round-meeting-'.$round->id" :value="__('talenma.direct_hire.round_meeting_url')" />
                <x-text-input
                    id="edit-round-meeting-{{ $round->id }}"
                    type="url"
                    class="mt-1 block w-full"
                    maxlength="2048"
                    placeholder="{{ __('talenma.direct_hire.round_meeting_url_placeholder') }}"
                    x-model="meetingUrl"
                    x-on:input="clearEditError('meeting_url')"
                    x-bind:aria-invalid="Boolean(editErrors.meeting_url)"
                    x-bind:disabled="loading"
                />
                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.round_meeting_url_hint') }}</p>
                <p x-show="editErrors.meeting_url" x-cloak class="mt-2 text-sm text-red-600" x-text="editErrors.meeting_url"></p>
            </div>
            <div>
                <x-input-label :for="'edit-round-note-'.$round->id" :value="__('talenma.direct_hire.round_note')" />
                <textarea
                    id="edit-round-note-{{ $round->id }}"
                    rows="2"
                    maxlength="2000"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm"
                    x-model="companyNote"
                    x-on:input="clearEditError('company_note')"
                    x-bind:aria-invalid="Boolean(editErrors.company_note)"
                    x-bind:disabled="loading"
                ></textarea>
                <p x-show="editErrors.company_note" x-cloak class="mt-2 text-sm text-red-600" x-text="editErrors.company_note"></p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <x-secondary-button type="button" x-on:click="closeEdit" x-bind:disabled="loading">
                    {{ __('talenma.direct_hire.cancel') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="saveDetails" x-bind:disabled="loading">
                    {{ __('talenma.direct_hire.round_save') }}
                </x-primary-button>
            </div>
        </div>
    @endif
    </div>
</div>
