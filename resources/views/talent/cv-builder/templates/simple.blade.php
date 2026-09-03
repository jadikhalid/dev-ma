<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <style>
        /* Page 2+ : air en haut/bas. Page 1 : plein écran pour le bandeau teal. */
        @page {
            size: A4 portrait;
            margin: 12mm 0;
        }
        @page :first {
            margin: 0;
        }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            color: #1f2937;
            margin: 0;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            background: #0f766e;
            color: #ffffff;
            padding: 22px 28px 18px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-text { vertical-align: middle; }
        .header-photo { width: 92px; vertical-align: middle; text-align: right; }

        .name {
            margin: 0;
            font-size: 22pt;
            font-weight: bold;
            line-height: 1.1;
            letter-spacing: 0.01em;
        }
        .headline {
            margin: 8px 0 0;
            font-size: 10.5pt;
            font-weight: normal;
            opacity: 0.95;
        }

        .photo {
            width: 78px;
            height: 78px;
            border: 3px solid #ffffff;
            display: block;
            margin-left: auto;
        }
        .photo-placeholder {
            width: 78px;
            height: 78px;
            border: 3px solid #ffffff;
            background: #115e59;
            color: #ffffff;
            font-size: 22pt;
            font-weight: bold;
            text-align: center;
            line-height: 72px;
            margin-left: auto;
        }

        .contact-bar {
            background: #f0fdfa;
            border-bottom: 1px solid #99f6e4;
            padding: 8px 28px;
            font-size: 8pt;
            color: #134e4a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .contact-bar-info { flex: 1 1 auto; min-width: 0; }
        .contact-bar-social { flex: 0 0 auto; white-space: nowrap; }
        .contact-bar span { white-space: nowrap; }
        .contact-sep { color: #5eead4; padding: 0 7px; }
        .contact-bar a { color: #134e4a; text-decoration: none; }

        .body { padding: 16px 28px 22px; }

        .section { margin: 0 0 14px; }
        .section-title {
            margin: 0 0 7px;
            padding: 0 0 4px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #0f766e;
            border-bottom: 2px solid #0f766e;
        }

        .summary {
            margin: 0;
            text-align: justify;
            font-size: 9.5pt;
            color: #374151;
        }

        .entry { margin: 0 0 10px; page-break-inside: avoid; }
        .entry-head { width: 100%; overflow: hidden; }
        .entry-head::after { content: ""; display: block; clear: both; }
        .entry-title { font-weight: bold; font-size: 10pt; color: #111827; }
        .entry-dates { float: right; font-size: 8.5pt; color: #6b7280; }
        .entry-meta { margin: 2px 0 4px; font-size: 8.5pt; color: #4b5563; font-style: italic; }
        .bullets { margin: 0; padding-left: 15px; font-size: 9pt; color: #374151; }
        .bullets li { margin-bottom: 2px; }

        .edu-row { margin: 0 0 5px; font-size: 9pt; color: #374151; page-break-inside: avoid; }
        .cert-row { margin: 0 0 3px; font-size: 9pt; color: #374151; }
        .skill-row { margin: 0 0 4px; font-size: 9pt; color: #374151; }
        .skill-label { font-weight: bold; color: #0f766e; }
        .lang-row { margin: 0 0 3px; font-size: 9pt; color: #374151; }

        .social-links { line-height: 1; }
        .social-link { display: inline-block; margin-left: 12px; vertical-align: middle; }
        .social-link:first-child { margin-left: 0; }
        .social-link img { width: 12px; height: 12px; display: block; border: 0; }
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
    $contactParts = collect([
        $has($d['email'] ?? '') ? (string) $d['email'] : null,
        $has($d['phone'] ?? '') ? (string) $d['phone'] : null,
        $has($d['city'] ?? '') ? (string) $d['city'] : null,
    ])->filter()->values();
@endphp

<div class="header">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-text">
                @if ($has($d['full_name'] ?? ''))
                    <p class="name">{{ $d['full_name'] }}</p>
                @endif
                @if ($has($d['headline'] ?? ''))
                    <p class="headline">{{ $d['headline'] }}</p>
                @endif
            </td>
            <td class="header-photo">
                @if (! empty($photoSrc))
                    <img src="{{ $photoSrc }}" alt="" class="photo" width="78" height="78">
                @else
                    <div class="photo-placeholder">{{ mb_substr((string) ($d['full_name'] ?? '?'), 0, 1) }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

@if ($contactParts->isNotEmpty() || $socialLinks->isNotEmpty())
    <div class="contact-bar">
        <div class="contact-bar-info">
            @foreach ($contactParts as $i => $part)
                @if ($i > 0)<span class="contact-sep">·</span>@endif
                <span>{{ $part }}</span>
            @endforeach
        </div>
        @if ($socialLinks->isNotEmpty())
            <div class="contact-bar-social">
                <span class="social-links">
                    @foreach ($socialLinks as $type => $url)
                        <a href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}" class="social-link" title="{{ $url }}">
                            <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type, '#0f766e') }}" alt="{{ $type }}">
                        </a>
                    @endforeach
                </span>
            </div>
        @endif
    </div>
@endif

<div class="body">
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
                        <span class="entry-title">{{ $exp['title'] ?? '' }}</span>
                        <span class="entry-dates">
                            {{ $exp['start'] ?? '' }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                        </span>
                    </div>
                    @if ($has($exp['company'] ?? '') || $has($exp['location'] ?? ''))
                        <p class="entry-meta">{{ $exp['company'] ?? '' }}@if ($has($exp['company'] ?? '') && $has($exp['location'] ?? '')) · @endif{{ $exp['location'] ?? '' }}</p>
                    @endif
                    @if (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) !== [])
                        <ul class="bullets">
                            @foreach (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) as $bullet)
                                <li>{{ $bullet }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($education->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('education') }}</p>
            @foreach ($education as $edu)
                <p class="edu-row">
                    <strong>{{ $edu['degree'] ?? '' }}</strong>
                    @if ($has($edu['school'] ?? '')) — {{ $edu['school'] }}@endif
                    @if ($has($edu['year'] ?? '')) ({{ $edu['year'] }})@endif
                </p>
            @endforeach
        </div>
    @endif

    @if ($skillGroups->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('skills') }}</p>
            @foreach ($skillGroups as $group)
                <p class="skill-row">
                    @if ($has($group['label'] ?? ''))<span class="skill-label">{{ $group['label'] }} :</span> @endif
                    {{ $group['items'] ?? '' }}
                </p>
            @endforeach
        </div>
    @endif

    @if ($languages->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('languages') }}</p>
            @foreach ($languages as $lang)
                <p class="lang-row">{{ $lang['name'] }}@if ($has($lang['level'] ?? '')) — {{ $lang['level'] }}@endif</p>
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

    @if ($has($d['availability_line'] ?? ''))
        <div class="section">
            <p class="section-title">{{ $t('availability') }}</p>
            <p class="summary">{{ $d['availability_line'] }}</p>
        </div>
    @endif
</div>
</body>
</html>
