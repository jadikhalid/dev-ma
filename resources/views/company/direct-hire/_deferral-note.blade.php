@if ($directHire->company_deferral_responded_at && $directHire->status === \App\Models\DirectHireRequest::STATUS_DEFERRED)
    <div id="direct-hire-company-deferral-note" class="rounded-lg border border-violet-100 bg-violet-50/60 px-3.5 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">{{ __('talenma.direct_hire.company_deferral_accepted_label') }}</p>
        <p class="mt-1 text-sm text-slate-900">
            {{ $directHire->company_deferral_responded_at->translatedFormat('d M Y H:i') }}
        </p>
        @if (filled($directHire->company_deferral_note))
            <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                <span class="font-medium text-slate-900">{{ __('talenma.direct_hire.decision_comment_label') }} :</span>
                {{ $directHire->company_deferral_note }}
            </p>
        @endif
    </div>
@endif
