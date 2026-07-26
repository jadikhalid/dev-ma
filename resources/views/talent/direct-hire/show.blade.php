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
    $showRounds = $directHire->rounds->isNotEmpty()
        || $directHire->status === \App\Models\DirectHireRequest::STATUS_IN_PROCESS;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.show_title') }}</h2>
                <p class="text-sm text-gray-500 truncate">{{ __('talenma.direct_hire.title_prefix') }} {{ $directHire->shortSubject() }}</p>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.with_company', ['name' => $directHire->companyDisplayName()]) }}</p>
            </div>
            <div class="inline-flex items-center gap-2 shrink-0">
                <span class="text-sm text-gray-500">{{ __('talenma.direct_hire.status_prefix') }}</span>
                <span class="inline-flex flex-wrap items-center gap-x-1.5 gap-y-0.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $tone }}">
                    <span>{{ $directHire->statusLabel() }}</span>
                    @if ($progress = $directHire->progressLabel())
                        <span class="opacity-40" aria-hidden="true">·</span>
                        <span class="font-medium">{{ $progress }}</span>
                    @endif
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div class="space-y-5 min-w-0">
                {{-- Proposition --}}
                <section class="bg-white rounded-2xl border overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.proposal') }}</h3>
                        <p class="text-xs text-slate-400">{{ $directHire->created_at?->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <blockquote class="relative overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 via-white to-indigo-50/50 px-5 py-5 sm:px-6 sm:py-6 shadow-sm ring-1 ring-slate-900/5">
                            <span class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500" aria-hidden="true"></span>
                            <p class="select-none pointer-events-none absolute top-3 right-4 text-6xl leading-none font-serif text-indigo-200/80" aria-hidden="true">„</p>
                            <p class="relative text-[0.95rem] sm:text-base text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $directHire->message }}</p>
                        </blockquote>

                        @if (filled($directHire->talent_decision_note))
                            <div class="rounded-lg bg-slate-50/80 border border-slate-100 px-3.5 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.your_note') }}</p>
                                <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $directHire->talent_decision_note }}</p>
                            </div>
                        @endif

                        @if (filled($directHire->closure_note) && $directHire->isTerminal())
                            <div class="rounded-lg bg-slate-50/80 border border-slate-100 px-3.5 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('talenma.direct_hire.closure_note_label') }}</p>
                                <p class="mt-1 text-sm text-slate-800 whitespace-pre-line">{{ $directHire->closure_note }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Décision --}}
                @if ($canDecide)
                    <section class="bg-white rounded-2xl border overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.decide_title') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('talenma.direct_hire.decide_subtitle') }}</p>
                        </div>
                        <form method="POST" action="{{ route('talent.direct-hire.decide', $directHire) }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="talent_decision_note" :value="__('talenma.direct_hire.decision_note')" />
                                <textarea
                                    id="talent_decision_note"
                                    name="talent_decision_note"
                                    rows="3"
                                    maxlength="2000"
                                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('talenma.direct_hire.decision_note_placeholder') }}"
                                >{{ old('talent_decision_note') }}</textarea>
                                <x-input-error :messages="$errors->get('talent_decision_note')" class="mt-2" />
                                <x-input-error :messages="$errors->get('decision')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <button type="submit" name="decision" value="accept" class="inline-flex justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                                    {{ __('talenma.direct_hire.decide_accept') }}
                                </button>
                                <button type="submit" name="decision" value="defer" class="inline-flex justify-center px-4 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600">
                                    {{ __('talenma.direct_hire.decide_defer') }}
                                </button>
                                <button type="submit" name="decision" value="decline" class="inline-flex justify-center px-4 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-lg hover:bg-rose-700">
                                    {{ __('talenma.direct_hire.decide_decline') }}
                                </button>
                            </div>
                        </form>
                    </section>
                @endif

                {{-- Étapes --}}
                @if ($showRounds)
                    <section class="bg-white rounded-2xl border overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.direct_hire.rounds_title') }}</h3>
                        </div>
                        <div class="p-5">
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
                                <div @class([
                                    'relative pl-6',
                                    'pb-5' => ! $loop->last,
                                ])>
                                    <span @class([
                                        'absolute left-0 top-1.5 h-2.5 w-2.5 rounded-full ring-4',
                                        'bg-gray-400 ring-gray-100' => $round->isCancelled(),
                                        'bg-indigo-500 ring-indigo-50' => ! $round->isCancelled(),
                                    ])></span>
                                    @unless ($loop->last)
                                        <span class="absolute left-[4px] top-4 bottom-0 w-px bg-gray-200"></span>
                                    @endunless
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span @class([
                                            'text-sm font-semibold',
                                            'text-gray-500 line-through' => $round->isCancelled(),
                                            'text-gray-900' => ! $round->isCancelled(),
                                        ])>
                                            {{ __('talenma.direct_hire.round_n', ['n' => $round->position]) }} — {{ $round->title }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $roundTone }}">
                                            {{ $round->statusLabel() }}
                                        </span>
                                    </div>
                                    @if ($round->scheduled_at)
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ __('talenma.direct_hire.round_scheduled_at') }} : {{ $round->scheduled_at->translatedFormat('d M Y H:i') }}
                                        </p>
                                    @endif
                                    @if (filled($round->meeting_url))
                                        <p class="mt-1 text-xs">
                                            <span class="text-gray-500">{{ __('talenma.direct_hire.round_meeting_url') }} :</span>
                                            <a href="{{ $round->meeting_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 underline underline-offset-2 break-all">
                                                {{ __('talenma.direct_hire.round_meeting_url_open') }}
                                            </a>
                                        </p>
                                    @endif
                                    @if (filled($round->company_note))
                                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $round->company_note }}</p>
                                    @endif
                                    @if ($round->isCancelled() && filled($round->cancellation_reason))
                                        <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                            <p class="text-xs font-semibold text-gray-600">{{ __('talenma.direct_hire.round_cancellation_reason_label') }}</p>
                                            <p class="mt-1 text-sm text-gray-800 whitespace-pre-line">{{ $round->cancellation_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.rounds_empty_talent') }}</p>
                            @endforelse
                        </div>
                    </section>
                @endif
            </div>

            <aside class="min-w-0 w-full lg:self-stretch">
                @include('direct-hire._chat', ['directHire' => $directHire, 'sidebar' => true])
            </aside>
        </div>
    </div>
</x-app-layout>
