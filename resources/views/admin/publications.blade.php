<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.admin.publications.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.admin.publications.subtitle') }}</p>
        </div>
    </x-slot>

    <x-process-help topic="publications" />

    <div
        class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="publicationsAdmin()"
        @keydown.escape.window="closeAll()"
        @ajax-form-success.window="onAjaxSuccess($event)"
        @publications-news-edit.window="openNewsEdit($event.detail)"
        @publications-delete.window="openDeleteConfirm($event.detail)"
    >
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 xl:gap-8 items-start">
            {{-- Actualités (bandeau haut) --}}
            <section id="actualites" class="scroll-mt-24 space-y-4 min-w-0">
                <div class="flex items-start justify-between gap-3 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-gray-900">{{ __('talenma.admin.news.title') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.news.subtitle', ['max' => $newsMaxItems]) }}</p>
                    </div>
                    <button
                        type="button"
                        @click="openNews()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-indigo-200 bg-white text-indigo-700 shadow-sm transition hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                        aria-label="{{ __('talenma.admin.news.add_title') }}"
                        title="{{ __('talenma.admin.news.add_title') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>

                @if (session('news_saved'))
                    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                        {{ __('talenma.admin.news.saved') }}
                    </div>
                @endif

                @if (session('news_updated'))
                    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                        {{ __('talenma.admin.news.updated') }}
                    </div>
                @endif

                @if (session('news_deleted'))
                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm">
                        {{ __('talenma.admin.news.deleted') }}
                    </div>
                @endif

                @include('admin.publications._news-list')
            </section>

            {{-- Posts réseaux sociaux (slider accueil) --}}
            <section id="reseaux" class="scroll-mt-24 space-y-4 min-w-0">
                <div class="flex items-start justify-between gap-3 rounded-2xl border border-violet-100 bg-gradient-to-br from-violet-50/80 via-white to-slate-50 p-4 sm:p-5">
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-gray-900">{{ __('talenma.admin.social_posts.title') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('talenma.admin.social_posts.subtitle', ['max' => $socialMaxItems]) }}</p>
                    </div>
                    <button
                        type="button"
                        @click="openSocial()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-violet-200 bg-white text-violet-700 shadow-sm transition hover:bg-violet-50 hover:border-violet-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2"
                        aria-label="{{ __('talenma.admin.social_posts.add_title') }}"
                        title="{{ __('talenma.admin.social_posts.add_title') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>

                @if (session('post_saved'))
                    <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
                        {{ __('talenma.admin.social_posts.saved') }}
                    </div>
                @endif

                @if (session('post_deleted'))
                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm">
                        {{ __('talenma.admin.social_posts.deleted') }}
                    </div>
                @endif

                @include('admin.publications._social-list')
            </section>
        </div>

        {{-- Bottom sheet: ajouter une actualité --}}
        <div
            x-show="newsOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="publications-news-create-title"
        >
            <div
                x-show="newsOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeNews()"
            ></div>

            <div
                x-show="newsOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,40rem)] sm:max-w-lg sm:rounded-2xl"
                @click.stop
            >
                <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.admin.news.title') }}</p>
                        <h3 id="publications-news-create-title" class="mt-1 text-lg font-bold text-gray-900">{{ __('talenma.admin.news.add_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.admin.news.add_hint', ['max' => $newsMaxItems]) }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeNews()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <div id="publications-news-create-card" class="relative">
                        <form
                            id="publications-news-create"
                            method="POST"
                            action="{{ route('admin.publications.news.store') }}"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            data-ajax
                            data-ajax-reset
                            data-refresh="publications-news"
                            data-loading-target="publications-news-create-card"
                            data-error-message="{{ __('talenma.common.save_error') }}"
                            novalidate
                        >
                            @csrf

                            <div>
                                <x-input-label for="news_title" :value="__('talenma.admin.news.field_title')" />
                                <x-text-input
                                    id="news_title"
                                    name="title"
                                    class="mt-1 block w-full"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.title_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="news_subtitle" :value="__('talenma.admin.news.field_subtitle')" />
                                <x-text-input
                                    id="news_subtitle"
                                    name="subtitle"
                                    class="mt-1 block w-full"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.subtitle_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="news_url" :value="__('talenma.admin.news.field_url')" />
                                <x-text-input
                                    id="news_url"
                                    name="url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    placeholder="https://"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.url_required') }}"
                                    data-url
                                    data-url-message="{{ __('talenma.admin.news.url_invalid') }}"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.url_hint') }}</p>
                            </div>

                            <div>
                                <x-input-label for="news_thumbnail" :value="__('talenma.admin.news.field_thumbnail')" />
                                <input
                                    id="news_thumbnail"
                                    name="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100"
                                >
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.thumbnail_hint') }}</p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-1">
                                <button
                                    type="button"
                                    class="inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
                                    @click="closeNews()"
                                >{{ __('talenma.common.cancel') }}</button>
                                <x-primary-button class="justify-center">{{ __('talenma.admin.news.submit') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom sheet: modifier une actualité --}}
        <div
            x-show="newsEditOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="publications-news-edit-title"
        >
            <div
                x-show="newsEditOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeNewsEdit()"
            ></div>

            <div
                x-show="newsEditOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,40rem)] sm:max-w-lg sm:rounded-2xl"
                @click.stop
            >
                <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.admin.news.title') }}</p>
                        <h3 id="publications-news-edit-title" class="mt-1 text-lg font-bold text-gray-900">{{ __('talenma.admin.news.edit_title') }}</h3>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeNewsEdit()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <div id="publications-news-edit-card" class="relative">
                        <form
                            id="publications-news-edit"
                            method="POST"
                            :action="editAction"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            data-ajax
                            data-refresh="publications-news"
                            data-loading-target="publications-news-edit-card"
                            data-error-message="{{ __('talenma.common.save_error') }}"
                            novalidate
                        >
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="news_edit_title" :value="__('talenma.admin.news.field_title')" />
                                <x-text-input
                                    id="news_edit_title"
                                    name="title"
                                    class="mt-1 block w-full"
                                    x-model="editTitle"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.title_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="news_edit_subtitle" :value="__('talenma.admin.news.field_subtitle')" />
                                <x-text-input
                                    id="news_edit_subtitle"
                                    name="subtitle"
                                    class="mt-1 block w-full"
                                    x-model="editSubtitle"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.subtitle_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="news_edit_url" :value="__('talenma.admin.news.field_url')" />
                                <x-text-input
                                    id="news_edit_url"
                                    name="url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    x-model="editUrl"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.news.url_required') }}"
                                    data-url
                                    data-url-message="{{ __('talenma.admin.news.url_invalid') }}"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.url_hint') }}</p>
                            </div>

                            <div>
                                <x-input-label for="news_edit_thumbnail" :value="__('talenma.admin.news.field_thumbnail')" />
                                <input
                                    id="news_edit_thumbnail"
                                    name="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100"
                                >
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.news.thumbnail_keep') }}</p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-1">
                                <button
                                    type="button"
                                    class="inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
                                    @click="closeNewsEdit()"
                                >{{ __('talenma.common.cancel') }}</button>
                                <x-primary-button class="justify-center">{{ __('talenma.admin.news.update') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom sheet: ajouter un post --}}
        <div
            x-show="socialOpen"
            x-cloak
            class="fixed inset-0 z-[60]"
            role="dialog"
            aria-modal="true"
            aria-labelledby="publications-social-create-title"
        >
            <div
                x-show="socialOpen"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-900/40"
                @click="closeSocial()"
            ></div>

            <div
                x-show="socialOpen"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="translate-y-full"
                class="absolute bottom-0 right-0 flex w-full max-w-md flex-col rounded-t-2xl bg-white shadow-2xl ring-1 ring-black/5 sm:bottom-3 sm:right-3 sm:max-h-[min(88vh,40rem)] sm:max-w-lg sm:rounded-2xl"
                @click.stop
            >
                <div class="mx-auto mt-3 h-1.5 w-10 shrink-0 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>

                <div class="flex shrink-0 items-start justify-between gap-4 border-b border-gray-100 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.admin.social_posts.title') }}</p>
                        <h3 id="publications-social-create-title" class="mt-1 text-lg font-bold text-gray-900">{{ __('talenma.admin.social_posts.add_title') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('talenma.admin.social_posts.add_hint', ['max' => $socialMaxItems]) }}</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="closeSocial()"
                        aria-label="{{ __('talenma.common.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                    <div id="publications-social-create-card" class="relative">
                        <form
                            id="publications-social-create"
                            method="POST"
                            action="{{ route('admin.publications.social-posts.store') }}"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            data-ajax
                            data-ajax-reset
                            data-refresh="publications-social"
                            data-loading-target="publications-social-create-card"
                            data-error-message="{{ __('talenma.common.save_error') }}"
                            novalidate
                        >
                            @csrf

                            <div>
                                <x-input-label for="post_title" :value="__('talenma.admin.social_posts.field_title')" />
                                <x-text-input
                                    id="post_title"
                                    name="post_title"
                                    class="mt-1 block w-full"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.social_posts.title_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="post_subtitle" :value="__('talenma.admin.social_posts.field_subtitle')" />
                                <x-text-input
                                    id="post_subtitle"
                                    name="post_subtitle"
                                    class="mt-1 block w-full"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.social_posts.subtitle_required') }}"
                                />
                            </div>

                            <div>
                                <x-input-label for="post_url" :value="__('talenma.admin.social_posts.field_url')" />
                                <x-text-input
                                    id="post_url"
                                    name="post_url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    placeholder="https://"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.social_posts.url_required') }}"
                                    data-url
                                    data-url-message="{{ __('talenma.admin.social_posts.url_invalid') }}"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.social_posts.url_hint') }}</p>
                            </div>

                            <div>
                                <x-input-label for="post_network" :value="__('talenma.admin.social_posts.field_network')" />
                                <select
                                    id="post_network"
                                    name="post_network"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                    data-required
                                    data-required-message="{{ __('talenma.admin.social_posts.network_required') }}"
                                >
                                    @foreach ($networks as $network)
                                        <option value="{{ $network }}" @selected($network === 'linkedin')>
                                            {{ __('talenma.social_feed.sources.'.$network) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="post_thumbnail" :value="__('talenma.admin.social_posts.field_thumbnail')" />
                                <input
                                    id="post_thumbnail"
                                    name="post_thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100"
                                >
                                <p class="mt-1 text-xs text-gray-500">{{ __('talenma.admin.social_posts.thumbnail_hint') }}</p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-1">
                                <button
                                    type="button"
                                    class="inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50"
                                    @click="closeSocial()"
                                >{{ __('talenma.common.cancel') }}</button>
                                <x-primary-button class="justify-center">{{ __('talenma.admin.social_posts.submit') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal confirmation suppression --}}
        <div
            x-show="deleteConfirmOpen"
            x-cloak
            class="fixed inset-0 z-[70] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="publications-delete-title"
        >
            <div class="flex min-h-full items-end justify-center p-4 sm:items-center sm:p-6">
                <div
                    x-show="deleteConfirmOpen"
                    x-transition:enter="transition-opacity ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/40"
                    @click="closeDeleteConfirm()"
                ></div>

                <div
                    x-show="deleteConfirmOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5"
                    @click.stop
                >
                    <div class="px-6 py-5">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 3.7 2.6 17a2 2 0 0 0 1.73 3h15.34a2 2 0 0 0 1.73-3L13.7 3.7a2 2 0 0 0-3.4 0Z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 id="publications-delete-title" class="text-lg font-bold text-gray-900" x-text="deleteModalTitle"></h3>
                                <p class="mt-2 text-sm leading-6 text-gray-600" x-text="deleteModalBody"></p>
                                <p class="mt-2 text-sm font-medium text-red-700">{{ __('talenma.admin.publications.delete_irreversible') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="inline-flex justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            @click="closeDeleteConfirm()"
                        >
                            {{ __('talenma.common.cancel') }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="confirmDelete()"
                            x-text="deleteConfirmLabel"
                        ></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
