<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">
                    {{ $talent
                        ? __('talenma.recruitment.title_named')
                        : __('talenma.recruitment.title_open') }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $talent
                        ? __('talenma.recruitment.subtitle_named', ['name' => $talent->name])
                        : __('talenma.recruitment.subtitle_open') }}
                </p>
            </div>
            <a
                href="{{ route('company.search') }}"
                class="inline-flex shrink-0 items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50"
                onclick="event.preventDefault(); const form = document.querySelector('#sourcing-request-form'); if (form) { form.reset(); } window.location.href = this.href;"
            >
                {{ __('talenma.recruitment.back_to_search') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6">
        <div
            id="sourcing-request-card"
            class="relative bg-white rounded-2xl border p-6 sm:p-8"
        >
            @include('recruitment._request-form', [
                'talent' => $talent,
                'embed' => false,
                'formId' => 'sourcing-request-form',
                'loadingTarget' => 'sourcing-request-card',
            ])
        </div>
    </div>
</x-app-layout>
