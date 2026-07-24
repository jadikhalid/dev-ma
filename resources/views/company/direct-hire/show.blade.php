@php
    $tone = match ($directHire->statusTone()) {
        'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
        'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
        'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
        'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };
    $talent = $directHire->talent;
    $canManageRounds = $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS;
    $canWithdraw = $directHire->isOpen();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold truncate">{{ $directHire->subject }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.with_talent', ['name' => $talent?->name]) }}</p>
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

            @if ($directHire->talent_decision_at)
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.direct_hire.talent_decision') }}</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $directHire->statusLabel() }} — {{ $directHire->talent_decision_at->translatedFormat('d M Y H:i') }}</p>
                    @if (filled($directHire->talent_decision_note))
                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $directHire->talent_decision_note }}</p>
                    @endif
                </div>
            @endif

            @if (filled($directHire->closure_note) && $directHire->isTerminal())
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
                    <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $directHire->closure_note }}</p>
                </div>
            @endif

            <div class="flex flex-wrap gap-3">
                @if ($directHire->conversation_id)
                    <a href="{{ route('inbox.show', $directHire->conversation_id) }}" class="inline-flex items-center px-4 py-2 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                        {{ __('talenma.direct_hire.open_thread') }}
                    </a>
                @endif
                @if ($talent)
                    <a href="{{ route('company.talent.show', $talent) }}" class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('talenma.direct_hire.view_talent') }}
                    </a>
                @endif
            </div>
        </div>

        @if ($canManageRounds || $directHire->rounds->isNotEmpty())
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
                    <div class="rounded-xl border border-gray-100 p-4">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.round_n', ['n' => $round->position]) }} — {{ $round->title }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $roundTone }}">{{ $round->statusLabel() }}</span>
                        </div>

                        @if ($canManageRounds)
                            <form method="POST" action="{{ route('company.direct-hire.rounds.update', [$directHire, $round]) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <x-input-label :for="'round-title-'.$round->id" :value="__('talenma.direct_hire.round_title')" />
                                        <x-text-input :id="'round-title-'.$round->id" name="title" class="mt-1 block w-full" :value="old('title', $round->title)" maxlength="120" />
                                    </div>
                                    <div>
                                        <x-input-label :for="'round-status-'.$round->id" :value="__('talenma.direct_hire.round_status')" />
                                        <select id="round-status-{{ $round->id }}" name="status" class="mt-1 block w-full border-gray-300 rounded-lg text-sm">
                                            @foreach ($roundStatuses as $status)
                                                <option value="{{ $status }}" @selected(old('status', $round->status) === $status)>{{ __('talenma.direct_hire.round_status_'.$status) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label :for="'round-scheduled-'.$round->id" :value="__('talenma.direct_hire.round_scheduled_at')" />
                                    <input id="round-scheduled-{{ $round->id }}" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $round->scheduled_at?->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <x-input-label :for="'round-note-'.$round->id" :value="__('talenma.direct_hire.round_note')" />
                                    <textarea id="round-note-{{ $round->id }}" name="company_note" rows="2" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 text-sm">{{ old('company_note', $round->company_note) }}</textarea>
                                </div>
                                <x-primary-button>{{ __('talenma.direct_hire.round_save') }}</x-primary-button>
                            </form>
                        @else
                            @if ($round->scheduled_at)
                                <p class="text-xs text-gray-500">{{ __('talenma.direct_hire.round_scheduled_at') }} : {{ $round->scheduled_at->translatedFormat('d M Y H:i') }}</p>
                            @endif
                            @if (filled($round->company_note))
                                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $round->company_note }}</p>
                            @endif
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.rounds_empty') }}</p>
                @endforelse

                @if ($canManageRounds)
                    <form method="POST" action="{{ route('company.direct-hire.rounds.store', $directHire) }}" class="rounded-xl border border-dashed border-gray-200 p-4 space-y-3">
                        @csrf
                        <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.round_add') }}</p>
                        <div>
                            <x-input-label for="new-round-title" :value="__('talenma.direct_hire.round_title')" />
                            <x-text-input id="new-round-title" name="title" class="mt-1 block w-full" maxlength="120" required placeholder="{{ __('talenma.direct_hire.round_title_placeholder') }}" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="new-round-note" :value="__('talenma.direct_hire.round_note')" />
                            <textarea id="new-round-note" name="company_note" rows="2" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 text-sm"></textarea>
                        </div>
                        <x-primary-button>{{ __('talenma.direct_hire.round_add_btn') }}</x-primary-button>
                    </form>

                    <div class="grid sm:grid-cols-2 gap-4 pt-2">
                        <form method="POST" action="{{ route('company.direct-hire.close', $directHire) }}" class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="outcome" value="hired">
                            <p class="text-sm font-semibold text-emerald-900">{{ __('talenma.direct_hire.close_hired') }}</p>
                            <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                            <button type="submit" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">{{ __('talenma.direct_hire.close_hired_btn') }}</button>
                        </form>
                        <form method="POST" action="{{ route('company.direct-hire.close', $directHire) }}" class="rounded-xl border border-rose-100 bg-rose-50/40 p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="outcome" value="closed_negative">
                            <p class="text-sm font-semibold text-rose-900">{{ __('talenma.direct_hire.close_negative') }}</p>
                            <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                            <button type="submit" class="inline-flex px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700">{{ __('talenma.direct_hire.close_negative_btn') }}</button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        @if ($canWithdraw)
            <div class="bg-white rounded-2xl border p-6">
                <form method="POST" action="{{ route('company.direct-hire.withdraw', $directHire) }}" class="space-y-3" onsubmit="return confirm(@js(__('talenma.direct_hire.withdraw_confirm')))">
                    @csrf
                    <p class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.withdraw_title') }}</p>
                    <textarea name="closure_note" rows="2" maxlength="2000" class="block w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.direct_hire.closure_note_placeholder') }}"></textarea>
                    <button type="submit" class="inline-flex px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">{{ __('talenma.direct_hire.withdraw_btn') }}</button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
