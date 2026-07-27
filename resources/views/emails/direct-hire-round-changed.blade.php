<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_changed.greeting', ['name' => $talentName]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_changed.body_'.$event, [
            'title' => $round->title,
            'subject' => $directHire->shortSubject(),
            'company' => $companyName,
        ]) }}
    </p>
    <div style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;">
        <p style="margin:0 0 8px;color:#374151;"><strong>{{ __('talenma.mail.direct_hire_round_changed.details_title') }}</strong></p>
        <p style="margin:0;">{{ __('talenma.mail.direct_hire_round_changed.detail_status', ['status' => $round->statusLabel()]) }}</p>
        @if ($round->scheduled_at)
            <p style="margin:6px 0 0;">{{ __('talenma.mail.direct_hire_round_changed.detail_scheduled', ['date' => $round->scheduled_at->translatedFormat('d M Y H:i')]) }}</p>
        @endif
        @if (filled($round->meeting_url))
            <p style="margin:6px 0 0;">
                {{ __('talenma.mail.direct_hire_round_changed.detail_meeting') }}
                <a href="{{ $round->meeting_url }}" style="color:#4f46e5;text-decoration:underline;">{{ $round->meeting_url }}</a>
            </p>
        @endif
        @if (filled($round->company_note))
            <p style="margin:10px 0 0;color:#374151;">{{ $round->company_note }}</p>
        @endif
    </div>
    <p style="margin:0 0 24px;">
        <a href="{{ $url }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.direct_hire_round_changed.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_changed.closing') }}
    </p>
</x-emails.layout>
