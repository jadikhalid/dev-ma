<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="basic">
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
        .header { padding: 30px 40px 14px; }
        .body { padding: 6px 40px 26px; }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-main { vertical-align: top; padding-right: 20px; }
        .header-contact {
            width: 38%;
            vertical-align: top;
            padding-left: 12px;
            border-left: 2px solid #555555;
        }

        .name {
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #111111;
            line-height: 1.15;
        }
        .headline {
            margin: 8px 0 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #777777;
            line-height: 1.25;
        }
        .contact-line {
            margin: 0 0 3px;
            font-size: 8.5pt;
            color: #666666;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .section { margin: 0 0 16px; }
        .section-title {
            margin: 0 0 7px;
            padding: 0 0 5px;
            font-size: 11pt;
            font-weight: bold;
            color: #111111;
            border-bottom: 1px solid #555555;
        }

        .summary {
            margin: 0;
            font-size: 8.5pt;
            color: #555555;
            text-align: justify;
            line-height: 1.45;
        }

        table.timeline {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 0 10px;
        }
        table.timeline td {
            vertical-align: top;
            padding: 0;
        }
        td.tl-left {
            width: 24%;
            padding-right: 12px;
            font-size: 8.5pt;
            color: #222222;
            line-height: 1.35;
        }
        td.tl-right {
            width: 76%;
            font-size: 8.5pt;
            color: #222222;
        }
        .tl-title {
            margin: 0 0 2px;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #111111;
            line-height: 1.3;
        }
        .tl-sub {
            margin: 0;
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #222222;
            line-height: 1.35;
        }
        .bullets {
            margin: 2px 0 0;
            padding-left: 14px;
            font-size: 8.5pt;
            color: #222222;
            line-height: 1.4;
        }
        .bullets li { margin-bottom: 2px; }

        .entry { page-break-inside: avoid; }
        .edu-row { page-break-inside: avoid; }
        .lang-row,
        .cert-row { margin: 0 0 3px; font-size: 8.5pt; color: #555555; }
        .social-row { margin: 3px 0 0; font-size: 8.5pt; color: #555555; line-height: 1.35; }
        .social-row:first-child { margin-top: 0; }
        .social-row .social-link { color: #555555; text-decoration: none; }
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

    $formatDates = function (array $exp) use ($t): array {
        $start = trim((string) ($exp['start'] ?? ''));
        $end = ($exp['current'] ?? false)
            ? $t('present')
            : trim((string) ($exp['end'] ?? ''));

        return array_values(array_filter([$start, $end], fn ($v) => $v !== ''));
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
                </td>
                @if ($has($d['city'] ?? '') || $has($d['phone'] ?? '') || $has($d['email'] ?? ''))
                    <td class="header-contact">
                        @if ($has($d['city'] ?? ''))
                            <p class="contact-line">{{ $d['city'] }}</p>
                        @endif
                        @if ($has($d['phone'] ?? ''))
                            <p class="contact-line">{{ $d['phone'] }}</p>
                        @endif
                        @if ($has($d['email'] ?? ''))
                            <p class="contact-line">{{ $d['email'] }}</p>
                        @endif
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <div class="body">
        @if ($has($d['summary'] ?? ''))
            <div class="section">
                <p class="section-title">{{ $t('about_me') }}</p>
                <p class="summary">{{ $d['summary'] }}</p>
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
                                @foreach ($dates as $date)
                                    {{ $date }}@if (! $loop->last)<br>@endif
                                @endforeach
                                @if ($has($exp['location'] ?? ''))
                                    @if ($dates !== [])<br>@endif
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

        @if ($skillGroups->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('skills') }}</p>
                @foreach ($skillGroups as $group)
                    <p class="summary">
                        @if ($has($group['label'] ?? ''))<strong>{{ $group['label'] }}</strong>@if ($has($group['items'] ?? '')) : @endif @endif{{ $group['items'] ?? '' }}
                    </p>
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

        @if ($certs->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('courses_certifications') }}</p>
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

        @if ($interests->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('hobbies') }}</p>
                <p class="summary">{{ $interests->implode(', ') }}</p>
            </div>
        @endif
    </div>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
