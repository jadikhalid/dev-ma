<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="cv-template" content="starter">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            margin: 0;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cv-document { width: 100%; }
        .header { padding: 18px 22px 0; }
        .body { padding: 0 22px 16px; }
        .serif { font-family: DejaVu Serif, DejaVu Sans, serif; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-main { vertical-align: top; padding-right: 12px; }
        .header-photo { width: 96px; vertical-align: top; text-align: right; }

        .name {
            margin: 0;
            font-size: 20pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.1;
        }
        .headline {
            margin: 6px 0 10px;
            font-size: 10pt;
            color: #2c84b5;
            font-weight: normal;
        }

        .contact-table { width: 100%; max-width: 420px; border-collapse: collapse; }
        .contact-table td { vertical-align: top; padding: 0 10px 4px 0; font-size: 7.8pt; color: #4b5563; }
        .contact-table a { color: #2c84b5; text-decoration: none; }
        .contact-icon {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin-right: 5px;
            vertical-align: middle;
            border: 1.5px solid #2c84b5;
            border-radius: 50%;
        }

        .photo {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-left: auto;
            border: 2px solid #e5e7eb;
        }
        .photo-placeholder {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #2c84b5;
            font-size: 24pt;
            font-weight: bold;
            text-align: center;
            line-height: 84px;
            margin-left: auto;
            border: 2px solid #d1d5db;
        }

        .section { margin: 0 0 13px; page-break-inside: avoid; }
        .section-title {
            margin: 0 0 8px;
            padding: 0 0 5px;
            font-family: DejaVu Serif, DejaVu Sans, serif;
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 2.5px solid #111827;
        }

        .summary {
            margin: 0;
            font-size: 8.8pt;
            color: #374151;
            text-align: justify;
            line-height: 1.45;
        }

        .skills-row {
            margin: 0;
            text-align: left;
            font-size: 8.4pt;
            color: #1f2937;
            line-height: 1.7;
        }
        .skill-item {
            display: inline;
            white-space: nowrap;
        }
        .skill-sep {
            display: inline-block;
            width: 1px;
            height: 11px;
            background: #cbd5e1;
            margin: 0 9px -1px;
            vertical-align: middle;
        }

        .entry { margin: 0 0 10px; padding: 0 0 10px; border-bottom: 1px dashed #d1d5db; }
        .entry:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .entry-title {
            margin: 0 0 2px;
            font-size: 9.5pt;
            font-weight: bold;
            color: #111827;
        }
        .entry-org {
            margin: 0 0 3px;
            font-size: 8.6pt;
            font-weight: bold;
            color: #2c84b5;
        }
        .entry-meta {
            margin: 0 0 5px;
            font-size: 7.6pt;
            color: #6b7280;
        }
        .bullets {
            margin: 0;
            padding-left: 14px;
            font-size: 8.3pt;
            color: #374151;
        }
        .bullets li { margin-bottom: 2px; }

        table.strengths { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td.strength-cell { width: 50%; vertical-align: top; padding: 0 12px 10px 0; }
        td.strength-cell:nth-child(2n) { padding-right: 0; padding-left: 8px; }
        .strength-icon {
            width: 28px;
            height: 28px;
            border: 1.5px solid #2c84b5;
            border-radius: 50%;
            color: #2c84b5;
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            line-height: 26px;
            float: left;
            margin-right: 8px;
        }
        .strength-body { overflow: hidden; }
        .strength-title {
            margin: 0 0 2px;
            font-size: 8.6pt;
            font-weight: bold;
            color: #111827;
        }
        .strength-text {
            margin: 0;
            font-size: 7.8pt;
            color: #4b5563;
            line-height: 1.35;
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
    $socialLinks = collect([
        'linkedin' => (string) ($d['linkedin_url'] ?? ''),
        'github' => (string) ($d['github_url'] ?? ''),
        'portfolio' => (string) ($d['portfolio_url'] ?? ''),
    ])->filter(fn (string $url) => $has($url));

    $fullName = trim((string) ($d['full_name'] ?? ''));

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
    $skills = $skills->unique()->values()->take(10);

    $strengths = $skillGroups
        ->filter(fn ($g) => $has($g['label'] ?? ''))
        ->map(fn ($g) => [
            'title' => trim((string) $g['label']),
            'text' => trim((string) ($g['items'] ?? '')),
        ])
        ->values()
        ->take(4);

    if ($strengths->isEmpty() && $certs->isNotEmpty()) {
        $strengths = $certs->take(4)->map(fn ($cert) => [
            'title' => (string) $cert,
            'text' => '',
        ])->values();
    }

    $strengthIcons = ['★', '◆', '●', '▲'];
@endphp
<div class="cv-document">
    <div class="header">
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-main">
                @if ($fullName !== '')
                    <p class="name serif">{{ mb_strtoupper($fullName) }}</p>
                @endif
                @if ($has($d['headline'] ?? ''))
                    <p class="headline">{{ $d['headline'] }}</p>
                @endif

                <table class="contact-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width:50%;">
                            @if ($has($d['phone'] ?? ''))
                                <div><span class="contact-icon" aria-hidden="true"></span>{{ $d['phone'] }}</div>
                            @endif
                            @if ($socialLinks->isNotEmpty())
                                @foreach ($socialLinks as $type => $url)
                                    <div style="margin-top:3px;">
                                        <a href="{{ \App\Support\TalentCv\TalentCvLinkHelper::href($url) }}">{{ $url }}</a>
                                    </div>
                                @endforeach
                            @endif
                        </td>
                        <td style="width:50%;">
                            @if ($has($d['email'] ?? ''))
                                <div><span class="contact-icon" aria-hidden="true"></span>{{ $d['email'] }}</div>
                            @endif
                            @if ($has($d['city'] ?? ''))
                                <div style="margin-top:3px;"><span class="contact-icon" aria-hidden="true"></span>{{ $d['city'] }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-photo">
                @if (! empty($photoSrc))
                    <img src="{{ $photoSrc }}" alt="" class="photo">
                @else
                    <div class="photo-placeholder">{{ mb_substr($fullName !== '' ? $fullName : '?', 0, 1) }}</div>
                @endif
            </td>
        </tr>
    </table>
    </div>

    <div class="body">
    @if ($has($d['summary'] ?? ''))
        <div class="section">
            <p class="section-title">{{ $t('summary') }}</p>
            <p class="summary">{{ $d['summary'] }}</p>
        </div>
    @endif

    @if ($skills->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('skills') }}</p>
            <p class="skills-row">
                @foreach ($skills as $skill)
                    <span class="skill-item">{{ $skill }}</span>@if (! $loop->last)<span class="skill-sep" aria-hidden="true"></span>@endif
                @endforeach
            </p>
        </div>
    @endif

    @if ($experiences->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('experience') }}</p>
            @foreach ($experiences as $exp)
                <div class="entry">
                    @if ($has($exp['title'] ?? ''))
                        <p class="entry-title">{{ $exp['title'] }}</p>
                    @endif
                    @if ($has($exp['company'] ?? ''))
                        <p class="entry-org">{{ $exp['company'] }}</p>
                    @endif
                    <p class="entry-meta">
                        {{ $exp['start'] ?? '' }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                        @if ($has($exp['location'] ?? '')) · {{ $exp['location'] }}@endif
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
                        <p class="entry-title">{{ $edu['degree'] }}</p>
                    @endif
                    @if ($has($edu['school'] ?? ''))
                        <p class="entry-org">{{ $edu['school'] }}</p>
                    @endif
                    @if ($has($edu['year'] ?? ''))
                        <p class="entry-meta">{{ $edu['year'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($languages->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('languages') }}</p>
            <p class="skills-row">
                @foreach ($languages as $lang)
                    <span class="skill-item">
                        {{ $lang['name'] }}@if ($has($lang['level'] ?? '')) ({{ $lang['level'] }})@endif
                    </span>@if (! $loop->last)<span class="skill-sep" aria-hidden="true"></span>@endif
                @endforeach
            </p>
        </div>
    @endif

    @if ($strengths->isNotEmpty())
        <div class="section">
            <p class="section-title">{{ $t('strengths') }}</p>
            <table class="strengths" cellpadding="0" cellspacing="0">
                @foreach ($strengths->chunk(2) as $row)
                    <tr>
                        @foreach ($row->values() as $strength)
                            <td class="strength-cell">
                                <div class="strength-icon" aria-hidden="true">{{ $strengthIcons[($loop->parent->index * 2) + $loop->index] ?? '●' }}</div>
                                <div class="strength-body">
                                    <p class="strength-title">{{ $strength['title'] }}</p>
                                    @if ($has($strength['text'] ?? ''))
                                        <p class="strength-text">{{ $strength['text'] }}</p>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                        @for ($i = $row->count(); $i < 2; $i++)
                            <td class="strength-cell"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if ($has($d['availability_line'] ?? ''))
        <div class="section">
            <p class="section-title">{{ $t('availability') }}</p>
            <p class="summary">{{ $d['availability_line'] }}</p>
        </div>
    @endif
    </div>
</div>
@include('talent.cv-builder.templates.partials.cv-preview-page-pads')
</body>
</html>
