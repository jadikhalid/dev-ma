@php
    $statusBadge = fn (string $status) => match ($status) {
        'published' => 'bg-emerald-50 text-emerald-700',
        'closed' => 'bg-gray-100 text-gray-600',
        'hidden' => 'bg-slate-100 text-slate-700',
        'postponed' => 'bg-violet-50 text-violet-800',
        default => 'bg-amber-50 text-amber-800',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.admin_title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.admin_subtitle') }}</p>
            </div>
            <a
                href="{{ route('admin.jobs.create') }}"
                class="inline-flex justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700"
            >{{ __('talenma.jobs.create_external') }}</a>
        </div>
    </x-slot>

    <x-process-help topic="jobs" />

    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
        <form method="GET" action="{{ route('admin.jobs.index') }}" class="rounded-2xl border bg-white p-4 sm:p-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <label for="admin-jobs-q" class="block text-xs font-semibold text-slate-700">{{ __('talenma.jobs.admin_search') }}</label>
                    <input
                        id="admin-jobs-q"
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        placeholder="{{ __('talenma.jobs.admin_search_placeholder') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>
                <div class="sm:w-48">
                    <label for="admin-jobs-status" class="block text-xs font-semibold text-slate-700">{{ __('talenma.jobs.admin_filter_status') }}</label>
                    <select id="admin-jobs-status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('talenma.jobs.admin_filter_all') }}</option>
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ __('talenma.jobs.status_'.$statusOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                    {{ __('talenma.jobs.admin_filter_apply') }}
                </button>
            </div>
        </form>

        <div class="space-y-3">
            @forelse ($jobs as $job)
                @php $publicShareUrl = route('jobs.gate', $job); @endphp
                <div class="rounded-xl border bg-white p-5 hover:border-indigo-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="inline-flex items-center gap-2 text-base font-semibold text-gray-900">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="hover:text-indigo-700">
                                    {{ $job->title }}
                                </a>
                                @if ($job->hasUnseenChangesForStaff())
                                    <span class="relative flex h-2.5 w-2.5 shrink-0" title="{{ __('talenma.jobs.nav_new') }}">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                                    </span>
                                    <span class="sr-only">{{ __('talenma.jobs.nav_new') }}</span>
                                @endif
                            </h3>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $job->advertiserName() }}
                                @if ($job->isExternalApplication())
                                    <span class="inline-flex ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-amber-50 text-amber-800">{{ __('talenma.jobs.external_badge') }}</span>
                                @endif
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
                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($job->description), 140) }}</p>

                            <div
                                class="mt-3 flex flex-col gap-1.5 sm:flex-row sm:items-center sm:gap-2"
                                x-data="{ copied: false }"
                            >
                                <span class="shrink-0 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                    {{ __('talenma.jobs.public_share_url_label') }}
                                </span>
                                <div class="min-w-0 flex flex-1 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
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
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge($job->status) }}">
                                {{ $job->statusLabel() }}
                            </span>
                            <span class="text-xs text-gray-500">{{ __('talenma.jobs.applications_count', ['count' => $job->applications_count]) }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border bg-white p-8 text-center text-sm text-gray-500">
                    {{ __('talenma.jobs.admin_empty') }}
                </div>
            @endforelse
        </div>

        <div>{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
