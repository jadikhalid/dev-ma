<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="girly">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1f2937; margin: 0; line-height: 1.38; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @include('talent.cv-builder.templates.partials.cv-layout-shell', [
            'sidebarSide' => 'left',
            'sidebarWidth' => '33%',
            'mainWidth' => '67%',
            'sidebarBg' => '#3f3f3f',
        ])
        td.sidebar { padding: 0; vertical-align: top; overflow: hidden; position: relative; }
        td.main { padding: 18px 16px 18px 16px; }

        .sidebar-inner { position: relative; z-index: 1; padding: 14px 13px 18px; color: #f8fafc; }
        .pink-corner {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 112px 138px 0 0;
            border-color: #f4a9bb transparent transparent transparent;
            z-index: 0;
        }

        .photo-wrap { text-align: center; margin: 10px 0 14px; position: relative; z-index: 2; }
        .photo {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            display: block;
            margin: 0 auto;
            background: #5b5b5b;
        }
        .photo-placeholder {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: #5b5b5b;
            border: 3px solid #ffffff;
            line-height: 90px;
            font-size: 26pt;
            color: #f4a9bb;
            text-align: center;
            margin: 0 auto;
        }

        .sidebar-name { font-size: 15pt; font-weight: bold; text-align: left; margin: 0 0 4px; line-height: 1.15; }
        .sidebar-name-first { color: #ffffff; }
        .sidebar-name-last { color: #f4a9bb; }
        .sidebar-headline { font-size: 8pt; text-align: left; color: #f3f4f6; margin: 0 0 12px; line-height: 1.3; }

        .sidebar-block { margin-bottom: 12px; }
        .sidebar-title {
            font-size: 7.6pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #ffffff;
            border-bottom: 1px solid #d4d4d4;
            margin: 0 0 8px;
            padding-bottom: 5px;
            font-weight: bold;
        }
        .sidebar-text { font-size: 7.8pt; margin: 0 0 4px; color: #f3f4f6; word-wrap: break-word; text-align: justify; }
        .sidebar-text a { color: #f3f4f6; text-decoration: none; }
        .contact-line { margin: 0 0 5px; font-size: 7.8pt; color: #f3f4f6; word-wrap: break-word; }
        .social-links { margin: 8px 0 2px; line-height: 1; }
        @media print {
            .social-links { margin-top: 14px; margin-bottom: 12px; }
        }
        .social-link { display: inline-block; margin-right: 14px; vertical-align: middle; }
        .social-link:last-child { margin-right: 0; }
        .social-link img { width: 13px; height: 13px; display: block; border: 0; }

        .section { margin-bottom: 12px; page-break-inside: avoid; }
        .section-title {
            font-size: 8.6pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #111827;
            border-bottom: 2px solid #111827;
            margin: 0 0 9px;
            padding-bottom: 5px;
            font-weight: bold;
        }

        .entry { margin-bottom: 9px; }
        .entry-title { font-weight: bold; font-size: 9.4pt; color: #111827; margin: 0 0 2px; }
        .entry-company { font-size: 8.4pt; color: #f4a9bb; margin: 0 0 2px; font-weight: bold; }
        .entry-meta { font-size: 7.6pt; color: #9ca3af; margin: 0 0 5px; }
        .bullets { margin: 0; padding-left: 14px; font-size: 8.3pt; color: #374151; }
        .bullets li { margin-bottom: 2px; }

        .edu-degree { font-weight: bold; font-size: 9pt; color: #111827; margin: 0 0 2px; }
        .edu-school { font-size: 8.3pt; color: #f4a9bb; margin: 0 0 1px; font-weight: bold; }
        .edu-meta { font-size: 7.6pt; color: #9ca3af; margin: 0 0 6px; }
        .cert-row { font-size: 8.3pt; margin: 0 0 3px; color: #374151; }

        table.skills-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td.skill-cell { width: 33.33%; vertical-align: top; padding: 0 8px 10px 0; }
        td.skill-cell:last-child { padding-right: 0; }
        .skill-name { font-size: 7.6pt; color: #111827; margin: 0 0 4px; font-weight: bold; line-height: 1.25; }
        table.skill-track { width: 100%; border-collapse: collapse; height: 6px; background: #e5e7eb; }
        td.skill-fill { height: 6px; background: #f4a9bb; }
        td.skill-rest { height: 6px; background: #e5e7eb; }
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

    $skillBars = collect();
    foreach ($skillGroups as $group) {
        $label = trim((string) ($group['label'] ?? ''));
        $items = collect(preg_split('/\s*[,;|]\s*/u', (string) ($group['items'] ?? '')) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '');

        if ($items->isNotEmpty()) {
            foreach ($items as $item) {
                $skillBars->push($item);
            }
        } elseif ($label !== '') {
            $skillBars->push($label);
        }
    }
    $skillBars = $skillBars->unique()->values()->take(9);
    $skillPercents = [92, 78, 86, 70, 84, 74, 88, 68, 80];
@endphp
<div class="cv-document">
    <table class="cv-columns" cellpadding="0" cellspacing="0">
    <tr>
    <td class="sidebar">
        <div class="pink-corner" aria-hidden="true"></div>
        <div class="sidebar-inner">
            <div class="photo-wrap">
                @if (! empty($photoSrc))
                    <img src="{{ $photoSrc }}" alt="" class="photo">
                @else
                    <div class="photo-placeholder">
                        {{ mb_substr($fullName !== '' ? $fullName : '?', 0, 1) }}
                    </div>
                @endif
            </div>

            @if ($fullName !== '')
                <p class="sidebar-name"><span class="sidebar-name-first">{{ $firstName }}</span>@if ($lastName !== '') <span class="sidebar-name-last">{{ $lastName }}</span>@endif</p>
            @endif
            @if ($has($d['headline'] ?? ''))
                <p class="sidebar-headline">{{ $d['headline'] }}</p>
            @endif

            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('contact') }}</p>
                @if ($has($d['phone'] ?? ''))<p class="contact-line">{{ $d['phone'] }}</p>@endif
                @if ($has($d['email'] ?? ''))<p class="contact-line">{{ $d['email'] }}</p>@endif
                @if ($has($d['city'] ?? ''))<p class="contact-line">{{ $d['city'] }}</p>@endif
                @if ($socialLinks->isNotEmpty())
                    <p class="contact-line social-links">
                        @foreach ($socialLinks as $type => $url)
                            <a
                                href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}"
                                class="social-link"
                                title="{{ $url }}"
                            >
                                <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type, '#f4a9bb') }}" alt="{{ $type }}">
                            </a>
                        @endforeach
                    </p>
                @endif
            </div>

            @if ($has($d['summary'] ?? ''))
                <div class="sidebar-block">
                    <p class="sidebar-title">{{ $t('summary') }}</p>
                    <p class="sidebar-text">{{ $d['summary'] }}</p>
                </div>
            @endif

            @if ($languages->isNotEmpty())
                <div class="sidebar-block">
                    <p class="sidebar-title">{{ $t('languages') }}</p>
                    @foreach ($languages as $lang)
                        <p class="sidebar-text" style="text-align:left;">
                            {{ $lang['name'] }}@if ($has($lang['level'] ?? '')) | {{ $lang['level'] }}@endif
                        </p>
                    @endforeach
                </div>
            @endif

            @if ($has($d['availability_line'] ?? ''))
                <div class="sidebar-block">
                    <p class="sidebar-title">{{ $t('availability') }}</p>
                    <p class="sidebar-text" style="text-align:left;">{{ $d['availability_line'] }}</p>
                </div>
            @endif
        </div>
    </td>
    <td class="main">
        <div class="main-inner">
            @if ($experiences->isNotEmpty())
                <div class="section">
                    <p class="section-title">{{ $t('experience') }}</p>
                    @foreach ($experiences as $exp)
                        <div class="entry">
                            @if ($has($exp['title'] ?? ''))
                                <p class="entry-title">{{ $exp['title'] }}</p>
                            @endif
                            @if ($has($exp['company'] ?? ''))
                                <p class="entry-company">{{ $exp['company'] }}</p>
                            @endif
                            <p class="entry-meta">
                                @if ($has($exp['location'] ?? '')){{ $exp['location'] }} | @endif
                                {{ $exp['start'] ?? '' }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                            </p>
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
                        <div class="entry">
                            @if ($has($edu['degree'] ?? ''))
                                <p class="edu-degree">{{ $edu['degree'] }}</p>
                            @endif
                            @if ($has($edu['school'] ?? ''))
                                <p class="edu-school">{{ $edu['school'] }}</p>
                            @endif
                            @if ($has($edu['year'] ?? ''))
                                <p class="edu-meta">{{ $edu['year'] }}</p>
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

            @if ($skillBars->isNotEmpty())
                <div class="section">
                    <p class="section-title">{{ $t('skills') }}</p>
                    <table class="skills-grid" cellpadding="0" cellspacing="0">
                        @foreach ($skillBars->chunk(3) as $row)
                            <tr>
                                @foreach ($row->values() as $skill)
                                    @php
                                        $percent = $skillPercents[($loop->parent->index * 3) + $loop->index] ?? 75;
                                    @endphp
                                    <td class="skill-cell">
                                        <p class="skill-name">{{ $skill }}</p>
                                        <table class="skill-track" cellpadding="0" cellspacing="0"><tr>
                                            <td class="skill-fill" style="width: {{ $percent }}%;"></td>
                                            <td class="skill-rest" style="width: {{ 100 - $percent }}%;"></td>
                                        </tr></table>
                                    </td>
                                @endforeach
                                @for ($i = $row->count(); $i < 3; $i++)
                                    <td class="skill-cell"></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>
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
