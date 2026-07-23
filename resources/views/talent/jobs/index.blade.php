<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.talent_title') }}</h2>
        <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.talent_subtitle') }}</p>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @forelse ($jobs as $job)
            <a href="{{ route('talent.jobs.show', $job) }}" class="block rounded-xl border bg-white p-5 hover:border-indigo-300 transition">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">{{ $job->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $job->companyProfile?->displayName() }}</p>
                        <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($job->description), 140) }}</p>
                    </div>
                    <div class="text-xs text-gray-500 shrink-0">
                        @if (in_array($job->id, $appliedIds, true))
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">{{ __('talenma.jobs.applied_badge') }}</span>
                        @else
                            {{ $job->locationLabel() ?: '—' }}
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-xl border bg-white p-8 text-center text-sm text-gray-500">
                {{ __('talenma.jobs.talent_empty') }}
            </div>
        @endforelse

        <div>{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
