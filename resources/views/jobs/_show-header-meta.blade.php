{{--
  Expects: $job, $statusBadge, $creatorAttribution
  Optional: $showCompanyInMeta (bool), $backUrl (string|null), $backLinkClass (string|null)
--}}
@php
    $showCompanyInMeta = $showCompanyInMeta ?? false;
    $backUrl = $backUrl ?? null;
    $backLinkClass = $backLinkClass ?? 'text-emerald-700 hover:text-emerald-900';
    $metaParts = array_values(array_filter([
        $showCompanyInMeta ? ($job->companyProfile?->displayName() ?: null) : null,
        $job->professionSummary() !== '' ? $job->professionSummary() : null,
        $job->locationLabel() !== '' ? $job->locationLabel() : null,
        $job->workModesSummary() !== '' ? $job->workModesSummary() : null,
    ]));
@endphp

<div class="min-w-0 space-y-3">
    <div class="space-y-2">
        <h2 class="text-xl font-bold text-gray-900 break-words">{{ $job->title }}</h2>

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5">
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge }}">{{ $job->statusLabel() }}</span>
            @foreach ($metaParts as $part)
                <span class="text-slate-300" aria-hidden="true">·</span>
                <span class="text-sm text-gray-500">{{ $part }}</span>
            @endforeach
        </div>
    </div>

    <div class="rounded-lg border border-slate-200/80 bg-slate-50/80 px-3 py-2.5 space-y-2">
        <div class="min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ __('talenma.jobs.created_by_label') }}</p>
            <p class="mt-0.5 text-sm text-slate-800 break-words">
                <span class="font-semibold">{{ $creatorAttribution['company'] }}</span>
                @if (filled($creatorAttribution['person']))
                    <span class="text-slate-400 mx-1" aria-hidden="true">·</span>
                    <span>{{ $creatorAttribution['person'] }}</span>
                    @if (filled($creatorAttribution['role']))
                        <span class="text-slate-500">({{ $creatorAttribution['role'] }})</span>
                    @endif
                @endif
            </p>
        </div>

        <div class="border-t border-slate-200/80 pt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-600">
            <p>
                <span class="font-semibold text-slate-500">{{ __('talenma.jobs.created_at_label') }}</span>
                <span class="ml-1">{{ $job->created_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
            </p>
            <p>
                <span class="font-semibold text-slate-500">{{ __('talenma.jobs.published_at_label') }}</span>
                <span class="ml-1">{{ $job->published_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
            </p>
            @if ($job->isClosed())
                <p>
                    <span class="font-semibold text-slate-500">{{ __('talenma.jobs.closed_at_label') }}</span>
                    <span class="ml-1">{{ $job->closed_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
                </p>
            @endif
        </div>
    </div>

    @if ($backUrl)
        <a
            href="{{ $backUrl }}"
            class="inline-flex text-sm font-medium {{ $backLinkClass }}"
        >← {{ __('talenma.jobs.back') }}</a>
    @endif
</div>
