@php
    $dotClass = match (true) {
        $event->event === 'submitted' => 'bg-sky-500',
        $event->event === 'comment_updated' => 'bg-indigo-500',
        $event->status === 'in_progress' => 'bg-amber-500',
        in_array($event->status, ['completed_successful', 'completed'], true) => 'bg-emerald-500',
        in_array($event->status, ['completed_unsuccessful', 'cancelled'], true) => 'bg-rose-500',
        $event->status === 'pending' => 'bg-indigo-500',
        default => 'bg-slate-400',
    };
    $rowClass = match (true) {
        $event->event === 'submitted' => 'hover:bg-sky-50/80',
        $event->event === 'comment_updated' => 'hover:bg-indigo-50/80',
        $event->status === 'in_progress' => 'hover:bg-amber-50/80',
        in_array($event->status, ['completed_successful', 'completed'], true) => 'hover:bg-emerald-50/80',
        in_array($event->status, ['completed_unsuccessful', 'cancelled'], true) => 'hover:bg-rose-50/80',
        default => 'hover:bg-slate-50',
    };
@endphp
<li class="relative flex gap-3 rounded-xl px-2 py-2.5 transition {{ $rowClass }}">
    <span class="relative z-[1] mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white ring-4 ring-white">
        <span class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
    </span>
    <div class="min-w-0 flex-1 pt-0.5">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
            {{ $event->created_at?->translatedFormat('d M Y, H:i') }}
        </p>
        <p class="mt-0.5 text-sm leading-snug text-slate-800">{{ $event->label($isStaff, $recruitment->mode) }}</p>
        @if (filled($event->comment))
            <div class="mt-2 rounded-lg border border-indigo-100 bg-indigo-50/70 px-3 py-2.5 text-sm text-indigo-950">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-indigo-700">{{ __('talenma.recruitment.company_comment_label') }}</p>
                <p class="mt-1 whitespace-pre-line leading-relaxed">{{ $event->comment }}</p>
            </div>
        @endif
    </div>
</li>
