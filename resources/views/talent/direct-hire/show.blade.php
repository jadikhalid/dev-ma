@php
    $tone = match ($directHire->statusTone()) {
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
    $canDecide = in_array($directHire->status, [
        \App\Models\DirectHireRequest::STATUS_PENDING_RESPONSE,
        \App\Models\DirectHireRequest::STATUS_DEFERRED,
    ], true);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold truncate">{{ $directHire->subject }}</h2>
                <p class="text-sm text-gray-500">{{ $directHire->companyDisplayName() }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $tone }}">
                {{ $directHire->statusLabel() }}
            </span>
        </div>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6 space-y-5">
        <div class="bg-white rounded-2xl border p-6 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.proposal') }}</h3>
                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $directHire->message }}</p>
                <p class="mt-2 text-xs text-gray-500">{{ $directHire->created_at?->translatedFormat('d M Y H:i') }}</p>
            </div>

            @if (filled($directHire->talent_decision_note))
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.direct_hire.your_note') }}</p>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $directHire->talent_decision_note }}</p>
                </div>
            @endif

            @if (filled($directHire->closure_note) && $directHire->isTerminal())
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $directHire->closure_note }}</p>
                </div>
            @endif

            @if ($directHire->conversation_id)
                <a href="{{ route('inbox.show', $directHire->conversation_id) }}" class="inline-flex items-center px-4 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                    {{ __('talenma.direct_hire.open_thread') }}
                </a>
            @endif
        </div>

        @if ($canDecide)
            <div class="bg-white rounded-2xl border p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.direct_hire.decide_title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('talenma.direct_hire.decide_subtitle') }}</p>

                <form method="POST" action="{{ route('talent.direct-hire.decide', $directHire) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="talent_decision_note" :value="__('talenma.direct_hire.decision_note')" />
                        <textarea id="talent_decision_note" name="talent_decision_note" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.decision_note_placeholder') }}">{{ old('talent_decision_note') }}</textarea>
                        <x-input-error :messages="$errors->get('talent_decision_note')" class="mt-2" />
                        <x-input-error :messages="$errors->get('decision')" class="mt-2" />
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="decision" value="accept" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                            {{ __('talenma.direct_hire.decide_accept') }}
                        </button>
                        <button type="submit" name="decision" value="defer" class="inline-flex px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600">
                            {{ __('talenma.direct_hire.decide_defer') }}
                        </button>
                        <button type="submit" name="decision" value="decline" class="inline-flex px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700">
                            {{ __('talenma.direct_hire.decide_decline') }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if ($directHire->rounds->isNotEmpty() || $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS)
            <div class="bg-white rounded-2xl border p-6 space-y-4">
                <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.direct_hire.rounds_title') }}</h3>
                @forelse ($directHire->rounds as $round)
                    @php
                        $roundTone = match ($round->statusTone()) {
                            'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
                            'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                            'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
                            'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <div class="rounded-xl border border-gray-100 px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.round_n', ['n' => $round->position]) }} — {{ $round->title }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $roundTone }}">{{ $round->statusLabel() }}</span>
                        </div>
                        @if ($round->scheduled_at)
                            <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.round_scheduled_at') }} : {{ $round->scheduled_at->translatedFormat('d M Y H:i') }}</p>
                        @endif
                        @if (filled($round->company_note))
                            <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $round->company_note }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.rounds_empty_talent') }}</p>
                @endforelse
            </div>
        @endif
    </div>
</x-app-layout>
