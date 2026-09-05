@php
    $statusBadge = match ($job->status) {
        'published' => 'bg-emerald-50 text-emerald-700',
        'closed' => 'bg-gray-100 text-gray-600',
        'hidden' => 'bg-slate-100 text-slate-700',
        'postponed' => 'bg-violet-50 text-violet-800',
        default => 'bg-amber-50 text-amber-800',
    };
    $creatorAttribution = $job->creatorAttribution();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
            <div class="min-w-0 sm:w-1/2 sm:max-w-[50%] sm:shrink-0">
                @include('jobs._show-header-meta', [
                    'job' => $job,
                    'statusBadge' => $statusBadge,
                    'creatorAttribution' => $creatorAttribution,
                    'showCompanyInMeta' => true,
                    'backUrl' => route('admin.jobs.index'),
                    'backLinkClass' => 'text-indigo-700 hover:text-indigo-900',
                ])
                @php $publicShareUrl = route('jobs.gate', $job); @endphp
                <div
                    class="mt-3 flex flex-col gap-1.5"
                    x-data="{ copied: false }"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                        {{ __('talenma.jobs.public_share_url_label') }}
                    </span>
                    <div class="min-w-0 flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
                        <a
                            href="{{ $publicShareUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="min-w-0 truncate text-xs font-medium text-indigo-700 hover:text-indigo-900"
                            title="{{ $publicShareUrl }}"
                        >{{ $publicShareUrl }}</a>
                        <button
                            type="button"
                            class="shrink-0 inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100"
                            @click="navigator.clipboard.writeText(@js($publicShareUrl)).then(() => { copied = true; setTimeout(() => copied = false, 1600) })"
                        >
                            <span x-show="!copied">{{ __('talenma.jobs.public_share_url_copy') }}</span>
                            <span x-cloak x-show="copied">{{ __('talenma.jobs.public_share_url_copied') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 sm:w-1/2 sm:justify-end">
                @if ($job->isClosed())
                    <p class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                        {{ __('talenma.jobs.closed_readonly_notice') }}
                    </p>
                @else
                    <a href="{{ route('admin.jobs.edit', $job) }}" class="inline-flex px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.jobs.edit') }}</a>
                    @if (! $job->isPublished())
                        <form
                            method="POST"
                            action="{{ route('admin.jobs.publish', $job) }}"
                            data-ajax
                            data-loading-target="admin-job-show-page"
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
                            action="{{ route('admin.jobs.hide', $job) }}"
                            data-ajax
                            data-loading-target="admin-job-show-page"
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
                            action="{{ route('admin.jobs.close', $job) }}"
                            data-loading-target="admin-job-show-page"
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
                <div
                    class="inline-flex"
                    x-data="companyJobConfirmAction({
                        messages: @js([
                            'badge' => __('talenma.jobs.delete_confirm_badge'),
                            'title' => __('talenma.jobs.delete_confirm_title'),
                            'body' => __('talenma.jobs.delete_confirm_body'),
                            'confirm' => __('talenma.jobs.delete_confirm_btn'),
                            'cancel' => __('talenma.jobs.delete_confirm_cancel'),
                        ]),
                    })"
                >
                    <form
                        method="POST"
                        action="{{ route('admin.jobs.destroy', $job) }}"
                        data-loading-target="admin-job-show-page"
                        data-error-message="{{ __('talenma.jobs.save_error') }}"
                        data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                        @submit.prevent="requestConfirm($event)"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex px-4 py-2 border border-rose-300 text-rose-700 text-sm font-semibold rounded-lg hover:bg-rose-50">{{ __('talenma.jobs.delete') }}</button>
                    </form>

                    <template x-teleport="body">
                        <div
                            x-show="confirming"
                            x-cloak
                            class="fixed inset-0 z-[80] flex items-center justify-center p-4"
                            role="alertdialog"
                            aria-modal="true"
                            aria-labelledby="admin-job-delete-title"
                            @keydown.escape.window="closeConfirm"
                        >
                            <div class="absolute inset-0 bg-slate-900/50" @click="closeConfirm" aria-hidden="true"></div>
                            <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl ring-2 ring-rose-200">
                                <p class="inline-flex items-center rounded-md bg-rose-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-rose-700 ring-1 ring-rose-100" x-text="messages.badge"></p>
                                <p id="admin-job-delete-title" class="mt-3 text-base font-semibold text-slate-900" x-text="messages.title"></p>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="messages.body"></p>
                                <div class="mt-5 flex flex-wrap justify-end gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50"
                                        @click="closeConfirm"
                                        x-text="messages.cancel"
                                    ></button>
                                    <button
                                        type="button"
                                        class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-rose-700"
                                        @click="confirmSubmit"
                                        x-text="messages.confirm"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </x-slot>

    <div
        id="admin-job-show-page"
        class="relative py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6"
        x-data="companyTalentProfileDrawer({
            composeUrl: '',
            csrf: @js(csrf_token()),
            labels: @js([
                'profileError' => __('talenma.home.search_drawer_error'),
                'error' => __('talenma.home.search_drawer_error'),
            ]),
        })"
    >
        @if ($job->isClosed())
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                {{ __('talenma.jobs.closed_readonly_body') }}
            </div>
        @endif
        <dl class="rounded-2xl border bg-white p-5 sm:p-6 grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.admin_company') }}</dt>
                <dd class="mt-1 text-gray-900 font-medium">{{ $job->companyProfile?->displayName() ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.admin_creator') }}</dt>
                <dd class="mt-1 text-gray-900">
                    {{ $creatorAttribution['company'] }}
                    @if (filled($creatorAttribution['person']))
                        · {{ $creatorAttribution['person'] }}
                        @if (filled($creatorAttribution['role']))
                            <span class="text-gray-500">({{ $creatorAttribution['role'] }})</span>
                        @endif
                    @endif
                    @if ($job->creator?->email)
                        <span class="text-gray-500">— {{ $job->creator->email }}</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_sector') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->sectorLabel() !== '' ? $job->sectorLabel() : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_profession') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->professionLabel() !== '' ? $job->professionLabel() : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_experience') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->experienceLabel() !== '' ? $job->experienceLabel() : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_contract') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->contractTypeLabel() }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_work_modes') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->workModesSummary() !== '' ? $job->workModesSummary() : '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.admin_dates') }}</dt>
                <dd class="mt-1 text-gray-900">
                    {{ __('talenma.jobs.admin_published_at') }}: {{ $job->published_at?->translatedFormat('d M Y, H:i') ?? '—' }}
                    · {{ __('talenma.jobs.admin_closed_at') }}: {{ $job->closed_at?->translatedFormat('d M Y, H:i') ?? '—' }}
                </dd>
            </div>
        </dl>

        <article class="rounded-2xl border bg-white p-6 sm:p-8 space-y-3">
            <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $job->description }}</div>
        </article>

        <section class="rounded-2xl border bg-white p-6 sm:p-8 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.jobs.applications') }}</h3>

            @if ($job->isExternalApplication())
                <div class="rounded-xl border border-amber-200 bg-amber-50/70 px-4 py-3 text-sm text-amber-900 space-y-1">
                    <p>{{ __('talenma.jobs.external_applications_notice') }}</p>
                    @if (filled($job->external_apply_url))
                        <p>
                            <a href="{{ $job->external_apply_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold underline underline-offset-2 break-all">
                                {{ $job->external_apply_url }}
                            </a>
                        </p>
                    @endif
                </div>
            @else
            @forelse ($job->applications as $application)
                @php
                    $talent = $application->talent;
                    $talentName = $talent?->formalDisplayName() ?? '—';
                    $profileUrl = $talent ? route('admin.direct-hire.talent-profile', $talent) : null;
                @endphp
                <div class="border rounded-xl p-4 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="min-w-0">
                            @if ($profileUrl)
                                <button
                                    type="button"
                                    class="text-left text-sm font-semibold text-indigo-700 hover:text-indigo-900 underline underline-offset-2"
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
                                    action="{{ route('admin.jobs.applications.update', [$job, $application]) }}"
                                    class="flex items-center gap-2"
                                    data-loading-target="admin-job-show-page"
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
                                        class="px-3 py-2 text-sm font-semibold text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-50 disabled:opacity-50"
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
                <p class="text-sm text-gray-500">{{ __('talenma.jobs.applications_empty') }}</p>
            @endforelse
            @endif
        </section>

        <a href="{{ route('admin.jobs.index') }}" class="inline-flex text-sm font-medium text-indigo-700 hover:text-indigo-900">← {{ __('talenma.jobs.back') }}</a>

        @include('company._talent-profile-drawer', ['hideHireActions' => true])
    </div>
</x-app-layout>
