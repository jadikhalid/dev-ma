@php
    $roleLabel = match ($role) {
        'company' => __('talenma.contact_page.form.role_company'),
        'other' => __('talenma.contact_page.form.role_other'),
        default => __('talenma.contact_page.form.role_talent'),
    };
@endphp

<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.contact_message.greeting') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.contact_message.body', ['name' => $name]) }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.contact_message.audience_label') }}</strong>
        {{ $audience === 'company' ? __('talenma.mail.contact_message.audience_company') : __('talenma.mail.contact_message.audience_talent') }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.contact_message.name_label') }}</strong>
        {{ $name }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.contact_message.email_label') }}</strong>
        {{ $email }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.contact_message.role_label') }}</strong>
        {{ $roleLabel }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.contact_message.subject_label') }}</strong>
        {{ $subjectLine }}
    </p>
    <p style="margin:16px 0 0;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-wrap;">{{ $body }}</p>
    <p style="margin:24px 0 0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.contact_message.closing') }}
    </p>
</x-emails.layout>
