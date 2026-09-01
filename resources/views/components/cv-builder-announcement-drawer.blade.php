@php
    $user = auth()->user();
    $cvAnnouncementCtaUrl = ($user && $user->isTalent())
        ? route('talent.cv-builder.index')
        : route('cv-builder.gate');
    $cvAnnouncementCtaLabel = __('talenma.home.cv_builder_announcement.cta');
@endphp

<div x-data="cvBuilderAnnouncement()">
    <template x-teleport="body">
        <div
            x-show="open"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[90]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cv-builder-announcement-title"
            @keydown.escape.window="close()"
        >
            <div
                class="absolute inset-0 bg-gray-900/45 backdrop-blur-[1px]"
                @click="close()"
            ></div>

            <div class="absolute inset-y-0 right-0 flex w-full max-w-[min(100vw,680px)] items-center p-4 sm:p-6 pointer-events-none">
                <div
                    x-show="open"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-250"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto flex w-full flex-col overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-2xl"
                    @click.stop
                >
                    <div class="shrink-0 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-teal-50 px-5 py-4 sm:px-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="inline-flex items-center rounded-full bg-indigo-600 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-white">
                                    {{ __('talenma.home.cv_builder_announcement.badge') }}
                                </p>
                                <h2 id="cv-builder-announcement-title" class="mt-2 text-xl font-bold text-gray-900 leading-tight">
                                    {{ __('talenma.home.cv_builder_announcement.title') }}
                                </h2>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-white/80 hover:text-gray-600"
                                @click="close()"
                                aria-label="{{ __('talenma.home.cv_builder_announcement.close') }}"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-5 py-4 sm:px-6">
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 items-start">
                            <div class="min-w-0 flex-1 space-y-3">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ __('talenma.home.cv_builder_announcement.subtitle') }}
                                </p>

                                <ul class="space-y-1.5 text-sm text-gray-600">
                                    <li class="flex gap-2">
                                        <span class="mt-0.5 text-indigo-600 shrink-0" aria-hidden="true">✓</span>
                                        <span>{{ __('talenma.home.cv_builder_announcement.feature_1') }}</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="mt-0.5 text-indigo-600 shrink-0" aria-hidden="true">✓</span>
                                        <span>{{ __('talenma.home.cv_builder_announcement.feature_2') }}</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="mt-0.5 text-indigo-600 shrink-0" aria-hidden="true">✓</span>
                                        <span>{{ __('talenma.home.cv_builder_announcement.feature_3') }}</span>
                                    </li>
                                </ul>
                            </div>

                            <figure
                                class="w-full sm:w-[200px] shrink-0 overflow-hidden rounded-2xl border-2 border-white/60 shadow-lg ring-2 ring-indigo-200/80"
                                aria-label="{{ __('talenma.home.cv_builder_announcement.logo_alt') }}"
                            >
                                <x-cv-builder-tool-mark class="block h-auto w-full" />
                            </figure>
                        </div>
                    </div>

                    <div class="shrink-0 border-t border-gray-100 bg-gray-50/80 px-5 py-4 sm:px-6 space-y-3">
                        <a
                            href="{{ $cvAnnouncementCtaUrl }}"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition"
                        >
                            {{ $cvAnnouncementCtaLabel }}
                        </a>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <button
                                type="button"
                                class="text-gray-500 hover:text-gray-700 underline-offset-2 hover:underline"
                                @click="dismissForever()"
                            >
                                {{ __('talenma.home.cv_builder_announcement.dismiss') }}
                            </button>
                            <button
                                type="button"
                                class="font-medium text-indigo-600 hover:text-indigo-800"
                                @click="close()"
                            >
                                {{ __('talenma.home.cv_builder_announcement.later') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
