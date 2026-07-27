@if (filled($directHire->closure_note) && $directHire->isTerminal())
    <div id="direct-hire-closure-note" class="rounded-lg border border-slate-100 bg-slate-50/80 px-3.5 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
        <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $directHire->closure_note }}</p>
    </div>
@else
    <div id="direct-hire-closure-note" class="hidden" aria-hidden="true"></div>
@endif
