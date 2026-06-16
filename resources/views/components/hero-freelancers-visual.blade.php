@props(['talentsCount' => 0])

@php
    $tiles = config('talenma.hero_bento', []);
    $profiles = __('talenma.home.hero_profiles');
    $fallbackPhoto = asset(config('talenma.hero_fallback_photo'));
@endphp

<div class="hero-visual">
    {{-- Fond doux contenu dans la boîte --}}
    <div class="hero-visual__bg" aria-hidden="true"></div>

    @if ($talentsCount > 0)
        <div class="hero-visual__badge">
            <span class="hero-visual__badge-dot" aria-hidden="true"></span>
            {{ __('talenma.home.talent_count', ['count' => $talentsCount]) }}
        </div>
    @endif

    <div class="hero-mosaic" role="img" aria-label="{{ __('talenma.home.badge') }}">
        @foreach ($tiles as $tile)
            @php
                $profile = $profiles[$tile['profile_index']] ?? null;
            @endphp
            <article class="hero-mosaic__cell hero-mosaic__cell--{{ $tile['size'] }}">
                <img
                    src="{{ asset($tile['photo']) }}"
                    alt="{{ $profile['name'] ?? '' }}"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    class="hero-mosaic__img"
                    onerror="this.onerror=null;this.src='{{ $fallbackPhoto }}';"
                >
                <div class="hero-mosaic__shade" aria-hidden="true"></div>

                @if ($profile)
                    <div class="hero-mosaic__meta">
                        <p class="hero-mosaic__name">{{ $profile['name'] }}</p>
                        <p class="hero-mosaic__role">{{ $profile['role'] }}</p>
                        <div class="hero-mosaic__footer">
                            <span>📍 {{ $profile['city'] }}</span>
                            <span class="hero-mosaic__rate">{{ $profile['rate'] }}</span>
                        </div>
                    </div>
                @endif
            </article>
        @endforeach
    </div>

    <div class="hero-visual__legend">
        <span class="hero-visual__pill">🇲🇦 Maroc</span>
        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
        <span class="hero-visual__pill">🇪🇺 🇬🇧 🌍 Europe &amp; Monde</span>
    </div>
</div>
