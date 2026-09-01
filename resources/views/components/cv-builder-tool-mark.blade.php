@props([
    'class' => 'h-full w-full',
])

<svg
    {{ $attributes->merge(['class' => $class]) }}
    viewBox="0 0 240 280"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    role="img"
    aria-hidden="true"
>
    <defs>
        <linearGradient id="cvMarkSky" x1="20" y1="0" x2="220" y2="280" gradientUnits="userSpaceOnUse">
            <stop stop-color="#312E81"/>
            <stop offset="0.55" stop-color="#4338CA"/>
            <stop offset="1" stop-color="#0F766E"/>
        </linearGradient>
        <linearGradient id="cvMarkSidebar" x1="48" y1="56" x2="88" y2="220" gradientUnits="userSpaceOnUse">
            <stop stop-color="#1E3A5F"/>
            <stop offset="1" stop-color="#0D9488"/>
        </linearGradient>
        <linearGradient id="cvMarkGlow" x1="120" y1="40" x2="200" y2="120" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FFFFFF" stop-opacity="0.22"/>
            <stop offset="1" stop-color="#FFFFFF" stop-opacity="0"/>
        </linearGradient>
    </defs>

    <rect width="240" height="280" rx="24" fill="url(#cvMarkSky)"/>
    <circle cx="196" cy="48" r="36" fill="#FFFFFF" opacity="0.07"/>
    <circle cx="40" cy="64" r="20" fill="#14B8A6" opacity="0.2"/>
    <ellipse cx="120" cy="72" rx="88" ry="52" fill="url(#cvMarkGlow)"/>

    {{-- Deuxième feuille (profondeur) --}}
    <rect x="64" y="58" width="136" height="184" rx="14" fill="#FFFFFF" opacity="0.28" stroke="#C7D2FE" stroke-width="1.5"/>

    {{-- CV principal --}}
    <g>
        <rect x="52" y="48" width="136" height="184" rx="14" fill="#FFFFFF" stroke="#E0E7FF" stroke-width="2"/>
        <rect x="52" y="48" width="38" height="184" rx="14" fill="url(#cvMarkSidebar)"/>
        <circle cx="71" cy="82" r="12" fill="#FFFFFF" opacity="0.95"/>
        <rect x="62" y="102" width="18" height="3" rx="1.5" fill="#FFFFFF" opacity="0.8"/>
        <rect x="60" y="110" width="22" height="2" rx="1" fill="#BFDBFE"/>

        <rect x="100" y="68" width="72" height="6" rx="3" fill="#4338CA" opacity="0.85"/>
        <rect x="100" y="82" width="58" height="3" rx="1.5" fill="#E2E8F0"/>
        <rect x="100" y="90" width="64" height="3" rx="1.5" fill="#E2E8F0"/>
        <rect x="100" y="98" width="48" height="3" rx="1.5" fill="#E2E8F0"/>

        <rect x="100" y="118" width="40" height="4" rx="2" fill="#6366F1" opacity="0.35"/>
        <rect x="100" y="128" width="62" height="2.5" rx="1.25" fill="#E2E8F0"/>
        <rect x="100" y="136" width="56" height="2.5" rx="1.25" fill="#E2E8F0"/>
        <rect x="100" y="144" width="60" height="2.5" rx="1.25" fill="#E2E8F0"/>

        <rect x="100" y="164" width="36" height="4" rx="2" fill="#6366F1" opacity="0.35"/>
        <rect x="100" y="174" width="54" height="2.5" rx="1.25" fill="#E2E8F0"/>
        <rect x="100" y="182" width="48" height="2.5" rx="1.25" fill="#E2E8F0"/>
        <rect x="100" y="190" width="52" height="2.5" rx="1.25" fill="#E2E8F0"/>

        <path d="M100 206h44" stroke="#0D9488" stroke-width="3" stroke-linecap="round"/>
    </g>

    {{-- Badge PDF --}}
    <g>
        <rect x="148" y="28" width="58" height="26" rx="13" fill="#FDE047" stroke="#FFFFFF" stroke-width="2"/>
        <text x="177" y="45" text-anchor="middle" fill="#7C2D12" font-family="system-ui, sans-serif" font-size="11" font-weight="800">PDF</text>
    </g>

    {{-- Icône édition --}}
    <g>
        <rect x="36" y="214" width="44" height="44" rx="12" fill="#FFFFFF" fill-opacity="0.14" stroke="#FFFFFF" stroke-opacity="0.25"/>
        <path d="M52 236l14-14 6 6-14 14h-6v-6z" fill="#FFFFFF" opacity="0.9"/>
        <path d="M62 224l6 6" stroke="#C7D2FE" stroke-width="2" stroke-linecap="round"/>
    </g>

    {{-- Icône téléchargement --}}
    <g>
        <rect x="160" y="214" width="44" height="44" rx="12" fill="#FFFFFF" fill-opacity="0.14" stroke="#FFFFFF" stroke-opacity="0.25"/>
        <path d="M182 226v18M175 239l7 7 7-7" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M170 252h24" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round"/>
    </g>
</svg>
