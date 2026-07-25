<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_cancelled.greeting', ['name' => $greetingName]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_cancelled.body', [
            'title' => $round->title,
            'subject' => $directHire->shortSubject(),
            'company' => $directHire->companyDisplayName(),
        ]) }}
    </p>
    @if (filled($round->cancellation_reason))
        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;">
            <strong style="color:#374151;">{{ __('talenma.mail.direct_hire_round_cancelled.reason_label') }}</strong><br>
            {{ $round->cancellation_reason }}
        </p>
    @endif
    <p style="margin:0 0 24px;">
        <a href="{{ $url }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.direct_hire_round_cancelled.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_round_cancelled.closing') }}
    </p>
</x-emails.layout>
