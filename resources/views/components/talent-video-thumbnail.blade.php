@props([
    'branded' => true,
    'personName' => null,
    'portrait' => false,
])

@php
    $uid = 'tdmVid'.uniqid();
    $bgId = $uid.'Bg';
    $dotsId = $uid.'Dots';
    $personName = trim((string) ($personName ?? ''));
    $label = __('talenma.dashboard.talent.video_thumbnail_label');
    $w = $portrait ? 360 : 640;
    $h = $portrait ? 450 : 360;
@endphp

{{-- Vignette standard partagée (tous les talents) --}}
<svg {{ $attributes->merge(['viewBox' => "0 0 {$w} {$h}", 'xmlns' => 'http://www.w3.org/2000/svg', 'aria-hidden' => 'true', 'focusable' => 'false']) }} preserveAspectRatio="xMidYMid slice">
    <defs>
        <linearGradient id="{{ $bgId }}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#312e81" />
            <stop offset="55%" stop-color="#4f46e5" />
            <stop offset="100%" stop-color="#818cf8" />
        </linearGradient>
        <pattern id="{{ $dotsId }}" width="24" height="24" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1.2" fill="rgba(255,255,255,0.18)" />
        </pattern>
    </defs>
    <rect width="{{ $w }}" height="{{ $h }}" fill="url(#{{ $bgId }})" />
    <rect width="{{ $w }}" height="{{ $h }}" fill="url(#{{ $dotsId }})" />
    @if ($portrait)
        <circle cx="{{ (int) ($w * 0.78) }}" cy="70" r="70" fill="rgba(255,255,255,0.08)" />
        <circle cx="55" cy="{{ (int) ($h * 0.82) }}" r="90" fill="rgba(15,23,42,0.18)" />
    @else
        <circle cx="520" cy="70" r="90" fill="rgba(255,255,255,0.08)" />
        <circle cx="90" cy="300" r="110" fill="rgba(15,23,42,0.18)" />
    @endif
    @if ($branded)
        @if ($personName !== '')
            <text x="28" y="{{ $portrait ? $h - 58 : 292 }}" fill="rgba(255,255,255,0.95)" font-family="system-ui,Segoe UI,sans-serif" font-size="{{ $portrait ? 22 : 30 }}" font-weight="700">{{ \Illuminate\Support\Str::limit($personName, $portrait ? 22 : 36, '…') }}</text>
            <text x="28" y="{{ $portrait ? $h - 32 : 324 }}" fill="rgba(255,255,255,0.7)" font-family="system-ui,Segoe UI,sans-serif" font-size="{{ $portrait ? 13 : 16 }}" font-weight="500">{{ $label }}</text>
        @else
            <text x="28" y="{{ $portrait ? $h - 40 : 310 }}" fill="rgba(255,255,255,0.9)" font-family="system-ui,Segoe UI,sans-serif" font-size="{{ $portrait ? 16 : 22 }}" font-weight="700">{{ $label }}</text>
        @endif
    @else
        {{-- Fond vide : motifs seulement, sans texte (évite le chevauchement avec le message front) --}}
        <g fill="none" stroke="rgba(255,255,255,0.16)" stroke-width="2">
            <circle cx="{{ (int) ($w / 2) }}" cy="{{ (int) ($h * 0.42) }}" r="{{ $portrait ? 36 : 42 }}" />
            <path d="M{{ (int) ($w / 2) - 12 }} {{ (int) ($h * 0.42) }}l{{ $portrait ? 18 : 20 }}-{{ $portrait ? 10 : 12 }}v{{ $portrait ? 20 : 24 }}l-{{ $portrait ? 18 : 20 }}-{{ $portrait ? 10 : 12 }}Z" fill="rgba(255,255,255,0.2)" stroke="none" />
        </g>
    @endif
</svg>
