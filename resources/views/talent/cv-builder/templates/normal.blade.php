<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="normal">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #111111;
            margin: 0;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cv-document { width: 100%; }
        .header { padding: 22px 28px 10px; }
        .body { padding: 4px 28px 22px; }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-main { vertical-align: top; padding-right: 16px; }
        .header-photo { width: 108px; vertical-align: top; text-align: right; }

        .name {
            margin: 0;
            font-size: 22pt;
            font-weight: bold;
            color: #111111;
            line-height: 1.15;
        }
        .headline {
            margin: 6px 0 10px;
            font-size: 11pt;
            font-weight: bold;
            color: #111111;
            line-height: 1.25;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .contact-line {
            margin: 0 0 2px;
            font-size: 8.5pt;
            color: #222222;
            line-height: 1.35;
        }

        .photo {
            width: 100px;
            height: 120px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            border: 0;
        }
        .photo-placeholder {
            width: 100px;
            height: 120px;
            background: #e5e7eb;
            color: #374151;
            font-size: 28pt;
            font-weight: bold;
            text-align: center;
            line-height: 120px;
            margin-left: auto;
        }

        .section { margin: 0 0 12px; }
        .section-title {
            margin: 0 0 8px;
            padding: 0 0 4px;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #111111;
            border-bottom: 1.5px solid #111111;
        }

        .summary {
            margin: 0;
            font-size: 9pt;
            color: #222222;
            text-align: justify;
            line-height: 1.45;
        }

        table.timeline {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 0 8px;
        }
        table.timeline td {
            vertical-align: top;
            padding: 0 0 8px;
        }
        td.tl-left {
            width: 26%;
            padding-right: 12px;
            font-size: 8.5pt;
            color: #222222;
            line-height: 1.35;
        }
        td.tl-right {
            width: 74%;
            font-size: 9pt;
            color: #222222;
        }
        .tl-title {
            margin: 0 0 3px;
            font-size: 9.5pt;
            font-weight: bold;
            color: #111111;
            line-height: 1.3;
        }
        .tl-sub {
            margin: 0;
            font-size: 9pt;
            font-weight: normal;
            color: #222222;
            line-height: 1.35;
        }
        .tl-body {
            margin: 4px 0 0;
            font-size: 8.5pt;
            color: #333333;
            line-height: 1.4;
            text-align: justify;
        }
        .bullets {
            margin: 4px 0 0;
            padding-left: 14px;
            font-size: 8.5pt;
            color: #333333;
        }
        .bullets li { margin-bottom: 2px; }

        .entry { margin: 0 0 8px; page-break-inside: avoid; }
        .edu-row { margin: 0 0 8px; page-break-inside: avoid; }
        .lang-row { margin: 0 0 3px; font-size: 9pt; color: #222222; }
        .cert-row { margin: 0 0 3px; font-size: 9pt; color: #222222; }
        .social-row { margin: 5px 0 0; font-size: 9pt; color: #000000; line-height: 1.35; }
        .social-row:first-child { margin-top: 0; }
        .social-row .social-link { color: #000000; text-decoration: none; }

        table.skills-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td.skill-cell {
            width: 33.33%;
            vertical-align: top;
            padding: 0 10px 8px 0;
            font-size: 8.5pt;
            color: #222222;
        }
        td.skill-cell:nth-child(3n) { padding-right: 0; }
        .skill-row { page-break-inside: avoid; }
        .skill-name {
            display: inline-block;
            margin-right: 6px;
            vertical-align: middle;
        }
        .skill-dots {
            display: inline-block;
            vertical-align: middle;
            white-space: nowrap;
            line-height: 1;
        }
        .dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            margin-right: 3px;
            border: 1.2px solid #111111;
            border-radius: 50%;
            background: transparent;
            vertical-align: middle;
        }
        .dot.filled {
            background: #111111;
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

    $skills = collect();
    foreach ($skillGroups as $group) {
        foreach (preg_split('/\s*[,;|]\s*/u', (string) ($group['items'] ?? '')) ?: [] as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $skills->push($item);
            }
        }
    }
    if ($skills->isEmpty()) {
        $skills = $skillGroups
            ->map(fn ($g) => trim((string) ($g['label'] ?? '')))
            ->filter(fn ($label) => $label !== '')
            ->values();
    }
    $skills = $skills->unique()->values()->take(12);
    $skillRows = $skills->chunk(3);

    $formatDates = function (array $exp) use ($t, $has): string {
        $start = trim((string) ($exp['start'] ?? ''));
        $end = ($exp['current'] ?? false)
            ? $t('present')
            : trim((string) ($exp['end'] ?? ''));

        if ($start === '' && $end === '') {
            return '';
        }
        if ($start !== '' && $end !== '') {
            return $start.' - '.$end;
        }

        return $start !== '' ? $start : $end;
    };
@endphp
<div class="cv-document">
    <div class="header">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-main">
                    @if ($has($d['full_name'] ?? ''))
                        <p class="name">{{ $d['full_name'] }}</p>
                    @endif
                    @if ($has($d['headline'] ?? ''))
                        <p class="headline">{{ $d['headline'] }}</p>
                    @endif
                    @if ($has($d['email'] ?? ''))
                        <p class="contact-line">{{ $d['email'] }}</p>
                    @endif
                    @if ($has($d['phone'] ?? ''))
                        <p class="contact-line">{{ $d['phone'] }}</p>
                    @endif
                    @if ($has($d['city'] ?? ''))
                        <p class="contact-line">{{ $d['city'] }}</p>
                    @endif
                </td>
                <td class="header-photo">
                    @if (! empty($photoSrc))
                        <img src="{{ $photoSrc }}" alt="" class="photo" width="100" height="120">
                    @else
                        <div class="photo-placeholder">{{ mb_substr((string) ($d['full_name'] ?? '?'), 0, 1) }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="body">
        @if ($has($d['summary'] ?? ''))
            <div class="section">
                <p class="section-title">{{ $t('profile_professional') }}</p>
                <p class="summary">{{ $d['summary'] }}</p>
            </div>
        @endif

        @if ($education->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('education') }}</p>
                @foreach ($education as $edu)
                    <table class="timeline edu-row" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="tl-left">
                                @if ($has($edu['year'] ?? '')){{ $edu['year'] }}@endif
                            </td>
                            <td class="tl-right">
                                @if ($has($edu['degree'] ?? ''))
                                    <p class="tl-title">{{ $edu['degree'] }}</p>
                                @endif
                                @if ($has($edu['school'] ?? ''))
                                    <p class="tl-sub">{{ $edu['school'] }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif

        @if ($experiences->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('experience') }}</p>
                @foreach ($experiences as $exp)
                    @php
                        $dates = $formatDates($exp);
                        $titleBits = collect([
                            trim((string) ($exp['title'] ?? '')),
                            trim((string) ($exp['company'] ?? '')),
                        ])->filter(fn ($v) => $v !== '')->values();
                        $bullets = array_values(array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)));
                    @endphp
                    <table class="timeline entry" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="tl-left">
                                @if ($dates !== ''){{ $dates }}@endif
                                @if ($has($exp['location'] ?? ''))
                                    @if ($dates !== '')<br>@endif
                                    {{ $exp['location'] }}
                                @endif
                            </td>
                            <td class="tl-right">
                                @if ($titleBits->isNotEmpty())
                                    <p class="tl-title">{{ $titleBits->implode(' - ') }}</p>
                                @endif
                                @if ($bullets !== [])
                                    <ul class="bullets">
                                        @foreach ($bullets as $bullet)
                                            <li>{{ $bullet }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif

        @if ($languages->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('languages') }}</p>
                @foreach ($languages as $lang)
                    <p class="lang-row">
                        {{ $lang['name'] }}@if ($has($lang['level'] ?? '')) : {{ $lang['level'] }}@endif
                    </p>
                @endforeach
            </div>
        @endif

        @if ($skills->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('skills') }}</p>
                <table class="skills-grid" cellpadding="0" cellspacing="0">
                    @foreach ($skillRows as $row)
                        <tr class="skill-row">
                            @foreach ($row as $skill)
                                <td class="skill-cell">
                                    <span class="skill-name">{{ $skill }}</span>
                                    <span class="skill-dots" aria-hidden="true">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="dot {{ $i <= 4 ? 'filled' : '' }}"></span>
                                        @endfor
                                    </span>
                                </td>
                            @endforeach
                            @for ($pad = $row->count(); $pad < 3; $pad++)
                                <td class="skill-cell"></td>
                            @endfor
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if ($certs->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('courses_certifications') }}</p>
                @foreach ($certs as $cert)
                    <p class="cert-row">{{ $cert }}</p>
                @endforeach
            </div>
        @endif

        @if ($interests->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('interests') }}</p>
                @foreach ($interests as $interest)
                    <p class="cert-row">{{ $interest }}</p>
                @endforeach
            </div>
        @endif

        @if ($has($d['availability_line'] ?? ''))
            <div class="section">
                <p class="section-title">{{ $t('availability') }}</p>
                <p class="summary">{{ $d['availability_line'] }}</p>
            </div>
        @endif

        @if ($socialLinks->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('social_links') }}</p>
                @foreach ($socialLinks as $type => $url)
                    <p class="social-row">
                        @include('talent.cv-builder.templates.partials.cv-social-link', ['type' => $type, 'url' => $url])
                    </p>
                @endforeach
            </div>
        @endif
    </div>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
