@props(['items'])

@php
    $postCards = $items->map(fn ($item) => [
        'title' => $item->title,
        'subtitle' => $item->subtitle,
        'url' => $item->url,
        'thumbnail' => $item->thumbnailUrl(),
        'network' => $item->localizedNetworkLabel(),
        'date' => $item->created_at->translatedFormat('d M Y'),
        'readLabel' => __('talenma.home.social_feed_read_external'),
    ])->values();
@endphp

<section id="publications" class="py-20 scroll-mt-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">{{ __('talenma.home.social_feed_eyebrow') }}</p>
            <h2 class="mt-3 text-3xl font-bold text-gray-900">{{ __('talenma.home.social_feed_title') }}</h2>
        </div>

        @if ($postCards->isEmpty())
            <p class="mt-10 rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-12 text-center text-sm text-gray-500">
                {{ __('talenma.home.social_feed_empty') }}
            </p>
        @else
            <div
                class="relative mt-10"
                x-data="socialPostsSlider({
                    items: @js($postCards),
                    prevLabel: @js(__('talenma.home.social_feed_slider_prev')),
                    nextLabel: @js(__('talenma.home.social_feed_slider_next')),
                })"
                x-init="init()"
            >
                <button
                    type="button"
                    class="social-posts-slider-nav social-posts-slider-nav--left"
                    @click="prev()"
                    :disabled="!canPrev"
                    :aria-label="prevLabel"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <button
                    type="button"
                    class="social-posts-slider-nav social-posts-slider-nav--right"
                    @click="next()"
                    :disabled="!canNext"
                    :aria-label="nextLabel"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-ref="viewport" class="overflow-hidden px-1">
                    <div
                        x-ref="track"
                        class="social-posts-slider-track flex gap-5"
                        :style="trackStyle()"
                    >
                        <template x-for="(item, index) in items" :key="index">
                            <a :href="item.url"
                               target="_blank"
                               rel="noopener noreferrer"
                               data-social-post-card
                               class="group flex shrink-0 flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white transition hover:border-indigo-200 hover:shadow-lg"
                               :style="cardStyle()">
                                <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-indigo-400 to-indigo-600">
                                    <img :src="item.thumbnail"
                                         alt=""
                                         class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                         loading="lazy"
                                         decoding="async"
                                         x-show="item.thumbnail"
                                         x-cloak
                                         @load="measure()">
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/50 via-transparent to-transparent"></div>
                                    <span class="absolute left-3 top-3 inline-flex items-center rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-700 shadow-sm"
                                          x-text="item.network"></span>
                                </div>

                                <div class="flex flex-1 flex-col p-5">
                                    <p class="text-xs font-medium text-gray-400" x-text="item.date"></p>
                                    <h3 class="mt-2 text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2"
                                        x-text="item.title"></h3>
                                    <p class="mt-2 flex-1 text-sm text-gray-600 leading-relaxed line-clamp-3"
                                       x-text="item.subtitle"></p>
                                    <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600">
                                        <span x-text="item.readLabel"></span>
                                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H19v5.5M19 5l-8.25 8.25M19 19H5V5"/>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
