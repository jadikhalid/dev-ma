<x-emails.layout :show-brand="false" :show-footer="false" brand-label="JADI DIGITAL">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.jadi_digital_contact.greeting') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.jadi_digital_contact.body', ['name' => $name]) }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.jadi_digital_contact.name_label') }}</strong>
        {{ $name }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.jadi_digital_contact.email_label') }}</strong>
        {{ $email }}
    </p>
    @if (filled($company))
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
            <strong style="color:#374151;">{{ __('talenma.mail.jadi_digital_contact.company_label') }}</strong>
            {{ $company }}
        </p>
    @endif
    <p style="margin:16px 0 0;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-wrap;">{{ $body }}</p>
    <p style="margin:24px 0 0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.jadi_digital_contact.closing') }}
    </p>
</x-emails.layout>
