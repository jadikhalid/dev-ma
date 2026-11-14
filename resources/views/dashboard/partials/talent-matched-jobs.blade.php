@php
    $matchedJobs = $matchedJobs ?? collect();
    $showJobMatchGate = (bool) ($showJobMatchGate ?? false);
@endphp

<section class="space-y-3" data-talent-matched-jobs>
    <div class="flex min-h-[3.25rem] items-center gap-2.5 rounded-2xl border border-emerald-200/70 bg-emerald-50/80 px-4">
        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm shadow-emerald-600/20" aria-hidden="true">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 2.606a48.59 48.59 0 0 0-4.5-.175m-14.25 2.606a48.59 48.59 0 0 1 4.5-.175m0 0a48.66 48.66 0 0 1 7.5 0m-7.5 0v-.916c0-1.18.91-2.164 2.09-2.201a51.964 51.964 0 0 1 3.32 0c1.18.037 2.09 1.022 2.09 2.201v.916m-7.5 0a48.66 48.66 0 0 0 7.5 0" />
            </svg>
        </span>
        <h2 class="min-w-0 text-sm font-bold uppercase tracking-[0.11em] text-emerald-950">
            {{ __('talenma.dashboard.talent.matched_jobs_title') }}
        </h2>
    </div>

    @if ($showJobMatchGate)
        <div class="rounded-2xl border border-dashed border-emerald-200 bg-white px-4 py-5 text-center">
            <p class="text-sm font-medium text-slate-800">{{ __('talenma.dashboard.talent.matched_jobs_gate_title') }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ __('talenma.jobs.talent_empty_matching_profile') }}</p>
            <a
                href="{{ route('profile.edit', ['panel' => 'talent']) }}"
                class="mt-4 inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
            >
                {{ __('talenma.dashboard.talent.complete_profile') }}
            </a>
        </div>
    @elseif ($matchedJobs->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-4 py-5 text-center">
            <p class="text-sm text-slate-500">{{ __('talenma.jobs.talent_empty_matching') }}</p>
            <a
                href="{{ route('talent.jobs.index') }}"
                class="mt-3 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-900"
            >
                {{ __('talenma.dashboard.talent.matched_jobs_browse') }}
            </a>
        </div>
    @else
        <ul class="space-y-2.5">
            @foreach ($matchedJobs as $job)
                <li>
                    <a
                        href="{{ $job['url'] }}"
                        class="group flex gap-3 rounded-2xl border border-slate-200/90 bg-white p-3.5 shadow-sm transition hover:-translate-y-px hover:border-emerald-200 hover:shadow"
                    >
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                            @if ($job['company_logo'])
                                <img src="{{ $job['company_logo'] }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ $job['company_initials'] }}
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 group-hover:text-emerald-800">{{ $job['title'] }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $job['company'] }}</p>
                                </div>
                                <span class="shrink-0 text-xs font-semibold text-emerald-700">{{ __('talenma.career_pages.offers_view') }}</span>
                            </div>
                            @if ($job['location'] || $job['work_modes'] !== [] || $job['experience'])
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @if ($job['location'])
                                        <span class="inline-flex max-w-full items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                                            <span class="truncate">{{ $job['location'] }}</span>
                                        </span>
                                    @endif
                                    @foreach ($job['work_modes'] as $mode)
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700">{{ $mode }}</span>
                                    @endforeach
                                    @if ($job['experience'])
                                        <span class="inline-flex items-center rounded-full bg-teal-50 px-2 py-0.5 text-[11px] font-medium text-teal-800">{{ $job['experience'] }}</span>
                                    @endif
                                </div>
                            @endif
                            @if ($job['date'] !== '')
                                <p class="mt-2 text-[11px] text-slate-400">{{ __('talenma.jobs.card_published_on', ['date' => $job['date']]) }}</p>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
