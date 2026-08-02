@php
    $events = $directHire->relationLoaded('statusEvents')
        ? $directHire->statusEvents
        : $directHire->statusEvents()->with('actor')->orderBy('created_at')->orderBy('id')->get();
@endphp

<div id="direct-hire-proposal-history" class="rounded-xl border border-slate-200 bg-white overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100">
        <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.history_title') }}</h4>
        <p class="mt-0.5 text-xs text-slate-400">{{ __('talenma.direct_hire.history_subtitle') }}</p>
    </div>

    @if ($events->isEmpty())
        <p class="px-4 py-5 text-center text-sm text-slate-500">{{ __('talenma.direct_hire.history_empty') }}</p>
    @else
        <ol class="relative space-y-0 px-3 py-2 before:absolute before:left-[1.35rem] before:top-4 before:bottom-4 before:w-px before:bg-slate-200">
            @foreach ($events as $event)
                @include('direct-hire._history-event', ['event' => $event])
            @endforeach
        </ol>
    @endif
</div>
