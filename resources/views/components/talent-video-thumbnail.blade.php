@props([
    'branded' => true,
    'personName' => null,
])

@php
    $uid = 'tdmVid'.uniqid();
    $bgId = $uid.'Bg';
    $dotsId = $uid.'Dots';
    $personName = trim((string) ($personName ?? ''));
    $label = __('talenma.dashboard.talent.video_thumbnail_label');
@endphp

{{-- Vignette standard partagée (tous les talents) --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 640 360', 'xmlns' => 'http://www.w3.org/2000/svg', 'aria-hidden' => 'true', 'focusable' => 'false']) }} preserveAspectRatio="xMidYMid slice">
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
    <rect width="640" height="360" fill="url(#{{ $bgId }})" />
    <rect width="640" height="360" fill="url(#{{ $dotsId }})" />
    <circle cx="520" cy="70" r="90" fill="rgba(255,255,255,0.08)" />
    <circle cx="90" cy="300" r="110" fill="rgba(15,23,42,0.18)" />
    @if ($branded)
        @if ($personName !== '')
            <text x="40" y="292" fill="rgba(255,255,255,0.95)" font-family="system-ui,Segoe UI,sans-serif" font-size="30" font-weight="700">{{ \Illuminate\Support\Str::limit($personName, 36, '…') }}</text>
            <text x="40" y="324" fill="rgba(255,255,255,0.7)" font-family="system-ui,Segoe UI,sans-serif" font-size="16" font-weight="500">{{ $label }}</text>
        @else
            <text x="40" y="310" fill="rgba(255,255,255,0.9)" font-family="system-ui,Segoe UI,sans-serif" font-size="22" font-weight="700">{{ $label }}</text>
        @endif
    @else
        {{-- Fond vide : motifs seulement, sans texte (évite le chevauchement avec le message front) --}}
        <g fill="none" stroke="rgba(255,255,255,0.16)" stroke-width="2">
            <circle cx="320" cy="168" r="42" />
            <path d="M308 168l20-12v24l-20-12Z" fill="rgba(255,255,255,0.2)" stroke="none" />
        </g>
    @endif
</svg>
