<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color: #1f2937; margin: 0; line-height: 1.35; }
        table.layout { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td.sidebar { width: 31%; vertical-align: top; background: #454545; color: #f8fafc; padding: 16px 12px 18px; }
        td.main { width: 69%; vertical-align: top; background: #fff; padding: 16px 16px 18px 14px; }

        .photo-wrap { text-align: center; margin-bottom: 14px; }
        .photo { width: 92px; height: 92px; border-radius: 50%; object-fit: cover; border: 2px solid #d1d5db; }
        .photo-placeholder { width: 92px; height: 92px; border-radius: 50%; background: #5b5b5b; border: 2px solid #d1d5db; line-height: 88px; font-size: 26pt; color: #e5e7eb; text-align: center; margin: 0 auto; }

        .sidebar-title { font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.1em; color: #f8fafc; border-bottom: 1px solid #737373; margin: 0 0 6px; padding-bottom: 3px; font-weight: bold; }
        .sidebar-block { margin-bottom: 12px; }
        .sidebar-text { font-size: 7.8pt; margin: 0 0 4px; color: #e5e7eb; text-align: left; word-wrap: break-word; }
        .skill-label { font-weight: bold; color: #fff; font-size: 8pt; }
        .skill-items { font-size: 7.5pt; color: #cbd5e1; }
        .lang-row { margin-bottom: 6px; }
        .lang-name { font-size: 7.6pt; text-transform: uppercase; margin: 0 0 2px; color: #f9fafb; }
        .lang-track { width: 100%; height: 5px; background: #636363; }
        .lang-fill { height: 5px; background: #f3f4f6; }

        .hero-name { font-size: 22pt; font-weight: bold; margin: 0; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.01em; line-height: 1.05; }
        .hero-headline { font-size: 9pt; margin: 4px 0 0; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.02em; }
        .contact-wrap { text-align: right; font-size: 7.6pt; color: #374151; line-height: 1.45; }
        .contact-line { margin: 0 0 2px; }
        .social-links { margin-top: 4px; line-height: 1; }
        .social-link { display: inline-block; margin-left: 8px; vertical-align: middle; }
        .social-link img { width: 12px; height: 12px; display: block; border: 0; }

        .section { margin-top: 12px; page-break-inside: avoid; }
        .section-title { font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; color: #374151; border-bottom: 1px solid #d1d5db; margin: 0 0 8px; padding-bottom: 3px; font-weight: bold; }
        table.timeline { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        td.tl-meta { width: 24%; vertical-align: top; font-size: 7.6pt; color: #4b5563; padding-right: 6px; }
        td.tl-meta strong { display: block; color: #374151; font-size: 7.8pt; margin-bottom: 2px; }
        td.tl-marker { width: 14px; vertical-align: top; border-left: 1px solid #cbd5e1; position: relative; }
        td.tl-body { vertical-align: top; padding-left: 8px; padding-bottom: 4px; }
        .tl-dot { width: 7px; height: 7px; border-radius: 50%; background: #fff; border: 1px solid #9ca3af; margin: 2px 0 0 -4px; }
        .tl-role { font-weight: bold; font-size: 8.6pt; color: #111827; margin: 0 0 3px; }
        .tl-dates { font-size: 7.4pt; color: #6b7280; }
        .bullets { margin: 0; padding-left: 13px; font-size: 8pt; color: #374151; }
        .bullets li { margin-bottom: 2px; }
        .edu-degree { font-weight: bold; font-size: 8.4pt; color: #111827; margin: 0 0 2px; }
        .edu-school { font-size: 8pt; color: #4b5563; margin: 0; }
        .cert-row { font-size: 8pt; margin: 0 0 2px; color: #374151; }
    </style>
</head>
<body>
@php
    $t = fn (string $key) => __("talenma.cv_builder.sections.{$key}", [], $locale);
    $d = $data;
    $has = fn (string $v) => filled(trim((string) $v));
    $skillGroups = collect($d['skill_groups'] ?? [])->filter(fn ($g) => $has($g['label'] ?? '') || $has($g['items'] ?? ''));
    $experiences = collect($d['experiences'] ?? [])->filter(fn ($e) => $has($e['title'] ?? '') || $has($e['company'] ?? ''));
    $education = collect($d['education'] ?? [])->filter(fn ($e) => $has($e['degree'] ?? '') || $has($e['school'] ?? ''));
    $languages = collect($d['languages'] ?? [])->filter(fn ($l) => $has($l['name'] ?? ''));
    $certs = collect($d['certifications'] ?? [])->filter(fn ($c) => $has($c));
    $socialLinks = collect([
        'linkedin' => (string) ($d['linkedin_url'] ?? ''),
        'github' => (string) ($d['github_url'] ?? ''),
        'portfolio' => (string) ($d['portfolio_url'] ?? ''),
    ])->filter(fn (string $url) => $has($url));
@endphp
<table class="layout" cellpadding="0" cellspacing="0">
<tr>
    <td class="sidebar">
        <div class="photo-wrap">
            @if (! empty($photoSrc))
                <img src="{{ $photoSrc }}" alt="" class="photo">
            @else
                <div class="photo-placeholder">
                    {{ mb_substr((string) ($d['full_name'] ?? '?'), 0, 1) }}
                </div>
            @endif
        </div>

        @if ($has($d['summary'] ?? ''))
            <div class="sidebar-block">
                <p class="sidebar-title">{{ mb_strtoupper($t('summary')) }}</p>
                <p class="sidebar-text">{{ $d['summary'] }}</p>
            </div>
        @endif

        @if ($skillGroups->isNotEmpty())
            <div class="sidebar-block">
                <p class="sidebar-title">{{ mb_strtoupper($t('skills')) }}</p>
                @foreach ($skillGroups as $group)
                    <p class="sidebar-text" style="margin-bottom:4px;">
                        @if ($has($group['label'] ?? ''))<span class="skill-label">{{ $group['label'] }}</span><br>@endif
                        @if ($has($group['items'] ?? ''))<span class="skill-items">{{ $group['items'] }}</span>@endif
                    </p>
                @endforeach
            </div>
        @endif

        @if ($languages->isNotEmpty())
            <div class="sidebar-block">
                <p class="sidebar-title">{{ mb_strtoupper($t('languages')) }}</p>
                @foreach ($languages as $lang)
                    @php($percent = \App\Support\TalentCv\TalentCvLanguageLevel::barPercent((string) ($lang['level'] ?? '')))
                    <div class="lang-row">
                        <p class="lang-name">{{ mb_strtoupper($lang['name']) }}</p>
                        <table class="lang-track" cellpadding="0" cellspacing="0"><tr>
                            <td class="lang-fill" style="width: {{ $percent }}%;"></td>
                            <td style="width: {{ 100 - $percent }}%;"></td>
                        </tr></table>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($has($d['availability_line'] ?? ''))
            <div class="sidebar-block">
                <p class="sidebar-title">{{ mb_strtoupper($t('availability')) }}</p>
                <p class="sidebar-text">{{ $d['availability_line'] }}</p>
            </div>
        @endif
    </td>
    <td class="main">
        <table style="width:100%;border-collapse:collapse;margin-bottom:4px;">
            <tr>
                <td style="width:58%;vertical-align:top;">
                    @if ($has($d['full_name'] ?? ''))
                        <p class="hero-name">{{ mb_strtoupper($d['full_name']) }}</p>
                    @endif
                    @if ($has($d['headline'] ?? ''))
                        <p class="hero-headline">{{ mb_strtoupper($d['headline']) }}</p>
                    @endif
                </td>
                <td style="width:42%;vertical-align:top;" class="contact-wrap">
                    @if ($has($d['city'] ?? ''))<p class="contact-line">{{ $d['city'] }}</p>@endif
                    @if ($has($d['phone'] ?? ''))<p class="contact-line">{{ $d['phone'] }}</p>@endif
                    @if ($has($d['email'] ?? ''))<p class="contact-line">{{ $d['email'] }}</p>@endif
                    @if ($socialLinks->isNotEmpty())
                        <p class="contact-line social-links">
                            @foreach ($socialLinks as $type => $url)
                                <a href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}" class="social-link" title="{{ $url }}">
                                    <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type, '#374151') }}" alt="{{ $type }}">
                                </a>
                            @endforeach
                        </p>
                    @endif
                </td>
            </tr>
        </table>

        @if ($experiences->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ mb_strtoupper($t('experience')) }}</p>
                @foreach ($experiences as $exp)
                    <table class="timeline" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="tl-meta">
                                @if ($has($exp['company'] ?? ''))<strong>{{ $exp['company'] }}</strong>@endif
                                @if ($has($exp['location'] ?? ''))<br>{{ $exp['location'] }}@endif
                                <br><span class="tl-dates">
                                    {{ $exp['start'] }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                                </span>
                            </td>
                            <td class="tl-marker"><div class="tl-dot"></div></td>
                            <td class="tl-body">
                                @if ($has($exp['title'] ?? ''))<p class="tl-role">{{ $exp['title'] }}</p>@endif
                                <ul class="bullets">
                                    @foreach (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif

        @if ($education->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ mb_strtoupper($t('education')) }}</p>
                @foreach ($education as $edu)
                    <table class="timeline" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="tl-meta">
                                @if ($has($edu['year'] ?? ''))<strong>{{ $edu['year'] }}</strong>@endif
                                @if ($has($edu['school'] ?? ''))<br>{{ $edu['school'] }}@endif
                            </td>
                            <td class="tl-marker"><div class="tl-dot"></div></td>
                            <td class="tl-body">
                                @if ($has($edu['degree'] ?? ''))<p class="edu-degree">{{ $edu['degree'] }}</p>@endif
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif

        @if ($certs->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ mb_strtoupper($t('certifications')) }}</p>
                @foreach ($certs as $cert)
                    <p class="cert-row">{{ $cert }}</p>
                @endforeach
            </div>
        @endif
    </td>
</tr>
</table>
</body>
</html>
