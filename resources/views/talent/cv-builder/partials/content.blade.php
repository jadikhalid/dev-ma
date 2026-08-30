@php
    $t = fn (string $key) => __("talenma.cv_builder.sections.{$key}", [], $locale);
    $d = $data;
    $has = fn (string $v) => filled(trim((string) $v));
@endphp
<div class="cv-body">
    <header class="cv-header">
        @if ($has($d['full_name'] ?? ''))
            <h1 class="cv-name">{{ $d['full_name'] }}</h1>
        @endif
        @if ($has($d['headline'] ?? ''))
            <p class="cv-headline">{{ $d['headline'] }}</p>
        @endif
        <p class="cv-contact">
            @if ($has($d['email'] ?? ''))<span>{{ $d['email'] }}</span>@endif
            @if ($has($d['phone'] ?? ''))<span>{{ $d['phone'] }}</span>@endif
            @if ($has($d['city'] ?? ''))<span>{{ $d['city'] }}</span>@endif
            @if ($has($d['linkedin_url'] ?? ''))<span>{{ $d['linkedin_url'] }}</span>@endif
            @if ($has($d['github_url'] ?? ''))<span>{{ $d['github_url'] }}</span>@endif
            @if ($has($d['portfolio_url'] ?? ''))<span>{{ $d['portfolio_url'] }}</span>@endif
        </p>
        @if ($has($d['availability_line'] ?? ''))
            <p class="cv-availability">{{ $d['availability_line'] }}</p>
        @endif
    </header>

    @if ($has($d['summary'] ?? ''))
        <section class="cv-section">
            <h2 class="cv-section-title">{{ $t('summary') }}</h2>
            <p class="cv-summary">{{ $d['summary'] }}</p>
        </section>
    @endif

    @php
        $skillGroups = collect($d['skill_groups'] ?? [])->filter(fn ($g) => $has($g['label'] ?? '') || $has($g['items'] ?? ''));
    @endphp
    @if ($skillGroups->isNotEmpty())
        <section class="cv-section">
            <h2 class="cv-section-title">{{ $t('skills') }}</h2>
            @foreach ($skillGroups as $group)
                <p class="cv-skill-row"><strong>{{ $group['label'] }}</strong>@if ($has($group['items'] ?? '')) — {{ $group['items'] }}@endif</p>
            @endforeach
        </section>
    @endif

    @php
        $experiences = collect($d['experiences'] ?? [])->filter(fn ($e) => $has($e['title'] ?? '') || $has($e['company'] ?? ''));
    @endphp
    @if ($experiences->isNotEmpty())
        <section class="cv-section">
            <h2 class="cv-section-title">{{ $t('experience') }}</h2>
            @foreach ($experiences as $exp)
                <div class="cv-entry">
                    <div class="cv-entry-head">
                        <strong>{{ $exp['title'] }}</strong>
                        <span class="cv-dates">
                            {{ $exp['start'] }}@if ($exp['current'] ?? false) – {{ $t('present') }}@elseif ($has($exp['end'] ?? '')) – {{ $exp['end'] }}@endif
                        </span>
                    </div>
                    <p class="cv-company">{{ $exp['company'] }}@if ($has($exp['location'] ?? '')) · {{ $exp['location'] }}@endif</p>
                    <ul class="cv-bullets">
                        @foreach (array_filter($exp['bullets'] ?? [], fn ($b) => $has($b)) as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>
    @endif

    @php
        $education = collect($d['education'] ?? [])->filter(fn ($e) => $has($e['degree'] ?? '') || $has($e['school'] ?? ''));
    @endphp
    @if ($education->isNotEmpty())
        <section class="cv-section">
            <h2 class="cv-section-title">{{ $t('education') }}</h2>
            @foreach ($education as $edu)
                <p class="cv-edu-row"><strong>{{ $edu['degree'] }}</strong> — {{ $edu['school'] }}@if ($has($edu['year'] ?? '')) ({{ $edu['year'] }})@endif</p>
            @endforeach
        </section>
    @endif

    @php
        $languages = collect($d['languages'] ?? [])->filter(fn ($l) => $has($l['name'] ?? ''));
        $certs = collect($d['certifications'] ?? [])->filter(fn ($c) => $has($c));
    @endphp
    @if ($languages->isNotEmpty() || $certs->isNotEmpty())
        <section class="cv-section cv-section-split">
            @if ($languages->isNotEmpty())
                <div>
                    <h2 class="cv-section-title">{{ $t('languages') }}</h2>
                    @foreach ($languages as $lang)
                        <p>{{ $lang['name'] }}@if ($has($lang['level'] ?? '')) ({{ $lang['level'] }})@endif</p>
                    @endforeach
                </div>
            @endif
            @if ($certs->isNotEmpty())
                <div>
                    <h2 class="cv-section-title">{{ $t('certifications') }}</h2>
                    @foreach ($certs as $cert)
                        <p>{{ $cert }}</p>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
