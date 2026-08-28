@props([
    'talentsCount' => 0,
    'tiles' => [],
])

@php
    $fallbackPhoto = asset(config('talenma.hero_fallback_photo'));
    $tiles = collect($tiles)->take(6)->values();
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
            <article class="hero-mosaic__cell">
                <img
                    src="{{ $tile['photo'] }}"
                    alt="{{ $tile['name'] ?? '' }}"
                    loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    class="hero-mosaic__img"
                    onerror="this.onerror=null;this.src='{{ $fallbackPhoto }}';"
                >
                <div class="hero-mosaic__shade" aria-hidden="true"></div>

                <div class="hero-mosaic__meta">
                    <p class="hero-mosaic__name">{{ $tile['name'] }}</p>
                    <p class="hero-mosaic__role">{{ $tile['role'] }}</p>
                    <div class="hero-mosaic__footer">
                        <span class="hero-mosaic__location" title="{{ $tile['city'] }}">📍 {{ $tile['city'] }}</span>
                        @php
                            $status = $tile['availability'] ?? 'available';
                            $statusLabel = __('talenma.home.hero_availability.'.$status);
                        @endphp
                        <span class="hero-mosaic__status hero-mosaic__status--{{ $status }}">{{ $statusLabel }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
