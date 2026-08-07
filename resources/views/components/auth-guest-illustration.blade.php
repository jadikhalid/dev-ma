{{-- Illustration aérée : un talent rejoint la plateforme (peu d'éléments, beaucoup d'air). --}}
<svg
    {{ $attributes->merge(['class' => 'w-full h-full max-h-full']) }}
    viewBox="0 0 640 420"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    role="img"
    aria-hidden="true"
    preserveAspectRatio="xMidYMid meet"
>
    {{-- Sol / horizon discret --}}
    <path d="M48 318h544" stroke="currentColor" stroke-opacity="0.18" stroke-width="2" stroke-linecap="round" />
    <circle cx="520" cy="96" r="56" fill="currentColor" fill-opacity="0.06" />
    <circle cx="120" cy="88" r="36" fill="currentColor" fill-opacity="0.05" />

    {{-- Une seule trajectoire ample --}}
    <path
        d="M210 250C280 250 310 170 390 170C450 170 470 210 500 230"
        stroke="currentColor"
        stroke-opacity="0.45"
        stroke-width="3"
        stroke-linecap="round"
        stroke-dasharray="10 14"
    >
        <animate attributeName="stroke-dashoffset" values="0;48" dur="7s" repeatCount="indefinite" />
    </path>

    {{-- Talent (grande silhouette, à gauche) --}}
    <g transform="translate(96 148)">
        <circle cx="70" cy="120" r="88" fill="currentColor" fill-opacity="0.08" />
        <circle cx="70" cy="48" r="34" fill="currentColor" fill-opacity="0.92" />
        <path
            d="M18 168c8-40 32-58 52-58s44 18 52 58c2 8-2 16-10 16H28c-8 0-12-8-10-16z"
            fill="currentColor"
            fill-opacity="0.92"
        />
    </g>

    {{-- Porte / plateforme (à droite, simple) --}}
    <g transform="translate(455 148)">
        <rect width="108" height="156" rx="22" fill="currentColor" fill-opacity="0.1" stroke="currentColor" stroke-opacity="0.35" stroke-width="2" />
        <rect x="22" y="28" width="64" height="100" rx="14" fill="currentColor" fill-opacity="0.14" />
        <circle cx="74" cy="84" r="5" fill="#a5b4fc" />
        {{-- Flèche d'entrée --}}
        <path d="M-28 78h34" stroke="#34d399" stroke-width="3" stroke-linecap="round" />
        <path d="M-2 66l14 12-14 12" stroke="#34d399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
    </g>

    {{-- Petit signal de validation, isolé --}}
    <g transform="translate(300 118)">
        <circle cx="0" cy="0" r="22" fill="#34d399" fill-opacity="0.9" />
        <path d="M-9 0l6 6 12-14" stroke="#064e3b" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
    </g>
</svg>
