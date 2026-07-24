<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.talent_index_title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.talent_index_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl border p-6">
            @if ($requests->isEmpty())
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.talent_index_empty') }}</p>
            @else
                <ul class="space-y-3">
                    @foreach ($requests as $req)
                        @php
                            $tone = match ($req->statusTone()) {
                                'amber' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'violet' => 'bg-violet-50 text-violet-800 border-violet-200',
                                'sky' => 'bg-sky-50 text-sky-800 border-sky-200',
                                'emerald' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'rose' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                        @endphp
                        <li>
                            <a href="{{ route('talent.direct-hire.show', $req) }}" class="block rounded-xl border border-gray-100 px-4 py-3 hover:bg-gray-50 transition">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-gray-900">{{ $req->subject }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $tone }}">{{ $req->statusLabel() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600">{{ $req->companyDisplayName() }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $req->created_at?->translatedFormat('d M Y') }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
