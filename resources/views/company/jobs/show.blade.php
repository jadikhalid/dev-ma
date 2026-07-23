<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ $job->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $job->statusLabel() }}
                    @if ($job->locationLabel() !== '')
                        · {{ $job->locationLabel() }}
                    @endif
                    @if ($job->remote_ok)
                        · {{ __('talenma.jobs.remote') }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('company.jobs.edit', $job) }}" class="inline-flex px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.jobs.edit') }}</a>
                @if (! $job->isPublished())
                    <form method="POST" action="{{ route('company.jobs.publish', $job) }}">@csrf
                        <button type="submit" class="inline-flex px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700">{{ __('talenma.jobs.publish') }}</button>
                    </form>
                @endif
                @if (! $job->isClosed())
                    <form method="POST" action="{{ route('company.jobs.close', $job) }}">@csrf
                        <button type="submit" class="inline-flex px-4 py-2 border border-amber-300 text-amber-800 text-sm font-semibold rounded-lg hover:bg-amber-50">{{ __('talenma.jobs.close') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <article class="rounded-2xl border bg-white p-6 sm:p-8 space-y-3">
            <p class="text-sm text-gray-500">{{ $job->contractTypeLabel() }}</p>
            <div class="prose prose-sm max-w-none text-gray-800 whitespace-pre-wrap">{{ $job->description }}</div>
        </article>

        <section class="rounded-2xl border bg-white p-6 sm:p-8 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.jobs.applications') }}</h3>

            @forelse ($job->applications as $application)
                <div class="border rounded-xl p-4 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $application->talent?->publicDisplayName() ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $application->submitted_at?->translatedFormat('d M Y, H:i') }}</p>
                        </div>
                        <form method="POST" action="{{ route('company.jobs.applications.update', [$job, $application]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="border-gray-300 rounded-lg text-sm">
                                @foreach (\App\Models\JobApplication::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($application->status === $status)>{{ __('talenma.jobs.application_status_'.$status) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 text-sm font-semibold text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-50">{{ __('talenma.jobs.save_status') }}</button>
                        </form>
                    </div>
                    @if ($application->cover_message)
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $application->cover_message }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">{{ __('talenma.jobs.applications_empty') }}</p>
            @endforelse
        </section>

        <a href="{{ route('company.jobs.index') }}" class="inline-flex text-sm font-medium text-emerald-700 hover:text-emerald-900">← {{ __('talenma.jobs.back') }}</a>
    </div>
</x-app-layout>
