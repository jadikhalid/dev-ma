@php
    use App\Support\PortalHost;
@endphp

<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_confirmation.greeting', ['name' => $demo->contact_name]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_confirmation.body', ['company' => $demo->company_name]) }}
    </p>
    @if (filled($demo->message))
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
            <strong style="color:#374151;">{{ __('talenma.mail.company_demo_confirmation.message_label') }}</strong>
        </p>
        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-wrap;">{{ $demo->message }}</p>
    @endif
    <p style="margin:0 0 24px;">
        <a href="{{ PortalHost::companyRootUrl() }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.company_demo_confirmation.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_confirmation.closing') }}
    </p>
</x-emails.layout>
