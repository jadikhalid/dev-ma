@php
    $statusBadge = match ($job->status) {
        'published' => 'bg-emerald-50 text-emerald-700',
        'closed' => 'bg-gray-100 text-gray-600',
        'hidden' => 'bg-slate-100 text-slate-700',
        'postponed' => 'bg-violet-50 text-violet-800',
        default => 'bg-amber-50 text-amber-800',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ $job->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge }}">{{ $job->statusLabel() }}</span>
                    @if ($job->professionSummary() !== '')
                        · {{ $job->professionSummary() }}
                    @endif
                    @if ($job->locationLabel() !== '')
                        · {{ $job->locationLabel() }}
                    @endif
                    @if ($job->remote_ok)
                        · {{ __('talenma.jobs.remote') }}
                    @endif
                </p>
                <a
                    href="{{ route('company.jobs.index') }}"
                    class="mt-2 inline-flex text-sm font-medium text-emerald-700 hover:text-emerald-900"
                >← {{ __('talenma.jobs.back') }}</a>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($job->isClosed())
                    <p class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        {{ __('talenma.jobs.closed_readonly_notice') }}
                    </p>
                @else
                    <a href="{{ route('company.jobs.edit', $job) }}" class="inline-flex px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.jobs.edit') }}</a>
                    @if (! $job->isPublished())
                        <form
                            method="POST"
                            action="{{ route('company.jobs.publish', $job) }}"
                            data-ajax
                            data-loading-target="company-job-show-page"
                            data-error-message="{{ __('talenma.jobs.save_error') }}"
                            data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                        >
                            @csrf
                            <button type="submit" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">{{ __('talenma.jobs.publish') }}</button>
                        </form>
                    @endif
                    @if (! $job->isHidden())
                        <form
                            method="POST"
                            action="{{ route('company.jobs.hide', $job) }}"
                            data-ajax
                            data-loading-target="company-job-show-page"
                            data-error-message="{{ __('talenma.jobs.save_error') }}"
                            data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                        >
                            @csrf
                            <button type="submit" class="inline-flex px-4 py-2 border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50">{{ __('talenma.jobs.hide') }}</button>
                        </form>
                    @endif
                    <div
                        class="inline-flex"
                        x-data="companyJobConfirmAction({
                            messages: @js([
                                'title' => __('talenma.jobs.close_confirm_title'),
                                'body' => __('talenma.jobs.close_confirm_body'),
                                'confirm' => __('talenma.jobs.close_confirm_btn'),
                                'cancel' => __('talenma.jobs.close_confirm_cancel'),
                            ]),
                        })"
                    >
                        <form
                            method="POST"
                            action="{{ route('company.jobs.close', $job) }}"
                            data-loading-target="company-job-show-page"
                            data-error-message="{{ __('talenma.jobs.save_error') }}"
                            data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                            @submit.prevent="requestConfirm($event)"
                        >
                            @csrf
                            <button type="submit" class="inline-flex px-4 py-2 border border-amber-300 text-amber-800 text-sm font-semibold rounded-lg hover:bg-amber-50">{{ __('talenma.jobs.close') }}</button>
                        </form>

                        <div
                            x-show="confirming"
                            x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4"
                            role="dialog"
                            aria-modal="true"
                            @keydown.escape.window="closeConfirm"
                        >
                            <div class="absolute inset-0 bg-slate-900/40" @click="closeConfirm" aria-hidden="true"></div>
                            <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200">
                                <p class="text-base font-semibold text-slate-900" x-text="messages.title"></p>
                                <p class="mt-2 text-sm text-slate-600" x-text="messages.body"></p>
                                <div class="mt-5 flex flex-wrap justify-end gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50"
                                        @click="closeConfirm"
                                        x-text="messages.cancel"
                                    ></button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-amber-700"
                                        @click="confirmSubmit"
                                        x-text="messages.confirm"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div
        id="company-job-show-page"
        class="relative py-5 sm:py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="companyTalentProfileDrawer({
            composeUrl: @js(route('inbox.store')),
            csrf: @js(csrf_token()),
            labels: @js([
                'profileError' => __('talenma.home.search_drawer_error'),
                'error' => __('talenma.home.search_drawer_error'),
                'composeError' => __('talenma.inbox.error'),
                'composeMinBody' => __('talenma.inbox.compose_min_body'),
                'composeSubjectRequired' => __('talenma.inbox.compose_subject_required'),
                'directHireDisabled' => __('talenma.direct_hire.cta_disabled_hint'),
                'namedDisabled' => __('talenma.recruitment.named_blocked_open'),
                'talentLocked' => __('talenma.recruitment.talent_lock_badge'),
                'unlockError' => __('talenma.recruitment.talent_unlock_error'),
            ]),
        })"
    >
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-4 lg:gap-5 lg:items-start">
            @if ($job->isClosed())
                <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    {{ __('talenma.jobs.closed_readonly_body') }}
                </div>
            @endif
            <article class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-3 min-w-0">
                <p class="text-sm text-gray-500">
                    {{ $job->contractTypeLabel() }}
                    @if ($job->professionSummary() !== '')
                        · {{ $job->professionSummary() }}
                    @endif
                    @if ($job->experienceLabel() !== '')
                        · {{ $job->experienceLabel() }}
                    @endif
                </p>
                <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $job->description }}</div>
            </article>

            <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-4 min-w-0 lg:sticky lg:top-20">
                <div class="flex flex-col gap-1">
                    <div class="flex items-baseline justify-between gap-2">
                        <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.applications_matched') }}</h3>
                        <span class="text-xs font-semibold text-emerald-700">{{ $matchingApplications->count() }}</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ __('talenma.jobs.applications_matched_hint', [
                            'matched' => $matchingApplications->count(),
                            'total' => $applicationsTotal,
                        ]) }}
                    </p>
                </div>

                @forelse ($matchingApplications as $application)
                    @php
                        $talent = $application->talent;
                        $talentName = $talent?->formalDisplayName() ?? '—';
                        $profileUrl = $talent ? route('company.talent.show', $talent) : null;
                    @endphp
                    <div class="rounded-lg border border-slate-200/90 p-3.5 space-y-3">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                @if ($profileUrl && $job->isMutable())
                                    <button
                                        type="button"
                                        class="text-left text-sm font-semibold text-emerald-700 hover:text-emerald-900 underline underline-offset-2"
                                        @click="openProfile(@js($profileUrl))"
                                    >{{ $talentName }}</button>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">{{ $talentName }}</p>
                                @endif
                                <p class="mt-0.5 text-xs text-gray-500">{{ $application->submitted_at?->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            @php
                                $currentStatus = $application->normalizedStatus();
                                $nextStatuses = $job->isMutable() ? $application->availableNextStatuses() : [];
                            @endphp
                            @if ($nextStatuses === [])
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ $application->statusLabel() }}
                                </span>
                            @else
                                <div
                                    class="shrink-0"
                                    x-data="companyJobApplicationStatus({
                                        current: @js($currentStatus),
                                        messages: @js([
                                            'viewedTitle' => __('talenma.jobs.application_status_viewed_confirm_title'),
                                            'viewedBody' => __('talenma.jobs.application_status_viewed_confirm_body'),
                                            'viewedBtn' => __('talenma.jobs.application_status_viewed_confirm_btn'),
                                            'closedTitle' => __('talenma.jobs.application_status_closed_confirm_title'),
                                            'closedBody' => __('talenma.jobs.application_status_closed_confirm_body'),
                                            'closedBtn' => __('talenma.jobs.application_status_closed_confirm_btn'),
                                            'cancel' => __('talenma.jobs.application_status_confirm_cancel'),
                                        ]),
                                    })"
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('company.jobs.applications.update', [$job, $application]) }}"
                                        class="flex flex-wrap items-center gap-2"
                                        data-loading-target="company-job-show-page"
                                        data-error-message="{{ __('talenma.jobs.save_error') }}"
                                        data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                                        @submit.prevent="requestUpdate($event)"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" x-model="selectedStatus" class="border-gray-300 rounded-lg text-sm">
                                            <option value="{{ $currentStatus }}" selected>{{ __('talenma.jobs.application_status_'.$currentStatus) }}</option>
                                            @foreach ($nextStatuses as $status)
                                                <option value="{{ $status }}">{{ __('talenma.jobs.application_status_'.$status) }}</option>
                                            @endforeach
                                        </select>
                                        <button
                                            type="submit"
                                            class="px-3 py-2 text-sm font-semibold text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-50 disabled:opacity-50"
                                            :disabled="selectedStatus === current"
                                        >{{ __('talenma.jobs.save_status') }}</button>
                                    </form>

                                    <div
                                        x-show="confirming"
                                        x-cloak
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                        role="dialog"
                                        aria-modal="true"
                                        @keydown.escape.window="closeConfirm"
                                    >
                                        <div class="absolute inset-0 bg-slate-900/40" @click="closeConfirm" aria-hidden="true"></div>
                                        <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-200">
                                            <p class="text-base font-semibold text-slate-900" x-text="confirmTitle"></p>
                                            <p class="mt-2 text-sm text-slate-600" x-text="confirmBody"></p>
                                            <div class="mt-5 flex flex-wrap justify-end gap-3">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50"
                                                    @click="closeConfirm"
                                                    x-text="messages.cancel"
                                                ></button>
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm text-white"
                                                    :class="confirmButtonClass"
                                                    @click="confirmSubmit"
                                                    x-text="confirmButtonLabel"
                                                ></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if ($application->cover_message)
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $application->cover_message }}</p>
                        @endif
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-200 bg-slate-50/80 px-3 py-6 text-center text-sm text-slate-500">
                        {{ $applicationsTotal > 0
                            ? __('talenma.jobs.applications_matched_empty')
                            : __('talenma.jobs.applications_empty') }}
                    </p>
                @endforelse
            </section>
        </div>

        @include('company._talent-profile-drawer')
    </div>
</x-app-layout>
