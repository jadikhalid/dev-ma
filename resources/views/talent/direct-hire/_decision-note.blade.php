@if (filled($directHire->talent_decision_note))
    <div id="direct-hire-talent-note" class="rounded-lg bg-slate-50/80 border border-slate-100 px-3.5 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.your_note') }}</p>
        <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $directHire->talent_decision_note }}</p>
    </div>
@endif
