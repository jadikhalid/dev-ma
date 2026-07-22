@php
    $user = Auth::user();
    $defaultName = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->name;
    $defaultSubject = __('talenma.services.accompagnement_subject_default');
    $defaultBody = __('talenma.services.accompagnement_body_default');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.services.title') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500 max-w-3xl">{{ __('talenma.services.subtitle_company') }}</p>
            </div>
            <a
                href="#accompagnement-form"
                class="inline-flex shrink-0 items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 scroll-smooth"
            >
                {{ __('talenma.services.cta_request') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
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

        <section
            id="accompagnement-form"
            class="relative scroll-mt-24 rounded-2xl border bg-white p-6 sm:p-8"
        >
            <h3 class="text-lg font-semibold text-gray-900">{{ __('talenma.services.accompagnement_title') }}</h3>
            <p class="mt-2 text-sm text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3">
                {{ __('talenma.services.accompagnement_free') }}
            </p>

            <form
                method="POST"
                action="{{ route('company.accompagnement.store') }}"
                class="mt-6 space-y-4"
                data-ajax
                data-loading-target="accompagnement-form"
                data-error-message="{{ __('talenma.services.accompagnement_error') }}"
                novalidate
            >
                @csrf

                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="grid sm:grid-cols-[7rem_1fr] gap-2 sm:gap-0 border-b border-gray-100 px-4 py-3 bg-slate-50/80">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 self-center">{{ __('talenma.services.accompagnement_to') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ __('talenma.services.accompagnement_to_value') }}</span>
                    </div>
                    <div class="grid sm:grid-cols-[7rem_1fr] gap-2 sm:gap-0 border-b border-gray-100 px-4 py-3">
                        <label for="requester_name" class="text-xs font-semibold uppercase tracking-wide text-gray-500 self-center">{{ __('talenma.services.accompagnement_from') }}</label>
                        <input
                            id="requester_name"
                            name="requester_name"
                            type="text"
                            value="{{ old('requester_name', $defaultName) }}"
                            required
                            data-required
                            data-required-message="{{ __('talenma.services.accompagnement_name_required') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div class="grid sm:grid-cols-[7rem_1fr] gap-2 sm:gap-0 border-b border-gray-100 px-4 py-3">
                        <label for="accompagnement_subject" class="text-xs font-semibold uppercase tracking-wide text-gray-500 self-center">{{ __('talenma.services.accompagnement_subject') }}</label>
                        <input
                            id="accompagnement_subject"
                            name="subject"
                            type="text"
                            value="{{ old('subject') }}"
                            placeholder="{{ $defaultSubject }}"
                            required
                            data-required
                            data-required-message="{{ __('talenma.services.accompagnement_subject_required') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"
                        >
                    </div>
                    <div class="px-4 py-3">
                        <label for="accompagnement_body" class="sr-only">{{ __('talenma.services.accompagnement_body') }}</label>
                        <textarea
                            id="accompagnement_body"
                            name="body"
                            rows="12"
                            placeholder="{{ $defaultBody }}"
                            required
                            data-required
                            data-required-message="{{ __('talenma.services.accompagnement_body_required') }}"
                            data-min-length="20"
                            data-min-length-message="{{ __('talenma.services.accompagnement_body_min') }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 font-sans placeholder:text-gray-400"
                        >{{ old('body') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-primary-button type="submit">{{ __('talenma.services.accompagnement_send') }}</x-primary-button>
                    <a href="{{ route('inbox.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('talenma.nav.messages') }} →</a>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
