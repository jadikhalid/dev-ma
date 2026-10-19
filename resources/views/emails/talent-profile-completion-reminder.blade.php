<x-emails.layout>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 24px;background-color:#fff7ed;border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;border-left:4px solid #f59e0b;">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#c2410c;">
                    {{ __('talenma.mail.talent_profile_completion_reminder.banner_kicker') }}
                </p>
                <p style="margin:0;font-size:18px;line-height:1.35;font-weight:800;color:#9a3412;">
                    {{ __('talenma.mail.talent_profile_completion_reminder.banner_title') }}
                </p>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.greeting', ['name' => $user->formalDisplayName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#111827;">
        {{ __('talenma.mail.talent_profile_completion_reminder.good_news') }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.missing') }}
    </p>
    <p style="margin:0 0 28px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.talent_profile_completion_reminder.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:40px;line-height:1;font-weight:800;color:#111827;">
        {{ __('talenma.mail.talent_profile_completion_reminder.stat_value') }}
    </p>
    <p style="margin:8px 0 20px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.stat') }}
    </p>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.pity') }}
    </p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_profile_completion_reminder.steps_title') }}
    </p>
    <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 24px;">
        @foreach (__('talenma.mail.talent_profile_completion_reminder.steps') as $step)
            <tr>
                <td style="padding:0 12px 10px 0;vertical-align:top;font-size:14px;font-weight:700;color:#4f46e5;">{{ $loop->iteration }}</td>
                <td style="padding:0 0 10px;vertical-align:top;font-size:15px;line-height:1.6;color:#374151;">{{ $step }}</td>
            </tr>
        @endforeach
    </table>
    <p style="margin:0 0 24px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.talent_profile_completion_reminder.cta') }}
        </a>
    </p>
    <p style="margin:0 0 28px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_profile_completion_reminder.rest') }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_profile_completion_reminder.support_title') }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.support_lead') }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.support') }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.closing') }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.signature') }}
    </p>
</x-emails.layout>
