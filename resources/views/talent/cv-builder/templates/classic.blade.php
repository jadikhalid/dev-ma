<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="classic">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1e293b; margin: 0; line-height: 1.38; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @include('talent.cv-builder.templates.partials.cv-layout-shell', [
            'sidebarSide' => 'left',
            'sidebarWidth' => '32%',
            'mainWidth' => '68%',
            'sidebarBg' => '#1e3a5f',
        ])
        .sidebar { color: #f8fafc; padding: 16px 12px; }
        .photo-wrap { text-align: center; margin-bottom: 12px; }
        .photo { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 3px solid #93c5fd; display: block; margin: 0 auto; }
        .sidebar-name { font-size: 13pt; font-weight: bold; text-align: center; margin: 0 0 4px; line-height: 1.2; color: #fff; }
        .sidebar-headline { font-size: 7.5pt; text-align: center; color: #bfdbfe; margin: 0 0 0; line-height: 1.3; }
        .sidebar-divider { border: none; border-top: 1px solid #4b6478; margin: 11px 0; height: 0; }
        .sidebar-block { margin-bottom: 0; }
        .sidebar-title { font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.08em; color: #93c5fd; border-bottom: 1px solid #334155; margin: 0 0 8px; padding-bottom: 5px; font-weight: bold; }
        .sidebar-text { font-size: 8pt; margin: 0 0 3px; color: #e2e8f0; word-wrap: break-word; }
        .sidebar-text a { color: #e2e8f0; text-decoration: none; }
        .social-links { margin: 6px 0 2px; line-height: 1; }
        @media print {
            .social-links { margin-top: 14px; margin-bottom: 12px; }
        }
        .social-link { display: inline-block; margin-right: 16px; vertical-align: middle; }
        .social-link:last-child { margin-right: 0; }
        .social-link img { width: 14px; height: 14px; display: block; border: 0; }
        .skill-label { font-weight: bold; color: #fff; font-size: 8pt; }
        .skill-items { font-size: 7.5pt; color: #cbd5e1; }
        .main-headline { font-size: 11pt; color: #1e3a5f; font-weight: bold; margin: 0 0 12px; }
        .section { margin-bottom: 11px; page-break-inside: avoid; }
        .section-title { font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.06em; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; margin: 0 0 8px; padding-bottom: 5px; font-weight: bold; }
        .summary { margin: 0; text-align: justify; font-size: 9pt; }
        .entry { margin-bottom: 7px; }
        .entry-head { width: 100%; }
        .entry-head::after { content: ""; display: table; clear: both; }
        .entry-title { font-weight: bold; font-size: 9.5pt; margin-bottom: 2px; }
        .entry-dates { float: right; font-size: 8pt; color: #64748b; }
        .entry-company { font-style: italic; font-size: 8.5pt; color: #475569; margin: 2px 0 6px; }
        .bullets { margin: 0; padding-left: 14px; font-size: 8.5pt; }
        .bullets li { margin-bottom: 2px; }
        .edu-row { font-size: 8.5pt; margin: 0 0 3px; }
        .cert-row { font-size: 8.5pt; margin: 0 0 2px; }
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
<div class="cv-document">
    <table class="cv-columns" cellpadding="0" cellspacing="0">
    <tr>
    <td class="sidebar">
        <div class="sidebar-inner">
        <div class="photo-wrap">
            @if (! empty($photoSrc))
                <img src="{{ $photoSrc }}" alt="" class="photo">
            @else
                <div class="photo" style="background:#334155;line-height:88px;font-size:24pt;color:#93c5fd;text-align:center;">
                    {{ mb_substr((string) ($d['full_name'] ?? '?'), 0, 1) }}
                </div>
            @endif
        </div>
        @if ($has($d['full_name'] ?? ''))
            <p class="sidebar-name">{{ $d['full_name'] }}</p>
        @endif
        @if ($has($d['headline'] ?? ''))
            <p class="sidebar-headline">{{ $d['headline'] }}</p>
        @endif

        <hr class="sidebar-divider">

        <div class="sidebar-block">
            <p class="sidebar-title">{{ $t('contact') }}</p>
            @if ($has($d['email'] ?? ''))<p class="sidebar-text">{{ $d['email'] }}</p>@endif
            @if ($has($d['phone'] ?? ''))<p class="sidebar-text">{{ $d['phone'] }}</p>@endif
            @if ($has($d['city'] ?? ''))<p class="sidebar-text">{{ $d['city'] }}</p>@endif
            @if ($socialLinks->isNotEmpty())
                <p class="sidebar-text social-links">
                    @foreach ($socialLinks as $type => $url)
                        <a
                            href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}"
                            class="social-link"
                            title="{{ $url }}"
                        >
                            <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type) }}" alt="{{ $type }}">
                        </a>
                    @endforeach
                </p>
            @endif
        </div>

        @if ($skillGroups->isNotEmpty())
            <hr class="sidebar-divider">
            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('skills') }}</p>
                @foreach ($skillGroups as $group)
                    <p class="sidebar-text" style="margin-bottom:4px;">
                        @if ($has($group['label'] ?? ''))<span class="skill-label">{{ $group['label'] }}</span><br>@endif
                        @if ($has($group['items'] ?? ''))<span class="skill-items">{{ $group['items'] }}</span>@endif
                    </p>
                @endforeach
            </div>
        @endif

        @if ($languages->isNotEmpty())
            <hr class="sidebar-divider">
            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('languages') }}</p>
                @foreach ($languages as $lang)
                    <p class="sidebar-text">{{ $lang['name'] }}@if ($has($lang['level'] ?? '')) · {{ $lang['level'] }}@endif</p>
                @endforeach
            </div>
        @endif

        @if ($has($d['availability_line'] ?? ''))
            <hr class="sidebar-divider">
            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('availability') }}</p>
                <p class="sidebar-text">{{ $d['availability_line'] }}</p>
            </div>
        @endif
        </div>
    </td>
    <td class="main">
        <div class="main-inner">
        @if ($has($d['summary'] ?? ''))
            <div class="section">
                <p class="section-title">{{ $t('summary') }}</p>
                <p class="summary">{{ $d['summary'] }}</p>
            </div>
        @endif

        @if ($experiences->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('experience') }}</p>
                @foreach ($experiences as $exp)
                    <div class="entry">
                        <div class="entry-head">
                            <span class="entry-title">{{ $exp['title'] }}</span>
                            <span class="entry-dates">
                                {{ $exp['start'] }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                            </span>
                        </div>
                        <p class="entry-company">{{ $exp['company'] }}@if ($has($exp['location'] ?? '')) · {{ $exp['location'] }}@endif</p>
                        <ul class="bullets">
                            @foreach (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($education->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('education') }}</p>
                @foreach ($education as $edu)
                    <p class="edu-row"><strong>{{ $edu['degree'] }}</strong> — {{ $edu['school'] }}@if ($has($edu['year'] ?? '')) ({{ $edu['year'] }})@endif</p>
                @endforeach
            </div>
        @endif

        @if ($certs->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('certifications') }}</p>
                @foreach ($certs as $cert)
                    <p class="cert-row">{{ $cert }}</p>
                @endforeach
            </div>
        @endif
        </div>
    </td>
    </tr>
    </table>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
