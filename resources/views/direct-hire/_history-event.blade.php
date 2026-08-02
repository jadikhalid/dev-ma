@php
    $dotClass = match (true) {
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_PROPOSED => 'bg-sky-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_TALENT_DECISION
            && $event->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS => 'bg-emerald-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_TALENT_DECISION
            && $event->status === \App\Models\DirectHireRequest::STATUS_DECLINED => 'bg-rose-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_TALENT_DECISION
            && $event->status === \App\Models\DirectHireRequest::STATUS_DEFERRED => 'bg-amber-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_DEFERRAL_ACKNOWLEDGED => 'bg-violet-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_WITHDRAWN => 'bg-slate-500',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_CLOSED
            && $event->status === \App\Models\DirectHireRequest::STATUS_HIRED => 'bg-emerald-600',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_CLOSED => 'bg-rose-500',
        default => 'bg-slate-400',
    };
    $rowClass = match (true) {
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_PROPOSED => 'hover:bg-sky-50/80',
        $event->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS => 'hover:bg-emerald-50/80',
        $event->status === \App\Models\DirectHireRequest::STATUS_DECLINED => 'hover:bg-rose-50/80',
        $event->status === \App\Models\DirectHireRequest::STATUS_DEFERRED => 'hover:bg-amber-50/80',
        $event->event === \App\Models\DirectHireStatusEvent::EVENT_DEFERRAL_ACKNOWLEDGED => 'hover:bg-violet-50/80',
        default => 'hover:bg-slate-50',
    };
    $actorLabel = $event->actorLabel();
@endphp
<li class="relative flex gap-3 rounded-xl px-2 py-2.5 transition {{ $rowClass }}">
    <span class="relative z-[1] mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white ring-4 ring-white">
        <span class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
    </span>
    <div class="min-w-0 flex-1 pt-0.5">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            {{ $event->created_at?->translatedFormat('d M Y, H:i') }}
            @if (filled($actorLabel))
                <span class="font-medium normal-case tracking-normal text-slate-500">· {{ $actorLabel }}</span>
            @endif
        </p>
        <p class="mt-0.5 text-sm leading-snug font-medium text-slate-800">{{ $event->label() }}</p>
        @if (filled($event->comment))
            <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.history_comment_label') }}</p>
                <p class="mt-1 whitespace-pre-line leading-relaxed">{{ $event->comment }}</p>
            </div>
        @endif
    </div>
</li>
