<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #1e293b; margin: 0; line-height: 1.38; }
        table.layout { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td.main { width: 66%; vertical-align: top; padding: 18px 16px 18px 18px; background: #fff; }
        td.sidebar { width: 34%; vertical-align: top; padding: 16px 14px; background: #ecfdf5; border-left: 3px solid #0d9488; }

        .hero-name { font-size: 19pt; font-weight: bold; margin: 0 0 4px; color: #0f766e; line-height: 1.12; }
        .hero-headline { font-size: 9pt; color: #475569; margin: 0 0 14px; line-height: 1.35; font-weight: bold; }

        .photo-wrap { text-align: center; margin-bottom: 14px; }
        .photo { width: 96px; height: 96px; border-radius: 8px; object-fit: cover; border: 2px solid #0d9488; }
        .photo-placeholder { width: 96px; height: 96px; border-radius: 8px; background: #ccfbf1; border: 2px solid #0d9488; line-height: 92px; font-size: 28pt; color: #0f766e; text-align: center; margin: 0 auto; }

        .sidebar-block { margin-bottom: 12px; }
        .sidebar-title { font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.08em; color: #0f766e; border-bottom: 1px solid #99f6e4; margin: 0 0 5px; padding-bottom: 2px; font-weight: bold; }
        .sidebar-text { font-size: 8pt; margin: 0 0 3px; color: #334155; word-wrap: break-word; }
        .sidebar-text a { color: #334155; text-decoration: none; }
        .social-links { margin: 6px 0 2px; line-height: 1; }
        .social-link { display: inline-block; margin-right: 10px; vertical-align: middle; }
        .social-link img { width: 14px; height: 14px; display: block; border: 0; }
        .skill-label { font-weight: bold; color: #0f766e; font-size: 8pt; }
        .skill-items { font-size: 7.5pt; color: #475569; }

        .section { margin-bottom: 11px; page-break-inside: avoid; }
        .section-title { font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.06em; color: #fff; background: #0d9488; margin: 0 0 6px; padding: 4px 8px; font-weight: bold; }
        .summary { margin: 0; text-align: justify; font-size: 9pt; color: #334155; }
        .entry { margin-bottom: 7px; }
        .entry-head { width: 100%; }
        .entry-title { font-weight: bold; font-size: 9.5pt; color: #0f766e; }
        .entry-dates { float: right; font-size: 8pt; color: #64748b; }
        .entry-company { font-style: italic; font-size: 8.5pt; color: #475569; margin: 1px 0 3px; }
        .bullets { margin: 0; padding-left: 14px; font-size: 8.5pt; color: #334155; }
        .bullets li { margin-bottom: 2px; }
        .edu-row { font-size: 8.5pt; margin: 0 0 3px; color: #334155; }
        .cert-row { font-size: 8.5pt; margin: 0 0 2px; color: #334155; }
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
    <td class="main">
        @if ($has($d['full_name'] ?? ''))
            <p class="hero-name">{{ $d['full_name'] }}</p>
        @endif
        @if ($has($d['headline'] ?? ''))
            <p class="hero-headline">{{ $d['headline'] }}</p>
        @endif

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
    </td>
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
                            <img src="{{ \App\Support\TalentCv\TalentCvLinkHelper::iconSrc($type, '#0f766e') }}" alt="{{ $type }}">
                        </a>
                    @endforeach
                </p>
            @endif
        </div>

        @if ($skillGroups->isNotEmpty())
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
            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('languages') }}</p>
                @foreach ($languages as $lang)
                    <p class="sidebar-text">{{ $lang['name'] }}@if ($has($lang['level'] ?? '')) · {{ $lang['level'] }}@endif</p>
                @endforeach
            </div>
        @endif

        @if ($has($d['availability_line'] ?? ''))
            <div class="sidebar-block">
                <p class="sidebar-title">{{ $t('availability') }}</p>
                <p class="sidebar-text">{{ $d['availability_line'] }}</p>
            </div>
        @endif
    </td>
</tr>
</table>
</body>
</html>
