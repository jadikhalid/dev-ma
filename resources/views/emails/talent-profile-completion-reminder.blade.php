<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.greeting', ['name' => $user->formalDisplayName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.body') }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.body_followup') }}
    </p>
    <p style="margin:0 0 24px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.talent_profile_completion_reminder.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.closing') }}
    </p>
</x-emails.layout>
