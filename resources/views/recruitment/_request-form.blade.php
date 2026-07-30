{{-- Open / named sourcing request form fields. Expects optional $talent. --}}
@php
    $talent = $talent ?? null;
    $embed = $embed ?? false;
    $formId = $formId ?? 'sourcing-request-form';
    $loadingTarget = $loadingTarget ?? 'sourcing-request-card';
@endphp

<form
    id="{{ $formId }}"
    method="POST"
    action="{{ route('recruitment.store') }}"
    class="space-y-6"
    data-ajax
    data-loading-target="{{ $loadingTarget }}"
    data-error-message="{{ __('talenma.recruitment.error') }}"
    data-network-error-message="{{ __('talenma.recruitment.network_error') }}"
    novalidate
>
    @csrf

    @if ($embed)
        <input type="hidden" name="embed" value="1">
    @endif

    @if ($talent)
        <input type="hidden" name="developer_user_id" value="{{ $talent->id }}">
        <div class="p-4 bg-violet-50 rounded-xl text-sm border border-violet-100">
            <strong>{{ __('talenma.talents.target') }}</strong>
            {{ $talent->name }}
            — {{ collect([$talent->profile?->professionLabel(), $talent->profile?->sectorLabel()])->filter()->implode(' - ') }}
        </div>
    @endif

    <div>
        <x-input-label for="{{ $formId }}-role_title" :value="$talent ? __('talenma.recruitment.role_title_named') : __('talenma.recruitment.role_title_open')" />
        <x-text-input
            id="{{ $formId }}-role_title"
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
        <x-input-label for="{{ $formId }}-need" :value="$talent ? __('talenma.recruitment.need_named') : __('talenma.recruitment.need_open')" />
        <textarea
            id="{{ $formId }}-need"
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
            @if ($embed)
                @click="closeCreate(); $el.closest('form')?.reset()"
            @else
                onclick="const form = this.closest('form'); if (form) { form.reset(); }"
            @endif
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
