<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $job->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $job->companyProfile?->displayName() }}
                @if ($job->locationLabel() !== '')
                    · {{ $job->locationLabel() }}
                @endif
                @if ($job->remote_ok)
                    · {{ __('talenma.jobs.remote') }}
                @endif
            </p>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <article class="rounded-2xl border bg-white p-6 sm:p-8 space-y-3">
            <p class="text-sm text-gray-500">{{ $job->contractTypeLabel() }}</p>
            <div class="text-gray-800 whitespace-pre-wrap text-sm leading-relaxed">{{ $job->description }}</div>
        </article>

        @if ($application)
            <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm text-indigo-900">
                {{ __('talenma.jobs.your_application', ['status' => $application->statusLabel()]) }}
            </div>
        @else
            <div id="talent-job-apply-card" class="relative rounded-2xl border bg-white p-6 sm:p-8">
                <form
                    method="POST"
                    action="{{ route('talent.jobs.apply', $job) }}"
                    class="space-y-4"
                    data-ajax
                    data-loading-target="talent-job-apply-card"
                    data-error-message="{{ __('talenma.jobs.save_error') }}"
                    data-network-error-message="{{ __('talenma.jobs.network_error') }}"
                    novalidate
                >
                    @csrf
                    <h3 class="text-base font-semibold text-gray-900">{{ __('talenma.jobs.apply') }}</h3>
                    <div>
                        <x-input-label for="cover_message" :value="__('talenma.jobs.cover_message')" />
                        <textarea
                            id="cover_message"
                            name="cover_message"
                            rows="5"
                            maxlength="2000"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                            placeholder="{{ __('talenma.jobs.cover_placeholder') }}"
                        >{{ old('cover_message') }}</textarea>
                        <x-input-error :messages="$errors->get('cover_message')" class="mt-2" />
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <x-primary-button type="submit">{{ __('talenma.jobs.submit_application') }}</x-primary-button>
                        <button
                            type="button"
                            data-reset
                            class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >{{ __('talenma.jobs.cancel') }}</button>
                    </div>
                </form>
            </div>
        @endif

        <a href="{{ route('talent.jobs.index') }}" class="inline-flex text-sm font-medium text-indigo-700 hover:text-indigo-900">← {{ __('talenma.jobs.back') }}</a>
    </div>
</x-app-layout>
