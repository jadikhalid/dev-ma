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
            'templateOptions' => $templateOptions,
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
                'export_print_ready' => __('talenma.cv_builder.export_print_ready'),
                'export_popup_blocked' => __('talenma.cv_builder.export_popup_blocked'),
                'export_error' => __('talenma.cv_builder.export_error'),
                'photo_too_large' => __('talenma.cv_builder.form.photo_too_large'),
                'choose_template' => __('talenma.cv_builder.choose_template'),
                'selected_template' => __('talenma.cv_builder.selected_template'),
                'templates_prev' => __('talenma.cv_builder.templates_prev'),
                'templates_next' => __('talenma.cv_builder.templates_next'),
            ],
        ]))"
    >
        <div class="mb-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('talenma.cv_builder.template_label') }}</p>
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase">{{ __('talenma.cv_builder.locale_label') }}</label>
                    <select x-model="locale" @change="onSettingsChange()" class="rounded-lg border-gray-300 text-sm">
                        <option value="fr">FR</option>
                        <option value="en">EN</option>
                    </select>
                    <button
                        type="button"
                        @click="exportPdf()"
                        :disabled="exporting"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition"
                    >
                        <span x-text="messages.export"></span>
                    </button>
                </div>
            </div>

            <div class="cv-template-rail relative">
                <button
                    type="button"
                    class="cv-template-nav cv-template-nav--left"
                    :class="{ 'is-disabled': ! templateSliderCanPrev }"
                    :disabled="! templateSliderCanPrev"
                    :aria-label="messages.templates_prev"
                    @click="scrollTemplateSlider(-1)"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-ref="templateSlider"
                    class="cv-template-slider flex gap-3 sm:gap-3.5 overflow-x-auto overflow-y-hidden py-4 px-10 sm:px-12 scroll-smooth snap-x snap-mandatory"
                    role="radiogroup"
                    aria-label="{{ __('talenma.cv_builder.template_label') }}"
                    @scroll.passive="updateTemplateSliderNav()"
                    @click.capture="onTemplateSliderBackgroundClick($event)"
                >
                    <template x-for="option in templateOptions" :key="option.key">
                        <div
                            class="cv-template-card group relative shrink-0 w-[7.5rem] sm:w-[8.75rem] md:w-[9.25rem] snap-start rounded-xl border bg-white p-2 transition-[transform,box-shadow,border-color] duration-200 ease-out will-change-transform"
                            :data-template-key="option.key"
                            :class="{
                                'is-selected border-indigo-500 ring-2 ring-indigo-200 shadow-sm': template === option.key,
                                'is-previewed border-indigo-300 shadow-md': previewedTemplate === option.key && template !== option.key,
                                'border-gray-200': template !== option.key && previewedTemplate !== option.key,
                            }"
                            role="radio"
                            :aria-checked="template === option.key"
                            :aria-label="option.label"
                            @mouseenter="onTemplateCardEnter(option.key)"
                            @mouseleave="onTemplateCardLeave(option.key)"
                            @click="onTemplateCardTap(option.key, $event)"
                        >
                            <div class="relative aspect-[3/4] overflow-hidden rounded-lg bg-gray-100 ring-1 ring-black/5">
                                <img
                                    :src="templatePreviewSrc(option)"
                                    :alt="option.label"
                                    class="cv-template-card-image h-full w-full object-cover object-top transition-transform duration-200 ease-out"
                                    loading="lazy"
                                    decoding="async"
                                    draggable="false"
                                >

                                <div
                                    class="cv-template-card-overlay absolute inset-x-0 bottom-0 flex justify-center bg-gradient-to-t from-black/70 via-black/35 to-transparent px-1.5 pb-2 pt-8 pointer-events-none"
                                >
                                    <template x-if="template === option.key">
                                        <span class="cv-template-banner inline-flex max-w-full items-center rounded-full bg-indigo-600 px-2.5 py-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-wide text-white shadow-sm">
                                            <span class="truncate" x-text="messages.selected_template"></span>
                                        </span>
                                    </template>
                                    <template x-if="template !== option.key">
                                        <button
                                            type="button"
                                            class="cv-template-choose pointer-events-auto inline-flex max-w-full items-center rounded-full bg-white px-2.5 py-1 text-[10px] sm:text-[11px] font-bold uppercase tracking-wide text-indigo-700 shadow-md ring-1 ring-indigo-100 transition hover:bg-indigo-50"
                                            @click.stop="selectTemplate(option.key)"
                                        >
                                            <span class="truncate" x-text="messages.choose_template"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <span
                                class="mt-2 block text-center text-xs sm:text-sm font-semibold truncate"
                                :class="template === option.key ? 'text-indigo-700' : 'text-gray-700'"
                                x-text="option.label"
                            ></span>
                        </div>
                    </template>
                </div>

                <button
                    type="button"
                    class="cv-template-nav cv-template-nav--right"
                    :class="{ 'is-disabled': ! templateSliderCanNext }"
                    :disabled="! templateSliderCanNext"
                    :aria-label="messages.templates_next"
                    @click="scrollTemplateSlider(1)"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
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
                <div class="flex-1 min-h-0 bg-gray-100 p-3 overflow-y-auto overflow-x-hidden" x-ref="previewStage">
                    {{-- Scaler: visual zoom only. iframe stays at true A4 width (794px) for PDF HTML. --}}
                    <div
                        class="mx-auto overflow-hidden"
                        :style="previewScalerBoxStyle()"
                    >
                        <iframe
                            x-ref="previewFrame"
                            class="bg-white rounded-lg shadow-sm border-0 block max-w-none"
                            :style="previewFrameStyle()"
                            title="{{ __('talenma.cv_builder.preview_title') }}"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
