<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold">{{ __('talenma.recruitment.title') }}</h2>
            <p class="text-sm text-gray-500">
                {{ $talent
                    ? __('talenma.recruitment.subtitle_talent', ['name' => $talent->name])
                    : __('talenma.recruitment.subtitle_general') }}
            </p>
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
                novalidate
            >
                @csrf

                @if ($talent)
                    <input type="hidden" name="developer_user_id" value="{{ $talent->id }}">
                    <div class="p-4 bg-indigo-50 rounded-xl text-sm">
                        <strong>{{ __('talenma.talents.target') }}</strong>
                        {{ $talent->name }}
                        — {{ collect([$talent->profile?->professionLabel(), $talent->profile?->sectorLabel()])->filter()->implode(' - ') }}
                    </div>
                @endif

                <div>
                    <x-input-label for="role_title" :value="__('talenma.recruitment.role_title')" />
                    <x-text-input
                        id="role_title"
                        name="role_title"
                        class="mt-1 block w-full"
                        :value="old('role_title', $talent ? __('talenma.recruitment.role_title_talent_default', ['name' => $talent->name]) : '')"
                        maxlength="120"
                        required
                        data-required
                        data-required-message="{{ __('talenma.recruitment.role_title_required') }}"
                        data-min-length="5"
                        data-min-length-message="{{ __('talenma.recruitment.role_title_min') }}"
                        placeholder="{{ __('talenma.recruitment.role_title_placeholder') }}"
                    />
                    <x-input-error :messages="$errors->get('role_title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="sector" :value="__('talenma.recruitment.sector')" />
                    <select
                        id="sector"
                        name="sector"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                    >
                        <option value="">{{ __('talenma.recruitment.sector_placeholder') }}</option>
                        @foreach ($professionSectors as $sectorOption)
                            <option
                                value="{{ $sectorOption['slug'] }}"
                                @selected(old('sector') === $sectorOption['slug'])
                            >{{ $sectorOption['name'] }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.recruitment.sector_hint') }}</p>
                    <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="need" :value="__('talenma.recruitment.need')" />
                    <textarea
                        id="need"
                        name="need"
                        rows="6"
                        maxlength="5000"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                        required
                        data-required
                        data-required-message="{{ __('talenma.recruitment.need_required') }}"
                        data-min-length="50"
                        data-min-length-message="{{ __('talenma.recruitment.need_min') }}"
                        placeholder="{{ __('talenma.recruitment.need_placeholder') }}"
                    >{{ old('need') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.recruitment.need_hint') }}</p>
                    <x-input-error :messages="$errors->get('need')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <a href="{{ route('dashboard') }}" class="order-2 sm:order-1 text-center px-4 py-2.5 text-sm text-gray-600 hover:text-gray-900">
                        {{ __('talenma.recruitment.cancel') }}
                    </a>
                    <x-primary-button type="submit" class="order-1 sm:order-2 justify-center">
                        {{ __('talenma.recruitment.send') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
