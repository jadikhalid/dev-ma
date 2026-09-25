<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.greeting', ['name' => $user->companyMailPersonName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.body') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.body_followup') }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.credentials_intro') }}
    </p>
    <div style="margin:0 0 24px;padding:14px 16px;background-color:#f3f4f6;border-radius:12px;font-size:14px;line-height:1.7;color:#111827;">
        <p style="margin:0 0 6px;"><strong>{{ __('talenma.mail.company_approved.email_label') }}</strong> {{ $user->email }}</p>
        <p style="margin:0;"><strong>{{ __('talenma.mail.company_approved.password_label') }}</strong> {{ $plainPassword }}</p>
    </div>
    <p style="margin:0 0 24px;">
        <a href="{{ \App\Support\PortalHost::companyLoginUrl() }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.company_approved.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.closing') }}
    </p>
</x-emails.layout>
