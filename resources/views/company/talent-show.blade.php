@php
    $availabilityLabels = [
        'disponible' => 'available',
        'sous 2 semaines' => 'two_weeks',
        'mission en cours' => 'on_mission',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">{{ $talent->name }}</h2>
                <p class="text-sm text-indigo-600 font-medium">{{ $talent->profile->title }}</p>
            </div>
            <span class="px-4 py-1.5 bg-emerald-100 text-emerald-800 font-semibold rounded-full text-sm">{{ $talent->profile->daily_rate_eur }} {{ __('talenma.talents.per_day') }}</span>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-6">
                <span>📍 {{ $talent->profile->city }}, {{ $talent->profile->country }}</span>
                <span>💼 {{ __('talenma.talents.experience', ['years' => $talent->profile->experience_years]) }}</span>
                <span>⏱ {{ __('talenma.talent.'.($availabilityLabels[$talent->profile->availability] ?? 'available')) }}</span>
            </div>

            @if ($talent->profile->skills)
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach ($talent->profile->skills as $skill)
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-sm rounded-full font-medium">{{ $skill }}</span>
                    @endforeach
                </div>
            @endif

            <div class="prose max-w-none text-gray-700">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.talents.presentation') }}</h3>
                <p>{{ $talent->profile->bio }}</p>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @if ($talent->profile->linkedin_url)
                    <a href="{{ $talent->profile->linkedin_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">LinkedIn</a>
                @endif
                @if ($talent->profile->github_url)
                    <a href="{{ $talent->profile->github_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">GitHub</a>
                @endif
                @if ($talent->profile->portfolio_url)
                    <a href="{{ $talent->profile->portfolio_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">Portfolio</a>
                @endif
            </div>

            <div class="mt-10 p-6 bg-gray-50 rounded-xl border grid sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.talents.direct_title') }}</h4>
                    <p class="mt-1 text-sm text-gray-600">{{ __('talenma.talents.direct_desc') }}</p>
                    <a href="mailto:{{ $talent->email }}" class="mt-3 inline-block px-5 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800">{{ __('talenma.talents.direct_btn') }}</a>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.talents.inter_title') }}</h4>
                    <p class="mt-1 text-sm text-gray-600">{{ __('talenma.talents.inter_desc') }}</p>
                    <a href="{{ route('recruitment.create', $talent) }}?mode=intermediary" class="mt-3 inline-block px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">{{ __('talenma.talents.inter_btn') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
