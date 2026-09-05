@php
    $isEdit = $job->exists;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $isEdit ? __('talenma.jobs.edit_external') : __('talenma.jobs.create_external') }}
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.jobs.external_form_help') }}</p>
            </div>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                ← {{ __('talenma.jobs.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-white rounded-2xl border p-6 sm:p-8">
            <form
                method="POST"
                enctype="multipart/form-data"
                action="{{ $isEdit ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}"
                class="space-y-5"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-900">
                    {{ __('talenma.jobs.external_form_notice') }}
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <x-input-label for="external_company_name" :value="__('talenma.jobs.field_external_company')" />
                        <x-text-input
                            id="external_company_name"
                            name="external_company_name"
                            class="mt-1 block w-full"
                            :value="old('external_company_name', $job->external_company_name)"
                            maxlength="255"
                            required
                        />
                        <x-input-error :messages="$errors->get('external_company_name')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="external_apply_url" :value="__('talenma.jobs.field_external_apply_url')" />
                        <x-text-input
                            id="external_apply_url"
                            name="external_apply_url"
                            type="url"
                            class="mt-1 block w-full"
                            :value="old('external_apply_url', $job->external_apply_url)"
                            maxlength="2048"
                            placeholder="https://"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.jobs.external_apply_url_hint') }}</p>
                        <x-input-error :messages="$errors->get('external_apply_url')" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="external_company_logo" :value="__('talenma.jobs.field_external_logo')" />
                        @if ($job->advertiserLogoUrl())
                            <div class="mt-2 mb-3 flex items-center gap-3">
                                <img src="{{ $job->advertiserLogoUrl() }}" alt="" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="remove_external_company_logo" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ __('talenma.jobs.remove_external_logo') }}
                                </label>
                            </div>
                        @endif
                        <input
                            id="external_company_logo"
                            name="external_company_logo"
                            type="file"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                        <p class="mt-1 text-xs text-gray-500">{{ __('talenma.jobs.external_logo_hint') }}</p>
                        <x-input-error :messages="$errors->get('external_company_logo')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="title" :value="__('talenma.jobs.field_title')" />
                    <x-text-input
                        id="title"
                        name="title"
                        class="mt-1 block w-full"
                        :value="old('title', $job->title)"
                        maxlength="255"
                        required
                    />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('talenma.jobs.field_description')" />
                    <textarea
                        id="description"
                        name="description"
                        rows="8"
                        maxlength="10000"
                        required
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                    >{{ old('description', $job->description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('talenma.jobs.description_hint') }}</p>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="job-sector" :value="__('talenma.jobs.field_sector')" />
                    <select
                        id="job-sector"
                        name="sector"
                        required
                        class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('talenma.talent.sector_placeholder') }}</option>
                        @foreach ($professionSectors as $sectorOption)
                            <option value="{{ $sectorOption['slug'] }}" @selected(old('sector', $sectorSlug) === $sectorOption['slug'])>
                                {{ $sectorOption['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sector')" class="mt-2" />
                </div>

                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                    <a href="{{ route('admin.jobs.index') }}" class="inline-flex justify-center px-5 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 hover:bg-gray-50">{{ __('talenma.jobs.cancel') }}</a>
                    <x-primary-button type="submit">{{ __('talenma.jobs.save') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
