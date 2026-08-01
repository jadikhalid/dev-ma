@forelse ($directHire->rounds as $round)
    @include('company.direct-hire._round-card', [
        'directHire' => $directHire,
        'round' => $round,
        'canManageRounds' => $canManageRounds,
        'roundStatuses' => $roundStatuses,
        'hireRoute' => $hireRoute ?? 'company.direct-hire',
    ])
@empty
    <p id="direct-hire-rounds-empty" class="text-sm text-gray-500">{{ __('talenma.direct_hire.rounds_empty') }}</p>
@endforelse
