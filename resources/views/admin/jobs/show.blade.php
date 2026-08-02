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
                    · {{ $job->companyProfile?->displayName() ?? '—' }}
                    @if ($job->locationLabel() !== '')
                        · {{ $job->locationLabel() }}
                    @endif
                    @if ($job->remote_ok)
                        · {{ __('talenma.jobs.remote') }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
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
                @if (! $job->isPostponed())
                    <form
                        method="POST"
                        action="{{ route('admin.jobs.postpone', $job) }}"
                        data-ajax
                        data-loading-target="admin-job-show-page"
                        data-error-message="{{ __('talenma.jobs.save_error') }}"
                        data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                    >
                        @csrf
                        <button type="submit" class="inline-flex px-4 py-2 border border-violet-300 text-violet-800 text-sm font-semibold rounded-lg hover:bg-violet-50">{{ __('talenma.jobs.postpone') }}</button>
                    </form>
                @endif
                @if (! $job->isClosed())
                    <form
                        method="POST"
                        action="{{ route('admin.jobs.close', $job) }}"
                        data-ajax
                        data-loading-target="admin-job-show-page"
                        data-error-message="{{ __('talenma.jobs.save_error') }}"
                        data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                    >
                        @csrf
                        <button type="submit" class="inline-flex px-4 py-2 border border-amber-300 text-amber-800 text-sm font-semibold rounded-lg hover:bg-amber-50">{{ __('talenma.jobs.close') }}</button>
                    </form>
                @endif
                <form
                    method="POST"
                    action="{{ route('admin.jobs.destroy', $job) }}"
                    data-ajax
                    data-confirm="{{ __('talenma.jobs.delete_confirm') }}"
                    data-loading-target="admin-job-show-page"
                    data-error-message="{{ __('talenma.jobs.save_error') }}"
                    data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex px-4 py-2 border border-rose-300 text-rose-700 text-sm font-semibold rounded-lg hover:bg-rose-50">{{ __('talenma.jobs.delete') }}</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div id="admin-job-show-page" class="relative py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <dl class="rounded-2xl border bg-white p-5 sm:p-6 grid sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.admin_company') }}</dt>
                <dd class="mt-1 text-gray-900 font-medium">{{ $job->companyProfile?->displayName() ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.admin_creator') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->creator?->name ?? '—' }} @if ($job->creator?->email)<span class="text-gray-500">({{ $job->creator->email }})</span>@endif</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.jobs.field_contract') }}</dt>
                <dd class="mt-1 text-gray-900">{{ $job->contractTypeLabel() }}</dd>
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

            @forelse ($job->applications as $application)
                <div class="border rounded-xl p-4 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $application->talent?->publicDisplayName() ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $application->submitted_at?->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <form
                            method="POST"
                            action="{{ route('admin.jobs.applications.update', [$job, $application]) }}"
                            class="flex items-center gap-2"
                            data-ajax
                            data-loading-target="admin-job-show-page"
                            data-error-message="{{ __('talenma.jobs.save_error') }}"
                            data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                        >
                            @csrf
                            @method('PATCH')
                            <select name="status" class="border-gray-300 rounded-lg text-sm">
                                @foreach (\App\Models\JobApplication::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($application->status === $status)>{{ __('talenma.jobs.application_status_'.$status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 text-sm font-semibold text-indigo-700 border border-indigo-200 rounded-lg hover:bg-indigo-50">{{ __('talenma.jobs.save_status') }}</button>
                        </form>
                    </div>
                    @if ($application->cover_message)
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $application->cover_message }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('talenma.jobs.applications_empty') }}</p>
            @endforelse
        </section>

        <a href="{{ route('admin.jobs.index') }}" class="inline-flex text-sm font-medium text-indigo-700 hover:text-indigo-900">← {{ __('talenma.jobs.back') }}</a>
    </div>
</x-app-layout>
