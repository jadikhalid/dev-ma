<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold">{{ __('talenma.direct_hire.create_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.direct_hire.create_subtitle', ['name' => $talent->name]) }}</p>
            </div>
            <a
                href="{{ route('admin.direct-hire.index') }}"
                class="inline-flex shrink-0 items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
            >{{ __('talenma.direct_hire.admin_back_to_list') }}</a>
        </div>
    </x-slot>

    <div class="py-10 max-w-2xl mx-auto px-4 sm:px-6">
        <div id="direct-hire-create-card" class="relative bg-white rounded-2xl border p-6 sm:p-8">
            <div class="p-4 bg-indigo-50 rounded-xl text-sm mb-6">
                <strong>{{ __('talenma.talents.target') }}</strong>
                {{ $talent->name }}
                — {{ collect([$talent->profile?->professionLabel(), $talent->profile?->sectorLabel()])->filter()->implode(' - ') }}
            </div>

            <form
                id="direct-hire-create-form"
                method="POST"
                action="{{ route('admin.direct-hire.store', $talent) }}"
                class="space-y-6"
                data-ajax
                data-loading-target="direct-hire-create-card"
                data-error-message="{{ __('talenma.direct_hire.create_error') }}"
                data-network-error-message="{{ __('talenma.direct_hire.network_error') }}"
                x-data="{ origin: @js(old('hire_origin', \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL)) }"
                novalidate
            >
                @csrf

                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ __('talenma.direct_hire.origin_label') }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ __('talenma.direct_hire.origin_hint') }}</p>
                    <div class="mt-3 space-y-2">
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50">
                            <input
                                type="radio"
                                name="hire_origin"
                                value="{{ \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL }}"
                                class="mt-1 text-indigo-600"
                                x-model="origin"
                                @checked(old('hire_origin', \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL) === \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL)
                            >
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">{{ __('talenma.direct_hire.origin_staff_internal') }}</span>
                                <span class="block text-xs text-slate-500">{{ __('talenma.direct_hire.origin_staff_internal_hint') }}</span>
                                @if ($staffInternalOpen)
                                    <span class="mt-1 block text-xs font-medium text-amber-700">{{ __('talenma.direct_hire.error_staff_internal_open') }}</span>
                                @endif
                            </span>
                        </label>
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-slate-50">
                            <input
                                type="radio"
                                name="hire_origin"
                                value="{{ \App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF }}"
                                class="mt-1 text-indigo-600"
                                x-model="origin"
                                @checked(old('hire_origin') === \App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)
                            >
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">{{ __('talenma.direct_hire.origin_staff_on_behalf') }}</span>
                                <span class="block text-xs text-slate-500">{{ __('talenma.direct_hire.origin_staff_on_behalf_hint') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div x-show="origin === @js(\App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)" x-cloak>
                    <x-input-label for="company_id" :value="__('talenma.direct_hire.beneficiary_company')" />
                    <select
                        id="company_id"
                        name="company_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        x-bind:required="origin === @js(\App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)"
                    >
                        <option value="">{{ __('talenma.direct_hire.admin_select_company') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>
                                {{ $company->name }} — {{ $company->email }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.direct_hire.beneficiary_company_hint') }}</p>
                </div>

                <div>
                    <x-input-label for="subject" :value="__('talenma.direct_hire.subject')" />
                    <x-text-input
                        id="subject"
                        name="subject"
                        class="mt-1 block w-full"
                        :value="old('subject')"
                        :placeholder="__('talenma.direct_hire.subject_placeholder')"
                        maxlength="120"
                        data-required
                        data-required-message="{{ __('talenma.direct_hire.subject_required') }}"
                        data-min-length="5"
                        data-min-length-message="{{ __('talenma.direct_hire.subject_min') }}"
                    />
                </div>

                <div>
                    <x-input-label for="message" :value="__('talenma.direct_hire.message')" />
                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        maxlength="5000"
                        data-required
                        data-required-message="{{ __('talenma.direct_hire.message_required') }}"
                        data-min-length="40"
                        data-min-length-message="{{ __('talenma.direct_hire.message_min') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('talenma.direct_hire.message_placeholder') }}"
                    >{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.direct_hire.message_hint') }}</p>
                    <p class="mt-1 text-xs text-indigo-700">{{ __('talenma.direct_hire.talent_masked_hint') }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-primary-button type="submit">{{ __('talenma.direct_hire.send') }}</x-primary-button>
                    <a
                        href="{{ route('admin.direct-hire.index') }}"
                        class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >{{ __('talenma.direct_hire.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
