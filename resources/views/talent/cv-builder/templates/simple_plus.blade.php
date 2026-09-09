<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="simple_plus">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #334155;
            margin: 0;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cv-document { width: 100%; padding: 18px 20px 16px; }
        .serif { font-family: DejaVu Serif, DejaVu Sans, serif; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .header-left { width: 58%; vertical-align: top; }
        .header-right { width: 42%; vertical-align: top; text-align: right; }

        .name-first {
            margin: 0;
            font-size: 22pt;
            font-weight: bold;
            color: #1e293b;
            line-height: 1.05;
            letter-spacing: -0.01em;
        }
        .name-last {
            margin: 0;
            font-size: 22pt;
            font-weight: bold;
            color: #0f766e;
            line-height: 1.05;
            letter-spacing: -0.01em;
        }
        .headline {
            margin: 8px 0 0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .contact-line {
            margin: 0 0 4px;
            font-size: 7.6pt;
            color: #64748b;
            line-height: 1.35;
        }
        .contact-line a { color: #64748b; text-decoration: none; }
        .social-links { margin-top: 6px; line-height: 1; }
        @media print {
            .social-links { margin-top: 12px; margin-bottom: 8px; }
        }
        .social-link { display: inline-block; margin-left: 10px; vertical-align: middle; }
        .social-link:first-child { margin-left: 0; }
        .social-link img { width: 12px; height: 12px; display: block; border: 0; }

        .header-rule {
            border: none;
            border-top: 1px solid #cbd5e1;
            margin: 0 0 14px;
            height: 0;
        }

        table.body-columns {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td.col-left {
            width: 34%;
            vertical-align: top;
            padding: 0 14px 0 0;
        }
        td.col-right {
            width: 66%;
            vertical-align: top;
            padding: 0 0 0 6px;
        }

        .side-section { margin-bottom: 14px; page-break-inside: avoid; }
        .side-title {
            margin: 0 0 8px;
            font-size: 7.6pt;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .competency-row {
            margin: 0;
            padding: 6px 0;
            font-family: DejaVu Serif, DejaVu Sans, serif;
            font-size: 8.4pt;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
        }
        .competency-row:last-child { border-bottom: none; }

        .tools-wrap { margin: 0; line-height: 1.8; }
        .tool-pill {
            display: inline-block;
            background: #e8eef3;
            color: #1e293b;
            font-size: 7.2pt;
            font-weight: bold;
            padding: 3px 8px;
            margin: 0 5px 5px 0;
            border-radius: 3px;
            line-height: 1.2;
        }

        .edu-degree {
            margin: 0 0 2px;
            font-family: DejaVu Serif, DejaVu Sans, serif;
            font-size: 8.6pt;
            font-weight: bold;
            color: #1e293b;
        }
        .edu-meta {
            margin: 0 0 8px;
            font-size: 7.5pt;
            color: #64748b;
        }
        .cert-title {
            margin: 0 0 2px;
            font-family: DejaVu Serif, DejaVu Sans, serif;
            font-size: 8.2pt;
            font-style: italic;
            color: #1e293b;
        }
        .lang-row {
            margin: 0 0 3px;
            font-size: 7.8pt;
            color: #475569;
        }

        .profile-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #f0f9f8;
        }
        td.profile-accent {
            width: 5px;
            background: #0f766e;
            vertical-align: top;
        }
        td.profile-body {
            vertical-align: top;
            padding: 10px 12px 11px;
            background: #f0f9f8;
        }
        .profile-title {
            margin: 0 0 6px;
            font-size: 7.8pt;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .profile-text {
            margin: 0;
            font-size: 8.5pt;
            color: #334155;
            text-align: justify;
            line-height: 1.45;
        }

        .section { margin-bottom: 12px; page-break-inside: avoid; }
        .section-title {
            margin: 0 0 10px;
            padding-bottom: 5px;
            font-size: 7.8pt;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 1px solid #e2e8f0;
        }

        .entry { margin-bottom: 11px; }
        .entry-head { width: 100%; border-collapse: collapse; }
        .entry-title {
            font-family: DejaVu Serif, DejaVu Sans, serif;
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
            line-height: 1.15;
        }
        .entry-dates {
            font-size: 7.6pt;
            color: #94a3b8;
            white-space: nowrap;
            text-align: right;
            vertical-align: top;
            padding-left: 8px;
        }
        .entry-company {
            margin: 3px 0 5px;
            font-size: 8.4pt;
            font-weight: bold;
            color: #0f766e;
        }
        .bullets {
            margin: 0;
            padding-left: 14px;
            font-size: 8.2pt;
            color: #475569;
        }
        .bullets li { margin-bottom: 3px; }
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

    $fullName = trim((string) ($d['full_name'] ?? ''));
    $nameParts = preg_split('/\s+/u', $fullName, 2) ?: [];
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';

    $competencies = $skillGroups
        ->map(fn ($g) => trim((string) ($g['label'] ?? '')))
        ->filter(fn ($label) => $label !== '')
        ->values();

    $tools = collect();
    foreach ($skillGroups as $group) {
        foreach (preg_split('/\s*[,;|]\s*/u', (string) ($group['items'] ?? '')) ?: [] as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $tools->push($item);
            }
        }
    }
    $tools = $tools->unique()->values()->take(12);

    if ($competencies->isEmpty() && $tools->isNotEmpty()) {
        $competencies = $tools->take(5)->values();
        $tools = $tools->slice(5)->values();
    }
@endphp
<div class="cv-document">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-left">
                @if ($fullName !== '')
                    <p class="name-first serif">{{ $firstName }}</p>
                    @if ($lastName !== '')
                        <p class="name-last serif">{{ $lastName }}</p>
                    @endif
                @endif
                @if ($has($d['headline'] ?? ''))
                    <p class="headline">{{ $d['headline'] }}</p>
                @endif
            </td>
            <td class="header-right">
                @if ($has($d['email'] ?? ''))<p class="contact-line">{{ $d['email'] }}</p>@endif
                @if ($has($d['phone'] ?? ''))<p class="contact-line">{{ $d['phone'] }}</p>@endif
                @if ($has($d['city'] ?? ''))<p class="contact-line">{{ $d['city'] }}</p>@endif
                @if ($socialLinks->isNotEmpty())
                    <p class="contact-line social-links">
                        @foreach ($socialLinks as $type => $url)
                            <a href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}" class="social-link" title="{{ $url }}">
                                <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type, '#64748b') }}" alt="{{ $type }}">
                            </a>
                        @endforeach
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <hr class="header-rule">

    <table class="body-columns" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col-left">
                @if ($competencies->isNotEmpty())
                    <div class="side-section">
                        <p class="side-title">{{ $t('skills') }}</p>
                        @foreach ($competencies as $skill)
                            <p class="competency-row">{{ $skill }}</p>
                        @endforeach
                    </div>
                @endif

                @if ($tools->isNotEmpty())
                    <div class="side-section">
                        <p class="side-title">{{ $t('tools') }}</p>
                        <div class="tools-wrap">
                            @foreach ($tools as $tool)
                                <span class="tool-pill">{{ $tool }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($education->isNotEmpty())
                    <div class="side-section">
                        <p class="side-title">{{ $t('education') }}</p>
                        @foreach ($education as $edu)
                            @if ($has($edu['degree'] ?? ''))
                                <p class="edu-degree">{{ $edu['degree'] }}</p>
                            @endif
                            <p class="edu-meta">
                                {{ $edu['school'] ?? '' }}@if ($has($edu['year'] ?? '')) · {{ $edu['year'] }}@endif
                            </p>
                        @endforeach
                    </div>
                @endif

                @if ($certs->isNotEmpty())
                    <div class="side-section">
                        <p class="side-title">{{ $t('certifications') }}</p>
                        @foreach ($certs as $cert)
                            <p class="cert-title">« {{ $cert }} »</p>
                        @endforeach
                    </div>
                @endif

                @if ($languages->isNotEmpty())
                    <div class="side-section">
                        <p class="side-title">{{ $t('languages') }}</p>
                        @foreach ($languages as $lang)
                            <p class="lang-row">
                                {{ $lang['name'] }}@if ($has($lang['level'] ?? '')) · {{ $lang['level'] }}@endif
                            </p>
                        @endforeach
                    </div>
                @endif

                @if ($has($d['availability_line'] ?? ''))
                    <div class="side-section">
                        <p class="side-title">{{ $t('availability') }}</p>
                        <p class="lang-row">{{ $d['availability_line'] }}</p>
                    </div>
                @endif
            </td>

            <td class="col-right">
                @if ($has($d['summary'] ?? ''))
                    <table class="profile-box" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="profile-accent"></td>
                            <td class="profile-body">
                                <p class="profile-title">{{ $t('summary') }}</p>
                                <p class="profile-text">{{ $d['summary'] }}</p>
                            </td>
                        </tr>
                    </table>
                @endif

                @if ($experiences->isNotEmpty())
                    <div class="section">
                        <p class="section-title">{{ $t('experience') }}</p>
                        @foreach ($experiences as $exp)
                            <div class="entry">
                                <table class="entry-head" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            @if ($has($exp['title'] ?? ''))
                                                <p class="entry-title">{{ $exp['title'] }}</p>
                                            @endif
                                        </td>
                                        <td class="entry-dates">
                                            {{ $exp['start'] ?? '' }}@if ($exp['current'] ?? false) — {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) — {{ $exp['end'] }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @if ($has($exp['company'] ?? '') || $has($exp['location'] ?? ''))
                                    <p class="entry-company">
                                        {{ $exp['company'] ?? '' }}@if ($has($exp['location'] ?? '')) ({{ $exp['location'] }})@endif
                                    </p>
                                @endif
                                <ul class="bullets">
                                    @foreach (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
    </table>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
