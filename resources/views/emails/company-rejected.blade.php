<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_rejected.greeting', ['name' => $user->name]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_rejected.thanks') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_rejected.body') }}
    </p>
    @if ($reason)
        <p style="margin:0 0 16px;padding:16px;background-color:#f9fafb;border-radius:12px;font-size:14px;line-height:1.7;color:#4b5563;">
            <strong>{{ __('talenma.mail.company_rejected.reason_label') }}</strong><br>
            {{ $reason }}
        </p>
    @endif
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_rejected.body_followup') }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_rejected.closing') }}
    </p>
</x-emails.layout>
