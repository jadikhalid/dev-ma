@php
    /** @var array<string, mixed> $copy */
    $copy = $copy ?? [];
    $isLast = $isLast ?? false;
    $bannerBg = $isLast ? '#fef2f2' : '#fff7ed';
    $bannerBorder = $isLast ? '#ef4444' : '#f59e0b';
    $kickerColor = $isLast ? '#b91c1c' : '#c2410c';
    $titleColor = $isLast ? '#991b1b' : '#9a3412';
@endphp
<x-emails.layout>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 24px;background-color:{{ $bannerBg }};border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;border-left:4px solid {{ $bannerBorder }};">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:{{ $kickerColor }};">
                    {{ $copy['banner_kicker'] ?? '' }}
                </p>
                <p style="margin:0;font-size:18px;line-height:1.35;font-weight:800;color:{{ $titleColor }};">
                    {{ $copy['banner_title'] ?? '' }}
                </p>
            </td>
        </tr>
    </table>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_profile_completion_reminder.'.$step.'.greeting', ['name' => $user->formalDisplayName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#111827;">
        {{ $copy['good_news'] ?? '' }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['missing'] ?? '' }}
    </p>
    <p style="margin:0 0 28px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ $copy['cta'] ?? '' }}
        </a>
    </p>
    <p style="margin:0;font-size:40px;line-height:1;font-weight:800;color:#111827;">
        {{ $copy['stat_value'] ?? '' }}
    </p>
    <p style="margin:8px 0 20px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['stat'] ?? '' }}
    </p>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['pity'] ?? '' }}
    </p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ $copy['steps_title'] ?? '' }}
    </p>
    <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 24px;">
        @foreach (($copy['steps'] ?? []) as $index => $stepLine)
            <tr>
                <td style="padding:0 12px 10px 0;vertical-align:top;font-size:14px;font-weight:700;color:#4f46e5;">{{ $index + 1 }}</td>
                <td style="padding:0 0 10px;vertical-align:top;font-size:15px;line-height:1.6;color:#374151;">{{ $stepLine }}</td>
            </tr>
        @endforeach
    </table>
    <p style="margin:0 0 24px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ $copy['cta'] ?? '' }}
        </a>
    </p>
    <p style="margin:0 0 28px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ $copy['rest'] ?? '' }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ $copy['support_title'] ?? '' }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['support_lead'] ?? '' }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['support'] ?? '' }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['closing'] ?? '' }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ $copy['signature'] ?? '' }}
    </p>
</x-emails.layout>
