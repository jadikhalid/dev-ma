<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                    </svg>
                </span>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('talenma.cv_builder.page_title') }}
                </h1>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                {{ __('talenma.cv_builder.back_dashboard') }}
            </a>
        </div>
    </x-slot>

    <div
        class="py-6 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8"
        x-data="talentCvBuilder(@js([
            'data' => $draft->data,
            'template' => $draft->template,
            'locale' => $draft->locale,
            'templates' => $templates,
            'profileAvatarUrl' => $profileAvatarUrl,
            'urls' => [
                'save' => route('talent.cv-builder.update'),
                'preview' => route('talent.cv-builder.preview'),
                'export' => route('talent.cv-builder.export'),
            ],
            'messages' => [
                'saved' => __('talenma.cv_builder.saved'),
                'save_error' => __('talenma.common.save_error'),
                'export' => __('talenma.cv_builder.export_pdf'),
                'photo_too_large' => __('talenma.cv_builder.form.photo_too_large'),
            ],
        ]))"
    >
        <div class="mb-4 flex flex-wrap items-center justify-end gap-2">
            <label class="text-xs font-semibold text-gray-500 uppercase">{{ __('talenma.cv_builder.template_label') }}</label>
            <select x-model="template" @change="onSettingsChange()" class="rounded-lg border-gray-300 text-sm">
                <template x-for="(label, key) in templates" :key="key">
                    <option :value="key" x-text="label"></option>
                </template>
            </select>
            <label class="text-xs font-semibold text-gray-500 uppercase ml-2">{{ __('talenma.cv_builder.locale_label') }}</label>
            <select x-model="locale" @change="onSettingsChange()" class="rounded-lg border-gray-300 text-sm">
                <option value="fr">FR</option>
                <option value="en">EN</option>
            </select>
            <button
                type="button"
                @click="exportPdf()"
                :disabled="exporting"
                class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition"
            >
                <span x-text="messages.export"></span>
            </button>
        </div>

        {{-- Onglets mobile : Rédaction / Aperçu --}}
        <div class="xl:hidden mb-4 grid grid-cols-2 gap-1 p-1 bg-gray-100 rounded-xl" role="tablist" aria-label="{{ __('talenma.cv_builder.mobile_tabs_label') }}">
            <button
                type="button"
                role="tab"
                :aria-selected="mobilePanel === 'edit'"
                @click="showMobilePanel('edit')"
                class="py-2.5 text-sm rounded-lg transition"
                :class="mobilePanel === 'edit' ? 'bg-white shadow-sm text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-800'"
            >
                {{ __('talenma.cv_builder.edit_title') }}
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="mobilePanel === 'preview'"
                @click="showMobilePanel('preview')"
                class="py-2.5 text-sm rounded-lg transition"
                :class="mobilePanel === 'preview' ? 'bg-white shadow-sm text-indigo-700 font-semibold' : 'text-gray-600 hover:text-gray-800'"
            >
                {{ __('talenma.cv_builder.preview_title') }}
            </button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 xl:h-[calc(100vh-11.5rem)] xl:min-h-[640px]">
            {{-- Rédaction --}}
            <div
                class="flex-col bg-white rounded-2xl border overflow-hidden shadow-sm min-h-[calc(100vh-15rem)] xl:min-h-0 xl:h-full"
                :class="mobilePanel === 'edit' ? 'flex' : 'hidden xl:flex'"
                role="tabpanel"
                :aria-hidden="mobilePanel !== 'edit'"
            >
                <div class="shrink-0 hidden xl:flex items-center px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-700">{{ __('talenma.cv_builder.edit_title') }}</h2>
                </div>
                <div class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-5 sm:p-6 space-y-6">
                    @include('talent.cv-builder.partials.form')
                </div>
            </div>

            {{-- Aperçu --}}
            <div
                class="flex-col bg-white rounded-2xl border overflow-hidden shadow-sm min-h-[calc(100vh-15rem)] xl:min-h-0 xl:h-full"
                :class="mobilePanel === 'preview' ? 'flex' : 'hidden xl:flex'"
                role="tabpanel"
                :aria-hidden="mobilePanel !== 'preview'"
            >
                <div class="shrink-0 hidden xl:flex items-center px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-700">{{ __('talenma.cv_builder.preview_title') }}</h2>
                </div>
                <div class="flex-1 min-h-0 bg-gray-100 p-3">
                    <iframe
                        x-ref="previewFrame"
                        class="w-full h-full min-h-[calc(100vh-18rem)] xl:min-h-[480px] bg-white rounded-lg shadow-sm border-0 block"
                        title="{{ __('talenma.cv_builder.preview_title') }}"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
