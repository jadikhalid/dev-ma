<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.confirm_pending_email.greeting', ['name' => $user->name]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.confirm_pending_email.body', ['email' => $pendingEmail]) }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#6b7280;">
        {{ __('talenma.mail.confirm_pending_email.expiry', ['minutes' => \App\Services\PendingEmailChangeService::TTL_MINUTES]) }}
    </p>
    <p style="margin:0 0 24px;">
        <a href="{{ $confirmUrl }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.confirm_pending_email.cta') }}
        </a>
    </p>
    <p style="margin:0 0 16px;font-size:13px;line-height:1.6;color:#6b7280;">
        {{ __('talenma.mail.confirm_pending_email.link_fallback') }}
    </p>
    <p style="margin:0 0 24px;font-size:12px;line-height:1.5;color:#9ca3af;word-break:break-all;">
        {{ $confirmUrl }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.confirm_pending_email.closing') }}
    </p>
</x-emails.layout>
