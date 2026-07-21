{{-- Vignette standard partagée (tous les talents) --}}
<svg {{ $attributes->merge(['viewBox' => '0 0 640 360', 'xmlns' => 'http://www.w3.org/2000/svg', 'aria-hidden' => 'true', 'focusable' => 'false']) }} preserveAspectRatio="xMidYMid slice">
    <defs>
        <linearGradient id="tdmVideoThumbBg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#312e81" />
            <stop offset="55%" stop-color="#4f46e5" />
            <stop offset="100%" stop-color="#818cf8" />
        </linearGradient>
        <pattern id="tdmVideoThumbDots" width="24" height="24" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1.2" fill="rgba(255,255,255,0.18)" />
        </pattern>
    </defs>
    <rect width="640" height="360" fill="url(#tdmVideoThumbBg)" />
    <rect width="640" height="360" fill="url(#tdmVideoThumbDots)" />
    <circle cx="520" cy="70" r="90" fill="rgba(255,255,255,0.08)" />
    <circle cx="90" cy="300" r="110" fill="rgba(15,23,42,0.18)" />
    <g fill="none" stroke="rgba(255,255,255,0.22)" stroke-width="2">
        <rect x="48" y="48" width="120" height="80" rx="12" />
        <path d="M72 120h72M72 96h48" />
    </g>
    <text x="48" y="300" fill="rgba(255,255,255,0.9)" font-family="system-ui,Segoe UI,sans-serif" font-size="28" font-weight="700">Talents du Maroc</text>
    <text x="48" y="328" fill="rgba(255,255,255,0.65)" font-family="system-ui,Segoe UI,sans-serif" font-size="16">Présentation talent</text>
</svg>
