{{-- Requires Alpine parent: libraryFilters --}}
<template x-teleport="body">
    <div
        x-show="drawerOpen"
        x-cloak
        class="fixed inset-0 z-[80]"
        style="margin: 0; height: 100vh; min-height: 100vh; max-height: 100vh;"
        role="dialog"
        aria-modal="true"
        :aria-label="selectedBook?.title || @js(__('talenma.library.drawer_title'))"
        @keydown.escape.window="closeBook()"
    >
        <div
            x-show="drawerOpen"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gray-900/45"
            @click="closeBook()"
        ></div>

        <aside
            x-show="drawerOpen"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-xl flex-col bg-white shadow-2xl"
            style="height: 100vh; min-height: 100vh; max-height: 100vh;"
            @click.stop
        >
            <div class="flex shrink-0 items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
                <p class="text-sm font-semibold uppercase tracking-wide text-amber-700">
                    {{ __('talenma.library.drawer_title') }}
                </p>
                <button
                    type="button"
                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    aria-label="{{ __('talenma.common.close') }}"
                    @click="closeBook()"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-7" x-show="selectedBook">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="mx-auto sm:mx-0 relative h-56 w-40 shrink-0 overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 via-orange-50 to-amber-200 shadow-sm">
                        <template x-if="selectedBook?.cover_url">
                            <img
                                :src="selectedBook.cover_url"
                                :alt="selectedBook.title"
                                class="absolute inset-0 h-full w-full object-cover"
                            >
                        </template>
                        <div
                            x-show="! selectedBook?.cover_url"
                            class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-amber-800/70 px-3 text-center"
                        >
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <span class="text-xs font-semibold">{{ __('talenma.library.cover_placeholder') }}</span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p
                            class="text-[11px] font-semibold uppercase tracking-wide text-amber-700/80"
                            x-text="selectedBook?.category_path || ''"
                            x-show="selectedBook?.category_path"
                        ></p>
                        <h2 class="mt-2 text-2xl font-bold text-gray-900 leading-tight" x-text="selectedBook?.title"></h2>
                        <p
                            class="mt-2 text-sm font-medium text-gray-600"
                            x-show="selectedBook?.author"
                            x-text="selectedBook?.author"
                        ></p>
                        <p
                            class="mt-3 text-xs text-gray-400"
                            x-show="selectedBook?.size"
                            x-text="selectedBook?.size"
                        ></p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        {{ __('talenma.library.description_label') }}
                    </h3>
                    <p
                        class="mt-2 text-sm leading-relaxed text-gray-700 whitespace-pre-wrap"
                        x-show="selectedBook?.description"
                        x-text="selectedBook?.description"
                    ></p>
                    <p
                        class="mt-2 text-sm text-gray-400"
                        x-show="! selectedBook?.description"
                    >{{ __('talenma.library.no_description') }}</p>
                </div>
            </div>

            <div class="shrink-0 border-t border-gray-100 bg-white px-5 py-4 sm:px-6" x-show="selectedBook">
                <a
                    :href="selectedBook?.download_url"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    {{ __('talenma.library.download') }}
                </a>
            </div>
        </aside>
    </div>
</template>
