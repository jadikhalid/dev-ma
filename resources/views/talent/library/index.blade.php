<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md shadow-amber-600/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
            </span>
            <div>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('talenma.library.page_title') }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('talenma.library.page_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('libraryFilters', (config = {}) => ({
                tree: config.tree || [],
                endpoint: config.endpoint || '',
                disciplineId: '',
                subId: '',
                loading: false,
                requestToken: 0,
                drawerOpen: false,
                selectedBook: null,
                init() {
                    this.hydrateFromCategory(config.initialCategoryId ? Number(config.initialCategoryId) : null);
                    this.$watch('drawerOpen', (open) => {
                        document.body.classList.toggle('overflow-hidden', open);
                    });
                },
                get subOptions() {
                    const root = this.tree.find((n) => String(n.id) === String(this.disciplineId));
                    return root?.children || [];
                },
                get selectedCategoryId() {
                    return this.subId || this.disciplineId || '';
                },
                onDisciplineChange() {
                    this.subId = '';
                    this.refreshResults();
                },
                openBook(book) {
                    this.selectedBook = book;
                    this.drawerOpen = true;
                },
                closeBook() {
                    this.drawerOpen = false;
                    this.selectedBook = null;
                },
                decodeBookPayload(raw) {
                    const bytes = Uint8Array.from(atob(raw), (char) => char.charCodeAt(0));
                    return JSON.parse(new TextDecoder().decode(bytes));
                },
                buildUrl(page) {
                    const url = new URL(this.endpoint, window.location.origin);
                    const categoryId = this.selectedCategoryId;

                    if (categoryId) {
                        url.searchParams.set('category', String(categoryId));
                    }

                    if (page && Number(page) > 1) {
                        url.searchParams.set('page', String(page));
                    }

                    return url;
                },
                async refreshResults(page) {
                    const token = ++this.requestToken;
                    const url = this.buildUrl(page);
                    const fetchUrl = new URL(url.toString());
                    fetchUrl.searchParams.set('partial', '1');

                    this.loading = true;

                    try {
                        const response = await fetch(fetchUrl.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'text/html',
                            },
                            credentials: 'same-origin',
                        });

                        if (! response.ok) {
                            throw new Error('Failed to load library results');
                        }

                        const html = await response.text();

                        if (token !== this.requestToken) {
                            return;
                        }

                        this.$refs.results.innerHTML = html;
                        history.replaceState({}, '', url.pathname + url.search);
                    } catch (error) {
                        if (token === this.requestToken) {
                            console.error(error);
                        }
                    } finally {
                        if (token === this.requestToken) {
                            this.loading = false;
                        }
                    }
                },
                onResultsClick(event) {
                    const openBtn = event.target.closest('[data-library-book]');

                    if (openBtn && this.$refs.results.contains(openBtn)) {
                        event.preventDefault();
                        event.stopPropagation();

                        try {
                            this.openBook(this.decodeBookPayload(openBtn.getAttribute('data-library-book') || ''));
                        } catch (error) {
                            console.error('library book drawer open failed', error);
                        }

                        return;
                    }

                    const link = event.target.closest('a');

                    if (! link || ! this.$refs.results.contains(link)) {
                        return;
                    }

                    if (link.getAttribute('href')?.includes('/download')) {
                        return;
                    }

                    const href = link.getAttribute('href');

                    if (! href || href === '#' || link.target === '_blank') {
                        return;
                    }

                    let target;

                    try {
                        target = new URL(href, window.location.origin);
                    } catch (error) {
                        return;
                    }

                    if (target.origin !== window.location.origin) {
                        return;
                    }

                    if (! target.pathname.includes('/talent/library')) {
                        return;
                    }

                    event.preventDefault();

                    const page = target.searchParams.get('page') || '1';
                    const category = target.searchParams.get('category') || '';

                    if (category) {
                        this.hydrateFromCategory(Number(category));
                    } else {
                        this.disciplineId = '';
                        this.subId = '';
                    }

                    this.refreshResults(page);
                },
                hydrateFromCategory(categoryId) {
                    this.disciplineId = '';
                    this.subId = '';

                    if (! categoryId) {
                        return;
                    }

                    for (const root of this.tree) {
                        if (root.id === categoryId) {
                            this.disciplineId = String(root.id);
                            return;
                        }

                        for (const sub of (root.children || [])) {
                            if (sub.id === categoryId) {
                                this.disciplineId = String(root.id);
                                this.subId = String(sub.id);
                                return;
                            }

                            for (const leaf of (sub.children || [])) {
                                if (leaf.id === categoryId) {
                                    this.disciplineId = String(root.id);
                                    this.subId = String(sub.id);
                                    return;
                                }
                            }
                        }
                    }
                },
            }));
        });
    </script>

    <div
        class="py-8"
        x-data="libraryFilters({
            tree: @js($treePayload),
            initialCategoryId: @js($filters['category']),
            endpoint: @js(route('talent.library.index')),
        })"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ __('talenma.library.filters_title') }}</h2>
                    <p class="mt-0.5 text-sm text-gray-500">{{ __('talenma.library.filters_subtitle') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ __('talenma.library.filter_discipline') }}</label>
                        <select
                            x-model="disciplineId"
                            @change="onDisciplineChange()"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500"
                        >
                            <option value="">{{ __('talenma.library.filter_all') }}</option>
                            <template x-for="node in tree" :key="node.id">
                                <option :value="String(node.id)" x-text="node.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase tracking-wide mb-1"
                            :class="disciplineId ? 'text-gray-500' : 'text-gray-400'"
                        >{{ __('talenma.library.filter_sub') }}</label>
                        <select
                            x-model="subId"
                            :disabled="!disciplineId"
                            @change="refreshResults()"
                            class="w-full rounded-xl border-gray-200 text-sm focus:border-amber-500 focus:ring-amber-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                        >
                            <option value="">{{ __('talenma.library.filter_all') }}</option>
                            <template x-for="node in subOptions" :key="node.id">
                                <option :value="String(node.id)" x-text="node.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div
                class="relative"
                :class="{ 'opacity-60 pointer-events-none': loading }"
                x-ref="results"
                @click="onResultsClick($event)"
            >
                @include('talent.library._results', ['books' => $books])
            </div>
        </div>

        @include('talent.library._book-drawer')
    </div>
</x-app-layout>
