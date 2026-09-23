<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="artist">
    @php
        // Page width 794px, sidebar 34%, horizontal padding 14px × 2 → 95% of usable column width
        $photoSide = (int) round((794 * 0.34 - 28) * 0.95);
        $photoLineHeight = max(1, $photoSide - 8);
    @endphp
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.8pt;
            color: #2f3640;
            margin: 0;
            line-height: 1.38;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @include('talent.cv-builder.templates.partials.cv-layout-shell', [
            'sidebarSide' => 'left',
            'sidebarWidth' => '34%',
            'mainWidth' => '66%',
            'sidebarBg' => '#333333',
        ])
        td.sidebar {
            color: #ffffff;
            padding: 18px 14px 20px;
        }
        td.main {
            padding: 0;
            background: #ffffff;
        }

        .sidebar-inner,
        .main-inner {
            width: 100%;
            max-width: 100%;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .photo-wrap { text-align: center; margin: 4px auto 27px; width: 95%; }
        .photo {
            width: {{ $photoSide }}px;
            height: {{ $photoSide }}px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            display: block;
            margin: 0 auto;
            background: #ffffff;
        }
        .photo-placeholder {
            width: {{ $photoSide }}px;
            height: {{ $photoSide }}px;
            border-radius: 50%;
            background: #4b5563;
            border: 4px solid #ffffff;
            color: #f9fafb;
            font-size: 42pt;
            font-weight: bold;
            text-align: center;
            line-height: {{ $photoLineHeight }}px;
            margin: 0 auto;
        }

        .sidebar-block { margin: 0 0 24px; }
        .sidebar-title {
            margin: 0 0 8px;
            padding: 0 0 5px;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #ffffff;
            border-bottom: 2px solid #f5c518;
        }
        .sidebar-text {
            margin: 0 0 4px;
            font-size: 8pt;
            color: #f3f4f6;
            line-height: 1.4;
        }
        .edu-degree {
            margin: 0 0 2px;
            font-size: 8.4pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
        }
        .edu-meta {
            margin: 0 0 8px;
            font-size: 7.6pt;
            color: #d1d5db;
        }

        .skill-row { margin: 0 0 9px; }
        .skill-name {
            margin: 0 0 3px;
            font-size: 7.6pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
        }
        .skill-track {
            width: 100%;
            height: 5px;
            background: #ffffff;
            border-collapse: collapse;
        }
        .skill-fill {
            height: 5px;
            background: #f5c518;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .main-top-accent {
            height: 18px;
            background: #f5c518;
            width: 100%;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .main-hero {
            padding: 0 18px;
            background: #ffffff;
            height: 140px;
        }
        .main-hero-table {
            width: 100%;
            height: 140px;
            border-collapse: collapse;
        }
        .main-hero-table td {
            vertical-align: middle;
            height: 140px;
        }
        .hero-name {
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            color: #2f3640;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }
        .hero-headline {
            margin: 6px 0 0;
            font-size: 9.5pt;
            font-weight: bold;
            color: #2f3640;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .contact-bar {
            background: #f5c518;
            padding: 10px 18px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .contact-table { width: 100%; border-collapse: collapse; }
        .contact-table td {
            vertical-align: middle;
            font-size: 7.6pt;
            font-weight: bold;
            color: #2f3640;
            padding: 3px 0;
        }
        .social-links { margin-top: 5px; line-height: 1.35; }
        .social-links--stack .social-link { display: block; margin: 5px 0 0; color: #000000; text-decoration: none; white-space: nowrap; }
        .social-links--stack .social-link:first-child { margin-top: 0; }

        .main-body { padding: 14px 18px 18px; }
        .section { margin: 0 0 13px; page-break-inside: avoid; }
        .section-title {
            margin: 0 0 8px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #2f3640;
        }
        .entry { margin: 0 0 10px; }
        .entry-title {
            margin: 0 0 2px;
            font-size: 9pt;
            font-weight: bold;
            color: #2f3640;
            text-transform: uppercase;
        }
        .entry-dates {
            margin: 0 0 5px;
            font-size: 7.8pt;
            color: #6b7280;
        }
        .entry-body {
            margin: 0;
            font-size: 8.2pt;
            color: #4b5563;
            text-align: justify;
            line-height: 1.45;
        }
        .bullets { margin: 0; padding-left: 14px; font-size: 8.2pt; color: #4b5563; }
        .bullets li { margin-bottom: 2px; }
        .cert-row { margin: 0 0 3px; font-size: 8.2pt; color: #374151; }
        .lang-row { margin: 0 0 3px; font-size: 8.2pt; color: #374151; }
        .summary {
            margin: 0;
            font-size: 8.2pt;
            color: #4b5563;
            text-align: justify;
            line-height: 1.45;
        }
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
    $interests = collect($d['interests'] ?? [])->filter(fn ($c) => $has($c));
    $socialLinks = collect([
        'linkedin' => (string) ($d['linkedin_url'] ?? ''),
        'github' => (string) ($d['github_url'] ?? ''),
        'portfolio' => (string) ($d['portfolio_url'] ?? ''),
    ])->filter(fn (string $url) => $has($url));

    $skillBars = collect();
    foreach ($skillGroups as $group) {
        if ($has($group['label'] ?? '')) {
            $skillBars->push((string) $group['label']);
        } elseif ($has($group['items'] ?? '')) {
            foreach (preg_split('/\s*,\s*/u', (string) $group['items']) as $item) {
                if ($has($item)) {
                    $skillBars->push($item);
                }
            }
        }
    }
    $skillBars = $skillBars->unique()->take(8)->values();
    $barPercents = [95, 88, 80, 72, 85, 78, 90, 70];
@endphp
<div class="cv-document">
    <table class="cv-columns" cellpadding="0" cellspacing="0">
    <tr>
        <td class="sidebar">
            <div class="sidebar-inner">
                <div class="photo-wrap">
                    @if (! empty($photoSrc))
                        <img src="{{ $photoSrc }}" alt="" class="photo" width="{{ $photoSide }}" height="{{ $photoSide }}">
                    @else
                        <div class="photo-placeholder">{{ mb_substr((string) ($d['full_name'] ?? '?'), 0, 1) }}</div>
                    @endif
                </div>

                @if ($education->isNotEmpty())
                    <div class="sidebar-block">
                        <p class="sidebar-title">{{ $t('education') }}</p>
                        @foreach ($education as $edu)
                            @if ($has($edu['degree'] ?? ''))
                                <p class="edu-degree">{{ $edu['degree'] }}</p>
                            @endif
                            <p class="edu-meta">
                                @if ($has($edu['school'] ?? '')){{ $edu['school'] }}@endif
                                @if ($has($edu['year'] ?? ''))
                                    @if ($has($edu['school'] ?? '')) | @endif{{ $edu['year'] }}
                                @endif
                            </p>
                        @endforeach
                    </div>
                @endif

                @if ($skillBars->isNotEmpty())
                    <div class="sidebar-block">
                        <p class="sidebar-title">{{ $t('skills') }}</p>
                        @foreach ($skillBars as $index => $skillName)
                            @php($percent = $barPercents[$index % count($barPercents)])
                            <div class="skill-row">
                                <p class="skill-name">{{ $skillName }}</p>
                                <table class="skill-track" cellpadding="0" cellspacing="0"><tr>
                                    <td class="skill-fill" style="width: {{ $percent }}%;"></td>
                                    <td style="width: {{ 100 - $percent }}%;"></td>
                                </tr></table>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($has($d['availability_line'] ?? ''))
                    <div class="sidebar-block">
                        <p class="sidebar-title">{{ $t('availability') }}</p>
                        <p class="sidebar-text">{{ $d['availability_line'] }}</p>
                    </div>
                @endif

                @if ($interests->isNotEmpty())
                    <div class="sidebar-block">
                        <p class="sidebar-title">{{ $t('interests') }}</p>
                        @foreach ($interests as $interest)
                            <p class="sidebar-text">{{ $interest }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </td>
        <td class="main">
            <div class="main-top-accent"></div>
            <div class="main-hero">
                <table class="main-hero-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            @if ($has($d['full_name'] ?? ''))
                                <p class="hero-name">{{ mb_strtoupper($d['full_name']) }}</p>
                            @endif
                            @if ($has($d['headline'] ?? ''))
                                <p class="hero-headline">{{ mb_strtoupper($d['headline']) }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @if ($has($d['phone'] ?? '') || $has($d['email'] ?? '') || $has($d['city'] ?? '') || $socialLinks->isNotEmpty())
                <div class="contact-bar">
                    <table class="contact-table" cellpadding="0" cellspacing="0">
                        @if ($has($d['phone'] ?? ''))
                            <tr><td>{{ $d['phone'] }}</td></tr>
                        @endif
                        @if ($has($d['email'] ?? ''))
                            <tr><td>{{ $d['email'] }}</td></tr>
                        @endif
                        @if ($has($d['city'] ?? ''))
                            <tr><td>{{ $d['city'] }}</td></tr>
                        @endif
                        @if ($socialLinks->isNotEmpty())
                            <tr class="contact-social-row">
                                <td>
                                    <div class="social-links social-links--stack">
                                        @foreach ($socialLinks as $type => $url)
                                            @include('talent.cv-builder.templates.partials.cv-social-link', ['type' => $type, 'url' => $url])
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endif

            <div class="main-inner main-body">
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
                                <p class="entry-title">
                                    @if ($has($exp['company'] ?? '') && $has($exp['title'] ?? ''))
                                        {{ $exp['company'] }} / {{ $exp['title'] }}
                                    @elseif ($has($exp['title'] ?? ''))
                                        {{ $exp['title'] }}
                                    @else
                                        {{ $exp['company'] }}
                                    @endif
                                </p>
                                <p class="entry-dates">
                                    {{ $exp['start'] }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                                    @if ($has($exp['location'] ?? '')) · {{ $exp['location'] }}@endif
                                </p>
                                @php($bullets = array_values(array_filter($exp['bullets'] ?? [], fn ($b) => $has($b))))
                                @if ($bullets !== [])
                                    <ul class="bullets">
                                        @foreach ($bullets as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
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

                @if ($languages->isNotEmpty())
                    <div class="section">
                        <p class="section-title">{{ $t('languages') }}</p>
                        @foreach ($languages as $lang)
                            <p class="lang-row">{{ $lang['name'] }}@if ($has($lang['level'] ?? '')) · {{ $lang['level'] }}@endif</p>
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
