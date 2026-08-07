@php
    $statusBadge = match ($application?->normalizedStatus()) {
        'received' => 'bg-sky-50 text-sky-800',
        'viewed' => 'bg-indigo-50 text-indigo-800',
        'closed' => 'bg-slate-100 text-slate-700',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $job->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $job->companyProfile?->displayName() }}
                @if ($job->professionSummary() !== '')
                    · {{ $job->professionSummary() }}
                @endif
                @if ($job->locationLabel() !== '')
                    · {{ $job->locationLabel() }}
                @endif
                @if ($job->workModesSummary() !== '')
                    · {{ $job->workModesSummary() }}
                @endif
            </p>
            <a
                href="{{ route('talent.jobs.index') }}"
                class="mt-2 inline-flex text-sm font-medium text-indigo-700 hover:text-indigo-900"
            >← {{ __('talenma.jobs.back') }}</a>
        </div>
    </x-slot>

    <x-process-help topic="jobs" />

    <div class="py-5 sm:py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($job->isClosed())
            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                {{ __('talenma.jobs.talent_closed_notice') }}
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] gap-4 lg:gap-5 lg:items-start">
            <article class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-3 min-w-0">
                <p class="text-sm text-gray-500">
                    {{ $job->contractTypeLabel() }}
                    @if ($job->professionSummary() !== '')
                        · {{ $job->professionSummary() }}
                    @endif
                    @if ($job->experienceLabel() !== '')
                        · {{ $job->experienceLabel() }}
                    @endif
                    @if ($job->workModesSummary() !== '')
                        · {{ $job->workModesSummary() }}
                    @endif
                </p>
                <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $job->description }}</div>
            </article>

            <section class="rounded-xl border border-slate-200 bg-white p-5 sm:p-6 space-y-4 min-w-0 lg:sticky lg:top-20">
                @if ($application)
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.application_panel_title') }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ __('talenma.jobs.application_submitted_at', [
                                        'date' => ($application->submitted_at ?? $application->created_at)?->translatedFormat('d M Y, H:i') ?? '—',
                                    ]) }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                {{ $application->statusLabel() }}
                            </span>
                        </div>

                        <div class="rounded-lg border border-indigo-100 bg-indigo-50/70 px-3.5 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-800/80">
                                {{ __('talenma.jobs.cover_message') }}
                            </p>
                            @if (filled($application->cover_message))
                                <p class="mt-1.5 text-sm text-indigo-950 whitespace-pre-wrap leading-relaxed">{{ $application->cover_message }}</p>
                            @else
                                <p class="mt-1.5 text-sm text-indigo-800/70 italic">{{ __('talenma.jobs.cover_message_empty') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.jobs.application_history_title') }}</h4>
                            <p class="mt-0.5 text-xs text-slate-400">{{ __('talenma.jobs.application_history_subtitle') }}</p>
                        </div>

                        @if ($applicationHistory->isEmpty())
                            <p class="px-4 py-5 text-center text-sm text-slate-500">{{ __('talenma.jobs.application_history_empty') }}</p>
                        @else
                            <ol class="relative space-y-0 px-3 py-2 before:absolute before:left-[1.35rem] before:top-4 before:bottom-4 before:w-px before:bg-slate-200">
                                @foreach ($applicationHistory as $event)
                                    @php
                                        $dotClass = match ($event['status'] ?? null) {
                                            'received', 'submitted' => 'bg-sky-500',
                                            'viewed', 'reviewed', 'shortlisted' => 'bg-indigo-500',
                                            'closed', 'rejected', 'withdrawn' => 'bg-slate-500',
                                            default => 'bg-slate-400',
                                        };
                                    @endphp
                                    <li class="relative flex gap-3 rounded-xl px-2 py-2.5">
                                        <span class="relative z-[1] mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white ring-4 ring-white">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
                                        </span>
                                        <div class="min-w-0 flex-1 pt-0.5">
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                                {{ $event['at']?->translatedFormat('d M Y, H:i') }}
                                                @if (filled($event['actor'] ?? null))
                                                    <span class="font-medium normal-case tracking-normal text-slate-500">· {{ $event['actor'] }}</span>
                                                @endif
                                            </p>
                                            <p class="mt-0.5 text-sm leading-snug font-medium text-slate-800">{{ $event['label'] }}</p>
                                            @if (filled($event['detail'] ?? null))
                                                <p class="mt-1 text-xs text-slate-500">{{ $event['detail'] }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @elseif ($job->isPublished())
                    <div id="talent-job-apply-card" class="relative space-y-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.apply') }}</h3>
                            <p class="mt-0.5 text-xs text-slate-500">{{ __('talenma.jobs.apply_hint') }}</p>
                        </div>
                        <form
                            method="POST"
                            action="{{ route('talent.jobs.apply', $job) }}"
                            class="space-y-4"
                            data-ajax
                            data-loading-target="talent-job-apply-card"
                            data-error-message="{{ __('talenma.jobs.save_error') }}"
                            data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                            novalidate
                        >
                            @csrf
                            <div>
                                <x-input-label for="cover_message" :value="__('talenma.jobs.cover_message')" />
                                <textarea
                                    id="cover_message"
                                    name="cover_message"
                                    rows="5"
                                    maxlength="2000"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    placeholder="{{ __('talenma.jobs.cover_placeholder') }}"
                                >{{ old('cover_message') }}</textarea>
                                <x-input-error :messages="$errors->get('cover_message')" class="mt-2" />
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <x-primary-button type="submit">{{ __('talenma.jobs.submit_application') }}</x-primary-button>
                                <button
                                    type="button"
                                    data-reset
                                    class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >{{ __('talenma.jobs.cancel') }}</button>
                            </div>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-slate-600">{{ __('talenma.jobs.talent_closed_notice') }}</p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
