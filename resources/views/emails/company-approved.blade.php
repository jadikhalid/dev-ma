<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.greeting', ['name' => $user->name]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.body') }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.body_followup') }}
    </p>
    <p style="margin:0 0 24px;">
        <a href="{{ route('dashboard') }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.company_approved.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_approved.closing') }}
    </p>
</x-emails.layout>
