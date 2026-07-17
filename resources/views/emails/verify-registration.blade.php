<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.verify_registration.greeting', ['name' => $pending->greetingName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.verify_registration.body') }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#6b7280;">
        {{ __('talenma.mail.verify_registration.expiry', ['minutes' => \App\Services\PendingRegistrationService::EXPIRY_MINUTES]) }}
    </p>
    <p style="margin:0 0 24px;">
        <a href="{{ $verificationUrl }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.verify_registration.cta') }}
        </a>
    </p>
    <p style="margin:0 0 16px;font-size:13px;line-height:1.6;color:#6b7280;">
        {{ __('talenma.mail.verify_registration.link_fallback') }}
    </p>
    <p style="margin:0 0 24px;font-size:12px;line-height:1.5;color:#9ca3af;word-break:break-all;">
        {{ $verificationUrl }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.verify_registration.closing') }}
    </p>
</x-emails.layout>
