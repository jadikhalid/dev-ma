<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.create_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.create_subtitle', ['name' => $talent->name]) }}</p>
            </div>
            <a
                href="{{ route('company.search') }}"
                class="inline-flex shrink-0 items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
            >{{ __('talenma.direct_hire.cancel') }}</a>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl border p-6 sm:p-8">
            <div class="p-4 bg-indigo-50 rounded-xl text-sm mb-6">
                <strong>{{ __('talenma.talents.target') }}</strong>
                {{ $talent->name }}
                — {{ collect([$talent->profile?->professionLabel(), $talent->profile?->sectorLabel()])->filter()->implode(' - ') }}
            </div>

            <form
                id="direct-hire-create-form"
                method="POST"
                action="{{ route('company.direct-hire.store', $talent) }}"
                class="space-y-6"
            >
                @csrf

                <div>
                    <x-input-label for="subject" :value="__('talenma.direct_hire.subject')" />
                    <x-text-input
                        id="subject"
                        name="subject"
                        class="mt-1 block w-full"
                        :value="old('subject')"
                        :placeholder="__('talenma.direct_hire.subject_placeholder')"
                        maxlength="120"
                        required
                    />
                    <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" :value="__('talenma.direct_hire.message')" />
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        required
                        minlength="40"
                        maxlength="5000"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('talenma.direct_hire.message_placeholder') }}"
                    >{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.message_hint') }}</p>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    <x-input-error :messages="$errors->get('talent_id')" class="mt-2" />
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-primary-button>{{ __('talenma.direct_hire.send') }}</x-primary-button>
                    <button
                        type="button"
                        class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                        onclick="
                            const form = document.getElementById('direct-hire-create-form');
                            const subject = form.querySelector('#subject');
                            const message = form.querySelector('#message');
                            if (subject) subject.value = '';
                            if (message) message.value = '';
                        "
                    >{{ __('talenma.direct_hire.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
