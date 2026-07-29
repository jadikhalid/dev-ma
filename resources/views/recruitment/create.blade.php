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
                onclick="event.preventDefault(); const form = document.querySelector('#sourcing-request-card form'); if (form) { form.reset(); } window.location.href = this.href;"
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
            <form
                method="POST"
                action="{{ route('recruitment.store') }}"
                class="space-y-6"
                data-ajax
                data-loading-target="sourcing-request-card"
                data-error-message="{{ __('talenma.recruitment.error') }}"
                data-network-error-message="{{ __('talenma.recruitment.network_error') }}"
                novalidate
            >
                @csrf

                @if ($talent)
                    <input type="hidden" name="developer_user_id" value="{{ $talent->id }}">
                    <div class="p-4 bg-violet-50 rounded-xl text-sm border border-violet-100">
                        <strong>{{ __('talenma.talents.target') }}</strong>
                        {{ $talent->name }}
                        — {{ collect([$talent->profile?->professionLabel(), $talent->profile?->sectorLabel()])->filter()->implode(' - ') }}
                    </div>
                @endif

                <div>
                    <x-input-label for="role_title" :value="$talent ? __('talenma.recruitment.role_title_named') : __('talenma.recruitment.role_title_open')" />
                    <x-text-input
                        id="role_title"
                        name="role_title"
                        class="mt-1 block w-full"
                        :value="old('role_title', $talent ? __('talenma.recruitment.role_title_named_default', ['name' => $talent->name]) : '')"
                        maxlength="120"
                        required
                        data-required
                        data-required-message="{{ __('talenma.recruitment.role_title_required') }}"
                        data-min-length="5"
                        data-min-length-message="{{ __('talenma.recruitment.role_title_min') }}"
                        placeholder="{{ $talent ? __('talenma.recruitment.role_title_named_placeholder') : __('talenma.recruitment.role_title_open_placeholder') }}"
                    />
                    <x-input-error :messages="$errors->get('role_title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="need" :value="$talent ? __('talenma.recruitment.need_named') : __('talenma.recruitment.need_open')" />
                    <textarea
                        id="need"
                        name="need"
                        rows="6"
                        maxlength="5000"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                        required
                        data-required
                        data-required-message="{{ $talent ? __('talenma.recruitment.need_named_required') : __('talenma.recruitment.need_open_required') }}"
                        data-min-length="50"
                        data-min-length-message="{{ __('talenma.recruitment.need_min') }}"
                        placeholder="{{ $talent ? __('talenma.recruitment.need_named_placeholder') : __('talenma.recruitment.need_open_placeholder') }}"
                    >{{ old('need') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.recruitment.need_hint') }}</p>
                    <x-input-error :messages="$errors->get('need')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <button
                        type="button"
                        class="order-2 sm:order-1 inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
                        onclick="const form = this.closest('form'); if (form) { form.reset(); }"
                    >
                        {{ __('talenma.recruitment.cancel') }}
                    </button>
                    <x-primary-button type="submit" class="order-1 sm:order-2 justify-center">
                        {{ $talent
                            ? __('talenma.recruitment.send_named')
                            : __('talenma.recruitment.send_open') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
