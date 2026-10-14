{{-- Expects: $company, $profile, $sector, $location, $websiteUrl, $onBanner (bool) --}}
@php
    $chipClass = $onBanner
        ? 'bg-gray-950/45 text-white ring-1 ring-white/25 backdrop-blur-sm'
        : 'bg-gray-100 text-gray-600';
    $sectorChipClass = $onBanner
        ? $chipClass
        : 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100';
    $websiteClass = $onBanner
        ? 'bg-white text-gray-900 hover:bg-indigo-50 hover:text-indigo-700'
        : 'border border-gray-200 bg-white text-gray-800 hover:border-indigo-300 hover:text-indigo-700';
@endphp
<h1 @class([
    'font-bold tracking-tight',
    'text-3xl text-white [text-shadow:0_1px_3px_rgb(0_0_0/0.6)] lg:text-4xl' => $onBanner,
    'text-2xl text-gray-900' => ! $onBanner,
])>{{ $company->name }}</h1>
<div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
    @if ($sector)
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-medium {{ $sectorChipClass }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
            {{ $sector }}
        </span>
    @endif
    @if ($location !== '')
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-medium {{ $chipClass }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
            {{ $location }}
        </span>
    @endif
    @if ($profile->employee_count)
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-medium {{ $chipClass }}">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
            {{ __('talenma.public_companies.employees', ['count' => $profile->employee_count]) }}
        </span>
    @endif
    @if ($websiteUrl)
        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer nofollow" class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 font-semibold shadow-sm transition {{ $websiteClass }}" data-company-website>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
            {{ __('talenma.public_companies.website') }}
        </a>
    @endif
</div>
