<x-emails.layout :show-brand="false" :show-footer="false">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_trial_request.greeting') }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_trial_request.body', [
            'company' => $trial->company_name,
            'contact' => $trial->contact_name,
        ]) }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.company_trial_request.email_label') }}</strong>
        {{ $trial->email }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.company_trial_request.phone_label') }}</strong>
        {{ $trial->phone }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.company_trial_request.sector_label') }}</strong>
        {{ $trial->sector }}
    </p>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
        <strong style="color:#374151;">{{ __('talenma.mail.company_trial_request.country_label') }}</strong>
        {{ $trial->company_country }}
    </p>
    @if (filled($trial->company_website))
        <p style="margin:0 0 8px;font-size:14px;line-height:1.7;color:#6b7280;">
            <strong style="color:#374151;">{{ __('talenma.mail.company_trial_request.website_label') }}</strong>
            {{ $trial->company_website }}
        </p>
    @endif
    <p style="margin:16px 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-wrap;">{{ $trial->company_description }}</p>
    <p style="margin:0 0 16px;">
        <a href="{{ route('admin.company-trial-requests.index') }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.company_trial_request.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.company_trial_request.closing') }}
    </p>
</x-emails.layout>
