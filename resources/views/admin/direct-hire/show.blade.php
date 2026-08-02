@php
    $hireRoute = $hireRoute ?? 'admin.direct-hire';
    $talent = $directHire->talent;
    $canManageRounds = $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS;
    $canRespondToDeferral = $directHire->awaitsCompanyDeferralReply();
    $canWithdraw = $directHire->isOpen() && ! $canRespondToDeferral;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div
            x-data="companyTalentProfileDrawer({
                composeUrl: '',
                csrf: @js(csrf_token()),
                labels: @js([
                    'profileError' => __('talenma.home.search_drawer_error'),
                    'error' => __('talenma.home.search_drawer_error'),
                ]),
            })"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.show_title') }}</h2>
                    <p class="text-sm text-gray-500 truncate">{{ __('talenma.direct_hire.title_prefix') }} {{ $directHire->shortSubject() }}</p>
                    <p class="text-sm text-gray-500">
                        {{ __('talenma.direct_hire.with_talent', ['name' => $directHire->talentDisplayName()]) }}
                        @if ($talent)
                            —
                            <button
                                type="button"
                                class="text-indigo-600 hover:text-indigo-800 underline underline-offset-2"
                                @click="openProfile(@js(route('admin.direct-hire.talent-profile', $talent)))"
                            >{{ __('talenma.direct_hire.view_talent') }}</button>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ __('talenma.direct_hire.admin_origin_label') }} :
                        <span class="font-medium text-slate-700">{{ $directHire->hireOriginLabel() }}</span>
                        @if ($directHire->isStaffOnBehalf())
                            <span class="text-slate-300"> · </span>
                            {{ __('talenma.direct_hire.admin_beneficiary_label') }} :
                            <span class="font-medium text-slate-700">{{ $directHire->companyDisplayName() }}</span>
                        @endif
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 shrink-0">
                    <span class="text-sm text-gray-500">{{ __('talenma.direct_hire.status_prefix') }}</span>
                    @include('company.direct-hire._status-badge', ['directHire' => $directHire])
                </div>
            </div>

            @include('company._talent-profile-drawer', ['hideHireActions' => true])

            <div class="mt-2">
                <a
                    href="{{ route($hireRoute.'.index') }}"
                    class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    {{ __('talenma.direct_hire.show_back_to_list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div id="direct-hire-main-column" class="space-y-5 min-w-0">
                <div id="direct-hire-proposal-card" class="bg-white rounded-2xl border p-6 space-y-5">
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

                    @include('direct-hire._proposal-history', ['directHire' => $directHire])
                </div>

                @if ($canManageRounds || $directHire->rounds->isNotEmpty())
                    <div id="direct-hire-rounds-block" class="bg-white rounded-2xl border p-6 space-y-4 relative">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.direct_hire.rounds_title') }}</h3>

                        <div id="direct-hire-rounds-list" class="space-y-4">
                            @include('company.direct-hire._rounds-list', [
                                'directHire' => $directHire,
                                'canManageRounds' => $canManageRounds,
                                'roundStatuses' => $roundStatuses,
                                'hireRoute' => $hireRoute,
                            ])
                        </div>

                        @if ($canManageRounds)
                            <form
                                id="direct-hire-round-create"
                                method="POST"
                                action="{{ route($hireRoute.'.rounds.store', $directHire) }}"
                                class="relative mt-2 rounded-lg border border-dashed border-slate-200 bg-slate-50/40 px-3.5 py-3 space-y-2.5"
                                x-data="directHireRoundCreate({
                                    storeUrl: @js(route($hireRoute.'.rounds.store', $directHire)),
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

                            <div
                                id="direct-hire-close-actions"
                                class="relative grid sm:grid-cols-2 gap-4 pt-2"
                                x-data="directHireCompanyClose({
                                    url: @js(route($hireRoute.'.close', $directHire)),
                                    messages: @js([
                                        'error' => __('talenma.direct_hire.close_error'),
                                        'networkError' => __('talenma.direct_hire.network_error'),
                                        'chatClosed' => __('talenma.direct_hire.chat_closed'),
                                        'chatClosedBadge' => __('talenma.direct_hire.chat_closed_badge'),
                                        'confirmHiredTitle' => __('talenma.direct_hire.close_confirm_hired_title'),
                                        'confirmHiredBody' => __('talenma.direct_hire.close_confirm_hired_body'),
                                        'confirmHiredBtn' => __('talenma.direct_hire.close_confirm_hired_btn'),
                                        'confirmNegativeTitle' => __('talenma.direct_hire.close_confirm_negative_title'),
                                        'confirmNegativeBody' => __('talenma.direct_hire.close_confirm_negative_body'),
                                        'confirmNegativeBtn' => __('talenma.direct_hire.close_confirm_negative_btn'),
                                        'confirmCancel' => __('talenma.direct_hire.cancel'),
                                    ]),
                                })"
                            >
                                <form method="POST" action="{{ route($hireRoute.'.close', $directHire) }}" class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 space-y-3" @submit.prevent="requestConfirm">
                                    @csrf
                                    <input type="hidden" name="outcome" value="hired">
                                    <p class="text-sm font-semibold text-emerald-900">{{ __('talenma.direct_hire.close_hired') }}</p>
                                    <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}" x-bind:disabled="loading"></textarea>
                                    <button type="submit" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 disabled:opacity-60" x-bind:disabled="loading">{{ __('talenma.direct_hire.close_hired_btn') }}</button>
                                </form>
                                <form method="POST" action="{{ route($hireRoute.'.close', $directHire) }}" class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 space-y-3" @submit.prevent="requestConfirm">
                                    @csrf
                                    <input type="hidden" name="outcome" value="closed_negative">
                                    <p class="text-sm font-semibold text-rose-900">{{ __('talenma.direct_hire.close_negative') }}</p>
                                    <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}" x-bind:disabled="loading"></textarea>
                                    <button type="submit" class="inline-flex px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700 disabled:opacity-60" x-bind:disabled="loading">{{ __('talenma.direct_hire.close_negative_btn') }}</button>
                                </form>

                                <div
                                    x-show="confirming"
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                    role="dialog"
                                    aria-modal="true"
                                    x-on:keydown.escape.window="closeConfirm"
                                >
                                    <div class="absolute inset-0 bg-slate-900/40" x-on:click="closeConfirm" aria-hidden="true"></div>
                                    <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200">
                                        <p class="text-base font-semibold text-slate-900" x-text="confirmTitle"></p>
                                        <p class="mt-2 text-sm text-slate-600" x-text="confirmBody"></p>
                                        <div class="mt-5 flex flex-wrap justify-end gap-3">
                                            <x-secondary-button type="button" x-on:click="closeConfirm" x-bind:disabled="loading">
                                                <span x-text="messages.confirmCancel"></span>
                                            </x-secondary-button>
                                            <button
                                                type="button"
                                                x-bind:class="confirmButtonClass"
                                                x-on:click="confirmSubmit"
                                                x-bind:disabled="loading"
                                                x-text="confirmButtonLabel"
                                            ></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($canRespondToDeferral)
                    <div
                        id="direct-hire-deferral-reply"
                        class="relative bg-white rounded-2xl border p-6"
                        x-data="directHireDeferralReply({
                            url: @js(route($hireRoute.'.deferral', $directHire)),
                            messages: @js([
                                'error' => __('talenma.direct_hire.deferral_reply_error'),
                                'networkError' => __('talenma.direct_hire.network_error'),
                                'noteMax' => __('talenma.direct_hire.chat_max'),
                                'refuseNoteRequired' => __('talenma.direct_hire.deferral_refuse_note_required'),
                                'chatClosedBadge' => __('talenma.direct_hire.chat_closed_badge'),
                                'chatClosed' => __('talenma.direct_hire.chat_closed'),
                                'acceptConfirmTitle' => __('talenma.direct_hire.deferral_accept_confirm_title'),
                                'acceptConfirmBody' => __('talenma.direct_hire.deferral_accept_confirm_body'),
                                'acceptConfirmBtn' => __('talenma.direct_hire.deferral_accept_confirm_btn'),
                                'refuseConfirmTitle' => __('talenma.direct_hire.deferral_refuse_confirm_title'),
                                'refuseConfirmBody' => __('talenma.direct_hire.deferral_refuse_confirm_body'),
                                'refuseConfirmBtn' => __('talenma.direct_hire.deferral_refuse_confirm_btn'),
                                'confirmCancel' => __('talenma.direct_hire.defer_confirm_cancel'),
                            ]),
                        })"
                    >
                        <form method="POST" action="{{ route($hireRoute.'.deferral', $directHire) }}" class="space-y-4" @submit.prevent>
                            @csrf
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.deferral_reply_title') }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.direct_hire.deferral_reply_subtitle') }}</p>
                            </div>
                            <div>
                                <x-input-label for="company_deferral_note" :value="__('talenma.direct_hire.deferral_reply_note')" />
                                <textarea
                                    id="company_deferral_note"
                                    name="note"
                                    rows="3"
                                    maxlength="2000"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('talenma.direct_hire.deferral_reply_note_placeholder') }}"
                                    x-ref="note"
                                    x-bind:disabled="loading"
                                ></textarea>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    class="inline-flex justify-center px-4 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 disabled:opacity-60"
                                    x-bind:disabled="loading"
                                    @click="requestAction('accept')"
                                >
                                    {{ __('talenma.direct_hire.deferral_accept_btn') }}
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex justify-center px-4 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700 disabled:opacity-60"
                                    x-bind:disabled="loading"
                                    @click="requestAction('refuse')"
                                >
                                    {{ __('talenma.direct_hire.deferral_refuse_btn') }}
                                </button>
                            </div>
                        </form>

                        <div
                            x-show="pendingAction"
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            role="dialog"
                            aria-modal="true"
                            x-on:keydown.escape.window="closeActionConfirm"
                        >
                            <div class="absolute inset-0 bg-slate-900/40" x-on:click="closeActionConfirm" aria-hidden="true"></div>
                            <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200">
                                <p class="text-base font-semibold text-slate-900" x-text="confirmTitle()"></p>
                                <p class="mt-2 text-sm text-slate-600" x-text="confirmBody()"></p>
                                <div class="mt-5 flex flex-wrap justify-end gap-3">
                                    <x-secondary-button type="button" x-on:click="closeActionConfirm" x-bind:disabled="loading">
                                        <span x-text="messages.confirmCancel"></span>
                                    </x-secondary-button>
                                    <button
                                        type="button"
                                        x-bind:class="confirmBtnClass()"
                                        x-on:click="confirmAction"
                                        x-bind:disabled="loading"
                                        x-text="confirmBtnLabel()"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($canWithdraw)
                    @include('company.direct-hire._withdraw', ['directHire' => $directHire, 'hireRoute' => $hireRoute])
                @endif
            </div>

            <aside class="min-w-0 w-full lg:self-stretch">
                @include('direct-hire._chat', ['directHire' => $directHire, 'sidebar' => true])
            </aside>
        </div>
    </div>
</x-app-layout>
