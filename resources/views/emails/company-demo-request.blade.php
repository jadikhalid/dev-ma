<x-emails.layout :show-brand="false" :show-footer="false">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_request.greeting') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_request.body', [
            'company' => $demo->company_name,
            'contact' => $demo->contact_name,
        ]) }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.company_demo_request.email_label') }}</strong>
        {{ $demo->email }}
    </p>
    @if (filled($demo->phone))
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
            <strong style="color:#374151;">{{ __('talenma.mail.company_demo_request.phone_label') }}</strong>
            {{ $demo->phone }}
        </p>
    @endif
    <p style="margin:16px 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-wrap;">{{ $demo->message }}</p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_demo_request.closing') }}
    </p>
</x-emails.layout>
