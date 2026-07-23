<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.jobs.title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ $org->displayName() }}</p>
            </div>
            <a href="{{ route('company.jobs.create') }}" class="inline-flex justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">
                {{ __('talenma.jobs.create') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @forelse ($jobs as $job)
            <a href="{{ route('company.jobs.show', $job) }}" class="block rounded-xl border bg-white p-5 hover:border-emerald-300 transition">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-gray-900">{{ $job->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($job->description), 140) }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $job->isPublished() ? 'bg-emerald-50 text-emerald-700' : ($job->isClosed() ? 'bg-gray-100 text-gray-600' : 'bg-amber-50 text-amber-800') }}">
                            {{ $job->statusLabel() }}
                        </span>
                        <span class="text-xs text-gray-500">{{ __('talenma.jobs.applications_count', ['count' => $job->applications_count]) }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-xl border bg-white p-8 text-center text-sm text-gray-500">
                {{ __('talenma.jobs.empty') }}
            </div>
        @endforelse

        <div>{{ $jobs->links() }}</div>
    </div>
</x-app-layout>
