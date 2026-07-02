@php
    $availabilityLabels = [
        'disponible' => 'available',
        'sous 2 semaines' => 'two_weeks',
        'mission en cours' => 'on_mission',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.talents.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.talents.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-2xl border p-6">
            <form method="GET" class="grid md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="keyword" :value="__('talenma.talents.skill')" />
                    <x-text-input id="keyword" name="keyword" class="mt-1 block w-full" :value="request('keyword')" placeholder="Laravel, React…" />
                </div>
                <div>
                    <x-input-label for="city" :value="__('talenma.talents.city')" />
                    <select id="city" name="city" class="mt-1 block w-full border-gray-300 rounded-lg text-sm">
                        <option value="">{{ __('talenma.talents.all') }}</option>
                        @foreach (['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Agadir'] as $v)
                            <option value="{{ $v }}" {{ request('city') == $v ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="country" :value="__('talenma.talents.country')" />
                    <select id="country" name="country" class="mt-1 block w-full border-gray-300 rounded-lg text-sm">
                        <option value="">{{ __('talenma.talents.all_countries') }}</option>
                        <option value="Maroc" {{ request('country') == 'Maroc' ? 'selected' : '' }}>{{ __('talenma.common.morocco') }}</option>
                    </select>
                </div>
                <x-primary-button class="w-full justify-center py-2.5">{{ __('talenma.talents.filter') }}</x-primary-button>
            </form>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            @forelse ($talents as $talent)
                <div class="bg-white rounded-2xl border p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg">{{ $talent->name }}</h3>
                                <p class="text-indigo-600 text-sm font-medium">{{ $talent->profile->title }}</p>
                                @if ($talent->profile->specialization)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $talent->profile->sectorLabel() }} · {{ $talent->profile->professionLabel() }} · {{ $talent->profile->specialization }}
                                    </p>
                                @endif
                            </div>
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full">{{ $talent->profile->daily_rate_eur }} {{ __('talenma.talents.per_day') }}</span>
                        </div>
                        <div class="mt-2 flex gap-3 text-xs text-gray-500">
                            <span>📍 {{ $talent->profile->city }}, {{ $talent->profile->country }}</span>
                            <span>⏱ {{ __('talenma.talent.'.($availabilityLabels[$talent->profile->availability] ?? 'available')) }}</span>
                        </div>
                        @if ($talent->profile->skills)
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($talent->profile->skills as $skill)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                        <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $talent->profile->bio }}</p>
                    </div>
                    <div class="mt-5 pt-4 border-t flex gap-2">
                        <a href="{{ route('company.talent.show', $talent) }}" class="flex-1 text-center px-3 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">{{ __('talenma.talents.view') }}</a>
                        <a href="{{ route('recruitment.create', $talent) }}?mode=intermediary" class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">{{ __('talenma.talents.intermediary') }}</a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-16 text-gray-500">
                    <p class="text-lg font-medium">{{ __('talenma.talents.empty') }}</p>
                    <p class="text-sm mt-1">{{ __('talenma.talents.empty_desc') }}</p>
                </div>
            @endforelse
        </div>
        <div>{{ $talents->links() }}</div>
    </div>
</x-app-layout>
