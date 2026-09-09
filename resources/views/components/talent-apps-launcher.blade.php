@props([
    'guest' => false,
])

@php
    $cvUrl = $guest ? route('cv-builder.gate') : route('talent.cv-builder.index');
    $atsUrl = $guest ? route('ats-score.gate') : route('talent.ats-score.index');
@endphp

<div
    class="relative"
    x-data="{
        open: false,
        panelStyle: 'visibility:hidden',
        toggle() {
            if (this.open) {
                this.open = false;
                this.panelStyle = 'visibility:hidden';
                return;
            }
            this.open = true;
            this.$nextTick(() => {
                requestAnimationFrame(() => this.placePanel());
            });
        },
        headerBottom() {
            const header = this.$refs.trigger?.closest('header, nav');
            return header ? header.getBoundingClientRect().bottom : 0;
        },
        placePanel() {
            const trigger = this.$refs.trigger;
            const panel = this.$refs.panel;
            if (! trigger || ! panel) {
                return;
            }

            const rect = trigger.getBoundingClientRect();
            const gap = 8;
            const margin = 12;
            const width = Math.min(272, window.innerWidth - (margin * 2));
            let left = rect.right - width;
            left = Math.max(margin, Math.min(left, window.innerWidth - width - margin));
            const top = Math.max(rect.bottom, this.headerBottom()) + gap;

            this.panelStyle = `position:fixed;top:${top}px;left:${left}px;width:${width}px;z-index:80;visibility:visible;`;
        },
        onViewportChange() {
            if (this.open) {
                this.placePanel();
            }
        },
        close() {
            this.open = false;
            this.panelStyle = 'visibility:hidden';
        },
    }"
    @keydown.escape.window="close()"
    @resize.window="onViewportChange()"
    @scroll.window="onViewportChange()"
>
    <button
        type="button"
        x-ref="trigger"
        @click="toggle()"
        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white/90 hover:bg-white/15 hover:text-white sm:text-gray-500 sm:hover:bg-gray-100 sm:hover:text-gray-700 transition"
        :aria-expanded="open"
        aria-haspopup="true"
        aria-label="{{ __('talenma.nav.apps_launcher_open') }}"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <circle cx="5" cy="5" r="1.75"/>
            <circle cx="12" cy="5" r="1.75"/>
            <circle cx="19" cy="5" r="1.75"/>
            <circle cx="5" cy="12" r="1.75"/>
            <circle cx="12" cy="12" r="1.75"/>
            <circle cx="19" cy="12" r="1.75"/>
            <circle cx="5" cy="19" r="1.75"/>
            <circle cx="12" cy="19" r="1.75"/>
            <circle cx="19" cy="19" r="1.75"/>
        </svg>
    </button>

    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 z-[70]"
            @click="close()"
        >
            <div
                x-ref="panel"
                class="rounded-xl bg-white py-3 shadow-lg ring-1 ring-black/5"
                :style="panelStyle"
                @click.stop
            >
                <div class="px-4 pb-1">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">
                        {{ __('talenma.nav.apps_launcher_title') }}
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-2 px-3 pb-1">
                    <a
                        href="{{ $cvUrl }}"
                        class="flex flex-col items-center rounded-xl px-3 py-3 text-center hover:bg-indigo-50 transition group"
                        @click="close()"
                    >
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 group-hover:bg-indigo-200 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                            </svg>
                        </span>
                        <span class="mt-2 text-xs font-semibold text-gray-800 leading-tight">
                            {{ __('talenma.nav.apps_launcher_cv_builder') }}
                        </span>
                    </a>

                    <a
                        href="{{ $atsUrl }}"
                        class="flex flex-col items-center rounded-xl px-3 py-3 text-center hover:bg-emerald-50 transition group"
                        @click="close()"
                    >
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 group-hover:bg-emerald-200 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/>
                            </svg>
                        </span>
                        <span class="mt-2 text-xs font-semibold text-gray-800 leading-tight">
                            {{ __('talenma.nav.apps_launcher_ats_score') }}
                        </span>
                    </a>

                    <div
                        class="flex flex-col items-center rounded-xl px-3 py-3 text-center cursor-not-allowed"
                        aria-disabled="true"
                    >
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </span>
                        <span class="mt-2 text-xs font-semibold text-gray-400 leading-tight">
                            {{ __('talenma.nav.apps_launcher_library') }}
                        </span>
                    </div>

                    <div
                        class="flex flex-col items-center rounded-xl px-3 py-3 text-center cursor-not-allowed"
                        aria-disabled="true"
                        title="{{ __('talenma.nav.apps_launcher_library_soon') }}"
                    >
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                            </svg>
                        </span>
                        <span class="mt-2 text-xs font-semibold text-gray-400 leading-tight">
                            {{ __('talenma.nav.apps_launcher_videotheque') }}
                        </span>
                    </div>

                    <div
                        class="flex flex-col items-center rounded-xl px-3 py-3 text-center cursor-not-allowed"
                        aria-disabled="true"
                        title="{{ __('talenma.nav.apps_launcher_library_soon') }}"
                    >
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.908.276-1.813.567-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>
                            </svg>
                        </span>
                        <span class="mt-2 text-xs font-semibold text-gray-400 leading-tight">
                            {{ __('talenma.nav.apps_launcher_e_academie') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
