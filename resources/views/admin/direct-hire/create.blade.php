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
                x-data="adminDirectHireCreateForm({
                    origin: @js(old('hire_origin', \App\Models\DirectHireRequest::ORIGIN_STAFF_INTERNAL)),
                    onBehalf: @js(\App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF),
                    companySearchUrl: @js(route('admin.direct-hire.company-search')),
                    initialCompanyId: @js(old('company_id')),
                    initialCompanyLabel: @js(old('company_label', '')),
                    loadingLabel: @js(__('talenma.direct_hire.admin_company_search_loading')),
                    emptyLabel: @js(__('talenma.direct_hire.admin_company_search_empty')),
                    minChars: 2,
                })"
                @click.outside="closeCompanyResults()"
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
                                @change="onOriginChange()"
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
                                @change="onOriginChange()"
                                @checked(old('hire_origin') === \App\Models\DirectHireRequest::ORIGIN_STAFF_ON_BEHALF)
                            >
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">{{ __('talenma.direct_hire.origin_staff_on_behalf') }}</span>
                                <span class="block text-xs text-slate-500">{{ __('talenma.direct_hire.origin_staff_on_behalf_hint') }}</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div x-show="origin === onBehalf" x-cloak class="relative">
                    <x-input-label for="admin-dh-company-query" :value="__('talenma.direct_hire.beneficiary_company')" />
                    <input type="hidden" name="company_id" :value="selectedCompanyId ?? ''">
                    <input type="hidden" name="company_label" :value="selectedCompanyLabel || companyQuery">
                    <input
                        id="admin-dh-company-query"
                        type="search"
                        x-model="companyQuery"
                        x-ref="companyInput"
                        @input="onCompanyInput()"
                        @keydown="onCompanyKeydown($event)"
                        @focus="onCompanyFocus()"
                        placeholder="{{ __('talenma.direct_hire.admin_company_search_placeholder') }}"
                        maxlength="100"
                        autocomplete="off"
                        role="combobox"
                        aria-controls="admin-dh-company-listbox"
                        :aria-expanded="companyOpen"
                        aria-autocomplete="list"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        x-bind:required="origin === onBehalf && !selectedCompanyId"
                    >
                    <div
                        id="admin-dh-company-listbox"
                        x-show="companyOpen && (companyLoading || companyResults.length > 0 || (companyQuery.trim().length >= minChars && !companyLoading))"
                        x-cloak
                        role="listbox"
                        class="absolute z-30 mt-1 max-h-64 w-full overflow-auto rounded-xl border border-slate-200 bg-white py-1 shadow-lg ring-1 ring-slate-900/5"
                    >
                        <p x-show="companyLoading" class="px-3 py-2 text-sm text-slate-500" x-text="loadingLabel"></p>
                        <p
                            x-show="!companyLoading && companyResults.length === 0 && companyQuery.trim().length >= minChars"
                            class="px-3 py-2 text-sm text-slate-500"
                            x-text="emptyLabel"
                        ></p>
                        <template x-for="(item, index) in companyResults" :key="item.id">
                            <button
                                type="button"
                                role="option"
                                class="flex w-full flex-col items-start gap-0.5 px-3 py-2 text-left hover:bg-indigo-50"
                                :class="{ 'bg-indigo-50': index === companyActiveIndex }"
                                @mousedown.prevent="selectCompany(item)"
                                @mouseenter="companyActiveIndex = index"
                            >
                                <span class="text-sm font-semibold text-slate-900" x-text="item.label"></span>
                                <span class="text-xs text-slate-500" x-text="item.email"></span>
                            </button>
                        </template>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ __('talenma.direct_hire.beneficiary_company_hint') }}</p>
                    <p x-show="selectedCompanyId" x-cloak class="mt-1 text-xs text-emerald-700">
                        {{ __('talenma.direct_hire.admin_company_selected_prefix') }}
                        <span class="font-semibold" x-text="selectedCompanyLabel"></span>
                    </p>
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
