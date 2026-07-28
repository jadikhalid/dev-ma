<x-emails.layout :show-brand="false" :show-footer="false">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_deferral_acknowledged.greeting', ['name' => $talentName]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_deferral_acknowledged.body', ['company' => $companyName, 'subject' => $directHire->subject]) }}
    </p>
    @if (filled($directHire->company_deferral_note))
        <p style="margin:0 0 8px;font-size:13px;line-height:1.6;color:#6b7280;font-weight:600;">
            {{ __('talenma.mail.direct_hire_deferral_acknowledged.note_label') }}
        </p>
        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;">
            {{ $directHire->company_deferral_note }}
        </p>
    @endif
    <p style="margin:0 0 24px;">
        <a href="{{ $url }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.direct_hire_deferral_acknowledged.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.direct_hire_deferral_acknowledged.closing') }}
    </p>
</x-emails.layout>
