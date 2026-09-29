<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="basic_plus">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #222222;
            margin: 0;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cv-document { width: 100%; }
        .header { padding: 24px 36px 12px; }
        .body { padding: 4px 36px 24px; }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-main { vertical-align: top; padding-right: 20px; }
        .header-photo { width: 176px; vertical-align: top; text-align: right; }

        .name {
            margin: 0;
            font-size: 24pt;
            font-weight: normal;
            color: #2b2b2b;
            line-height: 1.15;
        }
        .headline {
            margin: 6px 0 0;
            font-size: 12pt;
            color: #2b2b2b;
            line-height: 1.25;
        }
        .headline-rule {
            width: 150px;
            margin: 12px 0 12px;
            border-top: 1.5px solid #2b2b2b;
        }
        .contact-line {
            margin: 0 0 2px;
            font-size: 8.5pt;
            color: #333333;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .photo {
            width: 168px;
            height: 184px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            border: 0;
        }
        .photo-placeholder {
            width: 168px;
            height: 184px;
            background: #ece9f6;
            color: #7b6fb4;
            font-size: 36pt;
            text-align: center;
            line-height: 184px;
            margin-left: auto;
        }

        .section { margin: 0 0 14px; }
        .section-title {
            margin: 0 0 8px;
            padding: 0 0 5px;
            font-size: 12.5pt;
            font-weight: normal;
            text-transform: uppercase;
            color: #7b6fb4;
            border-bottom: 1.5px solid #2b2b2b;
        }

        .summary {
            margin: 0;
            font-size: 8.5pt;
            color: #333333;
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
            width: 20%;
            padding-right: 10px;
            font-size: 8.5pt;
            color: #333333;
            line-height: 1.35;
        }
        .tl-location {
            display: block;
            margin-top: 3px;
            color: #9a9a9a;
        }
        td.tl-right {
            width: 80%;
            font-size: 8.5pt;
            color: #333333;
        }
        .tl-title {
            margin: 0 0 2px;
            font-size: 9pt;
            font-weight: bold;
            color: #222222;
            line-height: 1.3;
        }
        .tl-sub {
            margin: 4px 0 0;
            font-size: 8.5pt;
            color: #333333;
            line-height: 1.35;
        }
        .bullets {
            margin: 3px 0 0;
            padding-left: 30px;
            font-size: 8.5pt;
            color: #333333;
            line-height: 1.4;
        }
        .bullets li { margin-bottom: 1px; }

        .entry { page-break-inside: avoid; }
        .edu-row { page-break-inside: avoid; }

        .lang-row,
        .cert-row { margin: 0 0 3px; font-size: 8.5pt; color: #333333; }
        .social-row { margin: 3px 0 0; font-size: 8.5pt; color: #333333; line-height: 1.35; }
        .social-row:first-child { margin-top: 0; }
        .social-row .social-link { color: #333333; text-decoration: none; }
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

    $formatDates = function (array $exp) use ($t): string {
        $start = trim((string) ($exp['start'] ?? ''));
        $end = ($exp['current'] ?? false)
            ? $t('present')
            : trim((string) ($exp['end'] ?? ''));

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
                    <div class="headline-rule"></div>
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
                        <img src="{{ $photoSrc }}" alt="" class="photo" width="168" height="184">
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
                                    <span class="tl-location">{{ $exp['location'] }}</span>
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
                <p class="section-title">{{ $t('contact_me') }}</p>
                @foreach ($socialLinks as $type => $url)
                    <p class="social-row">
                        @include('talent.cv-builder.templates.partials.cv-social-link', ['type' => $type, 'url' => $url])
                    </p>
                @endforeach
            </div>
        @endif

        @if ($interests->isNotEmpty())
            <div class="section">
                <p class="section-title">{{ $t('interests') }}</p>
                <p class="summary">{{ $interests->implode(', ') }}</p>
            </div>
        @endif
    </div>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
