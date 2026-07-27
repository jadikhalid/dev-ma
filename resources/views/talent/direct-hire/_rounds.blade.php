<section id="direct-hire-rounds" class="bg-white rounded-2xl border overflow-hidden">
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
