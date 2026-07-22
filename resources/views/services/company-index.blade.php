<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.services.title') }}</h2>
            <p class="mt-0.5 text-sm text-gray-500 max-w-3xl">{{ __('talenma.services.subtitle_company') }}</p>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($services as $service)
                <article class="flex h-full gap-3 rounded-xl border bg-white p-4 sm:p-5">
                    <span class="text-2xl leading-none shrink-0" aria-hidden="true">{{ $service->icon }}</span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-semibold text-gray-900 leading-snug">{{ $service->localized_title }}</h3>
                        @if (filled($service->localized_summary))
                            <p class="mt-1.5 text-sm text-gray-600 leading-relaxed">{{ $service->localized_summary }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-xl border bg-white px-5 py-4">
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.services.cta_title') }}</h3>
                <p class="mt-0.5 text-sm text-gray-600">{{ __('talenma.services.cta_desc') }}</p>
            </div>
            <a
                href="{{ route('recruitment.create') }}?mode=intermediary"
                class="inline-flex shrink-0 items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700"
            >
                {{ __('talenma.services.cta_request') }}
            </a>
        </div>
    </div>
</x-app-layout>
