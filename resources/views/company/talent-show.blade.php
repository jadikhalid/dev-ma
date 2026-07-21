@php
    $profile = $talent->profile;
    $isPublic = $profile->isPublic();
    $displayName = $profile->visibleDisplayName($talent);
    $avatarUrl = $profile->visibleAvatarUrl($talent);
    $statusTone = $profile->statusTone();
    $statusClasses = match ($statusTone) {
        'busy' => 'bg-gray-200 text-gray-700',
        'listening' => 'bg-amber-100 text-amber-800',
        default => 'bg-emerald-100 text-emerald-800',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $displayName }}" class="h-16 w-16 rounded-full object-cover ring-1 ring-gray-200 shrink-0">
                @else
                    <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700 shrink-0">{{ $talent->initials() }}</span>
                @endif
                <div class="min-w-0">
                    <h2 class="text-xl font-bold">{{ $displayName }}</h2>
                    <p class="text-sm text-indigo-600 font-medium">
                        {{ collect([$profile->professionLabel(), $profile->sectorLabel()])->filter()->implode(' - ') }}
                    </p>
                    @if ($profile->employerLabel())
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.talent.employer') }} : {{ $profile->employerLabel() }}</p>
                    @endif
                </div>
            </div>
            <span class="px-4 py-1.5 font-semibold rounded-full text-sm {{ $statusClasses }}">{{ $profile->statusLabel() }}</span>
        </div>
    </x-slot>

    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-6">
                <span>💼 {{ __('talenma.talents.experience', ['years' => $profile->experience_years]) }}</span>
                <span>⏱ {{ $profile->statusLabel() }}</span>
            </div>

            @if ($profile->specialization)
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-700">{{ __('talenma.dashboard.talent.specialty_skills') }}</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (
                            collect(explode(',', (string) $profile->specialization))
                                ->map(fn ($item) => trim($item))
                                ->filter()
                                ->unique()
                                ->values()
                            as $item
                        )
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-sm rounded-full font-medium">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($profile->workModeLabels())
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach ($profile->workModeLabels() as $mode)
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">{{ $mode }}</span>
                    @endforeach
                </div>
            @endif

            @if ($profile->languageLabels())
                <p class="mb-4 text-sm text-gray-600">
                    {{ __('talenma.talent.languages') }} :
                    {{ implode(', ', $profile->languageLabels()) }}
                </p>
            @endif

            <div class="prose max-w-none text-gray-700">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.talents.presentation') }}</h3>
                <p>{{ $profile->bio }}</p>
            </div>

            @if ($isPublic && filled($profile->presentation_video_url))
                <div class="mt-8">
                    <x-talent-video-snapshot :video-url="$profile->presentation_video_url" />
                </div>
            @endif

            @if ($isPublic)
                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($profile->linkedin_url)
                        <a href="{{ $profile->linkedin_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">LinkedIn</a>
                    @endif
                    @if ($profile->github_url)
                        <a href="{{ $profile->github_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">GitHub</a>
                    @endif
                    @if ($profile->portfolio_url)
                        <a href="{{ $profile->portfolio_url }}" target="_blank" class="px-4 py-2 border rounded-lg text-sm font-medium hover:bg-gray-50">Portfolio</a>
                    @endif
                </div>
            @endif

            <div class="mt-10 p-6 bg-gray-50 rounded-xl border grid sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900">{{ __('talenma.inbox.compose_title') }}</h4>
                    <p class="mt-1 text-sm text-gray-600">{{ __('talenma.inbox.compose_desc') }}</p>
                    <form method="POST" action="{{ route('inbox.store') }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                        @csrf
                        <input type="hidden" name="talent_id" value="{{ $talent->id }}">
                        <div>
                            <x-input-label for="message-subject" :value="__('talenma.inbox.compose_subject')" />
                            <x-text-input id="message-subject" name="subject" class="mt-1 block w-full" required maxlength="255" :placeholder="__('talenma.inbox.compose_subject_placeholder')" />
                        </div>
                        <div>
                            <x-input-label for="message-body" :value="__('talenma.inbox.compose_body')" />
                            <textarea id="message-body" name="body" rows="4" required minlength="20" maxlength="5000" class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('talenma.inbox.compose_body_placeholder') }}"></textarea>
                        </div>
                        <div>
                            <x-input-label for="message-attachments" :value="__('talenma.inbox.attach')" />
                            <input id="message-attachments" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/*" class="mt-1 block w-full text-sm text-gray-600">
                            <p class="mt-1 text-xs text-gray-400">{{ __('talenma.inbox.attachments_hint') }}</p>
                        </div>
                        <x-primary-button>{{ __('talenma.inbox.compose_send') }}</x-primary-button>
                    </form>
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
