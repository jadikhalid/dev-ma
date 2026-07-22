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
    <p class="text-lg font-bold uppercase tracking-wide text-indigo-600">{{ __('talenma.dashboard.talent.video_snapshot') }}</p>

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
