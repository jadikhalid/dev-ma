@props([
    'videoUrl' => null,
    'editable' => false,
    'maxBytes' => null,
    'personName' => null,
])

@php
    $maxBytes = $maxBytes ?? (app(\App\Services\TalentPresentationVideoService::class)->maxKilobytes() * 1024);
    $maxMo = max(1, (int) ceil($maxBytes / (1024 * 1024)));
@endphp

<div
    @if ($editable)
        id="talent-presentation-video-card"
        x-data="talentPresentationVideo({
            videoUrl: @js($videoUrl),
            maxBytes: {{ (int) $maxBytes }},
            allowedTypes: @js(\App\Services\TalentPresentationVideoService::ALLOWED_MIMES),
            messages: {
                invalidType: @js(__('talenma.talent.presentation_video_type')),
                tooLarge: @js(__('talenma.talent.presentation_video_size', ['max' => $maxMo])),
                required: @js(__('talenma.talent.presentation_video_required')),
            },
        })"
    @endif
    {{ $attributes->merge(['class' => 'relative bg-white rounded-2xl border p-6 sm:p-8 h-full flex flex-col']) }}
>
    <div class="flex items-center justify-between gap-3">
        <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.video_snapshot') }}</p>
        @if ($editable)
            <button
                type="button"
                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-indigo-100 bg-indigo-50 text-indigo-600 transition hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                @click="openHelp()"
                aria-label="{{ __('talenma.talent.presentation_video_help_aria') }}"
                title="{{ __('talenma.talent.presentation_video_help_aria') }}"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" d="M12 16v-4" />
                    <circle cx="12" cy="8" r="0.75" fill="currentColor" stroke="none" />
                </svg>
            </button>
        @endif
    </div>

    <div class="mt-4 flex-1 flex flex-col">
        @if ($editable)
            <template x-if="videoUrl">
                <div class="space-y-3">
                    <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-900">
                        <x-talent-video-thumbnail :person-name="$personName" class="absolute inset-0 h-full w-full" x-show="!playing" />
                        <button
                            type="button"
                            class="absolute inset-0 z-10 flex items-center justify-center bg-black/20 hover:bg-black/30 transition"
                            @click="playing = true; $nextTick(() => $refs.player?.play())"
                            x-show="!playing"
                        >
                            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/95 text-indigo-600 shadow-lg">
                                <svg class="ml-0.5 h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M8 5.14v13.72L19 12 8 5.14Z" />
                                </svg>
                            </span>
                            <span class="sr-only">{{ __('talenma.dashboard.talent.video_play') }}</span>
                        </button>
                        <video
                            x-show="playing"
                            x-cloak
                            class="absolute inset-0 h-full w-full object-cover bg-black"
                            controls
                            preload="none"
                            :src="videoUrl"
                            x-ref="player"
                            @play="playing = true"
                            @ended="playing = false; $refs.player.currentTime = 0"
                        ></video>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('talenma.dashboard.talent.video_ready_hint') }}</p>
                </div>
            </template>

            <template x-if="!videoUrl">
                <div class="relative aspect-video w-full overflow-hidden rounded-xl border border-dashed border-gray-200 bg-slate-50">
                    <x-talent-video-thumbnail :branded="false" class="absolute inset-0 h-full w-full opacity-80" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-white/55 px-4 text-center">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-600/10 text-indigo-600">
                            <svg class="ml-0.5 h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M8 5.14v13.72L19 12 8 5.14Z" />
                            </svg>
                        </span>
                        <p class="text-sm font-medium text-gray-800">{{ __('talenma.dashboard.talent.video_empty_title') }}</p>
                        <p class="text-xs text-gray-500 max-w-[16rem]">{{ __('talenma.dashboard.talent.video_empty_desc') }}</p>
                    </div>
                </div>
            </template>

            <form
                method="POST"
                action="{{ route('talent.presentation-video.store') }}"
                enctype="multipart/form-data"
                class="mt-4 space-y-3"
                data-ajax
                data-loading-target="talent-presentation-video-card"
                data-ajax-timeout="180000"
                novalidate
                data-error-message="{{ __('talenma.talent.save_error') }}"
            >
                @csrf
                <div>
                    <label for="presentation_video" class="block text-sm font-medium text-gray-700">
                        <span x-text="videoUrl ? @js(__('talenma.talent.presentation_video_replace')) : @js(__('talenma.talent.presentation_video_upload'))"></span>
                    </label>
                    <input
                        id="presentation_video"
                        name="presentation_video"
                        type="file"
                        accept="video/mp4,video/quicktime,.mp4,.mov"
                        x-ref="fileInput"
                        @change="onFileChange($event)"
                        required
                        data-required
                        data-required-message="{{ __('talenma.talent.presentation_video_required') }}"
                        class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    >
                    <p class="mt-1 text-xs text-gray-500" x-show="!pendingName">{{ __('talenma.talent.presentation_video_hint', ['max' => $maxMo]) }}</p>
                    <p class="mt-1 text-xs text-gray-600" x-show="pendingName" x-cloak>
                        <span x-text="pendingName"></span>
                        <span class="text-gray-400" x-text="' · ' + pendingSizeLabel"></span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-primary-button type="submit" class="justify-center">{{ __('talenma.talent.save_section') }}</x-primary-button>
                </div>
            </form>

            <form
                method="POST"
                action="{{ route('talent.presentation-video.destroy') }}"
                class="mt-2"
                data-ajax
                data-loading-target="talent-presentation-video-card"
                data-confirm="{{ __('talenma.talent.presentation_video_delete_confirm') }}"
                data-error-message="{{ __('talenma.talent.save_error') }}"
                x-show="videoUrl"
                x-cloak
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">
                    {{ __('talenma.talent.presentation_video_delete') }}
                </button>
            </form>

            <div
                x-show="helpOpen"
                x-cloak
                class="fixed inset-0 z-[60]"
                role="dialog"
                aria-modal="true"
                aria-labelledby="talent-presentation-video-help-title"
                @keydown.escape.window="helpOpen && closeHelp()"
            >
                <div
                    x-show="helpOpen"
                    x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 bg-gray-900/40"
                    @click="closeHelp()"
                ></div>

                <div
                    x-show="helpOpen"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-white shadow-2xl"
                    @click.stop
                >
                    <div class="relative shrink-0 border-b border-gray-100 px-5 py-5 sm:px-6">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-indigo-400 to-sky-400"></div>
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm shadow-indigo-200">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M4 6.75A2.75 2.75 0 0 1 6.75 4h6.5A2.75 2.75 0 0 1 16 6.75v10.5A2.75 2.75 0 0 1 13.25 20h-6.5A2.75 2.75 0 0 1 4 17.25V6.75Z" />
                                        <path d="M17.25 9.1 20.2 7.2a1 1 0 0 1 1.55.83v7.94a1 1 0 0 1-1.55.83l-2.95-1.9V9.1Z" />
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <h2 id="talent-presentation-video-help-title" class="text-lg font-bold text-gray-900">
                                        {{ __('talenma.talent.presentation_video_help_title') }}
                                    </h2>
                                    <p class="mt-2 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                        {{ __('talenma.talent.presentation_video_help_summary', ['max' => $maxMo]) }}
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="closeHelp()"
                                class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                aria-label="{{ __('talenma.talent.presentation_video_help_close') }}"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-5 sm:px-6">
                        <div class="rounded-2xl border border-gray-100 bg-slate-50/80 p-4 sm:p-5">
                            <div class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-6m0-4h.01M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.presentation_video_help_why_title') }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ __('talenma.talent.presentation_video_help_why_body') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-slate-50/80 p-4 sm:p-5">
                            <div class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5 20.25 7.5v9l-4.5-3M4.5 7.5h9A1.5 1.5 0 0 1 15 9v6a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 3 15V9a1.5 1.5 0 0 1 1.5-1.5Z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.presentation_video_help_what_title') }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ __('talenma.talent.presentation_video_help_what_body') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-gray-100 bg-slate-50/80 p-4 sm:p-5">
                            <div class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1.5A2.5 2.5 0 0 0 6.5 20h11A2.5 2.5 0 0 0 20 17.5V16M12 4v11m0 0-3.5-3.5M12 15l3.5-3.5" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ __('talenma.talent.presentation_video_help_how_title') }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ __('talenma.talent.presentation_video_help_how_body', ['max' => $maxMo]) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 border-t border-gray-100 px-5 py-4 sm:px-6">
                        <x-primary-button type="button" class="w-full justify-center" @click="closeHelp()">
                            {{ __('talenma.talent.presentation_video_help_close') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>
        @else
            @if (filled($videoUrl))
                <div
                    class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-900"
                    x-data="{ playing: false }"
                >
                    <x-talent-video-thumbnail :person-name="$personName" class="absolute inset-0 h-full w-full" x-show="!playing" />
                    <button
                        type="button"
                        class="absolute inset-0 z-10 flex items-center justify-center bg-black/20 hover:bg-black/30 transition"
                        x-show="!playing"
                        @click="playing = true; $nextTick(() => $refs.player?.play())"
                    >
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/95 text-indigo-600 shadow-lg">
                            <svg class="ml-0.5 h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M8 5.14v13.72L19 12 8 5.14Z" />
                            </svg>
                        </span>
                        <span class="sr-only">{{ __('talenma.dashboard.talent.video_play') }}</span>
                    </button>
                    <video
                        x-ref="player"
                        class="absolute inset-0 h-full w-full object-cover bg-black"
                        controls
                        preload="none"
                        src="{{ $videoUrl }}"
                        x-show="playing"
                        x-cloak
                        @ended="playing = false; $refs.player.currentTime = 0"
                    ></video>
                </div>
            @else
                <div class="relative aspect-video w-full overflow-hidden rounded-xl border border-dashed border-gray-200 bg-slate-50">
                    <x-talent-video-thumbnail :branded="false" class="absolute inset-0 h-full w-full opacity-80" />
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-white/55 px-4 text-center">
                        <p class="text-sm font-medium text-gray-800">{{ __('talenma.dashboard.talent.video_empty_title') }}</p>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
