@php
    $siteUrl = 'https://www.talentsdumaroc.com';
    $logoUrl = asset('images/logo2-white.png');
    $socials = array_filter([
        'LinkedIn' => config('talenma.social.linkedin'),
        'Facebook' => config('talenma.social.facebook'),
        'Instagram' => config('talenma.social.instagram'),
    ]);
    $socialParts = [];
    foreach ($socials as $label => $url) {
        $socialParts[] = '<a href="'.e($url).'" style="color:#c7d2fe;font-weight:600;text-decoration:none;">'.e($label).'</a>';
    }
@endphp
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#111827;">
    <tr>
        <td align="center" style="padding:20px 24px 18px;text-align:center;">
            <a href="{{ $siteUrl }}" style="text-decoration:none;">
                <img src="{{ $logoUrl }}" alt="Talents du Maroc" width="180" style="display:block;margin:0 auto 12px;width:180px;max-width:80%;height:auto;border:0;">
            </a>
            <p style="margin:0 0 14px;font-size:13px;line-height:1.5;">
                <a href="{{ $siteUrl }}" style="color:#c7d2fe;font-weight:700;text-decoration:none;">www.talentsdumaroc.com</a>
            </p>
            @if ($socialParts !== [])
                <p style="margin:0 0 6px;font-size:11px;line-height:1.3;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#6b7280;">{{ __('talenma.footer.follow_us') }}</p>
                <p style="margin:0 0 14px;font-size:12px;line-height:1.6;">{!! implode('&nbsp;·&nbsp;', $socialParts) !!}</p>
            @endif
            <p style="margin:0;font-size:11px;line-height:1.45;color:#6b7280;">&copy; {{ date('Y') }} {{ __('talenma.footer.copyright') }}</p>
        </td>
    </tr>
</table>
