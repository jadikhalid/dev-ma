<div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-600">
    <span class="inline-flex items-center gap-2 font-semibold text-slate-900">
        @if ($job->advertiserLogoUrl())
            <img src="{{ $job->advertiserLogoUrl() }}" alt="" class="h-8 w-8 rounded-lg bg-white object-contain p-0.5 ring-1 ring-slate-200">
        @else
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-xs font-bold text-indigo-700 ring-1 ring-indigo-100" aria-hidden="true">{{ $job->advertiserInitials() }}</span>
        @endif
        <span>{{ $job->advertiserName() }}</span>
    </span>
    @foreach ($job->collaborationModeLabels() as $mode)
        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $mode }}</span>
    @endforeach
    @if ($job->locationLabel() !== '')
        <span class="inline-flex items-center gap-1.5">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
            </svg>
            <span>{{ $job->locationLabel() }}</span>
        </span>
    @endif
    @if ($job->published_at)
        <time datetime="{{ $job->published_at->toDateString() }}" class="inline-flex items-center gap-1.5">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
            </svg>
            <span>{{ __('talenma.jobs.card_published_on', ['date' => $job->published_at->translatedFormat('d M Y')]) }}</span>
        </time>
    @endif
</div>
