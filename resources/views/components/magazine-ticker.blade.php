@php
    $articles = array_slice(__('talenma.home.magazine_ticker_articles'), 0, 7);
@endphp

<div
    x-data="magazineTicker"
    x-init="init()"
    @scroll.window.passive="update()"
    @resize.window.passive="update()"
>
    <div class="h-[8.25rem]" aria-hidden="true"></div>

    <section
        x-ref="banner"
        x-bind:style="bannerStyle()"
        x-bind:class="{ 'invisible': opacity < 0.01 }"
        class="fixed top-16 left-0 right-0 z-40 pt-4 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm will-change-[opacity,transform]"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-3 sm:gap-4 py-1.5 border-b border-gray-100/90">
                <span class="h-px w-10 sm:w-16 bg-gradient-to-r from-transparent to-gray-200/90"></span>
                <a href="{{ route('magazine.index') }}"
                   class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 hover:text-indigo-500 transition-colors duration-300 ease-in-out">
                    {{ __('talenma.home.magazine_ticker_label') }}
                </a>
                <span class="h-px w-10 sm:w-16 bg-gradient-to-l from-transparent to-gray-200/90"></span>
            </div>

            <div class="relative min-h-[5.5rem] overflow-hidden">
                <div class="absolute inset-y-0 left-0 w-8 bg-gradient-to-r from-white to-transparent z-10 pointer-events-none"></div>
                <div class="absolute inset-y-0 right-0 w-8 bg-gradient-to-l from-white to-transparent z-10 pointer-events-none"></div>

                <div class="magazine-marquee-track flex w-max items-center py-3">
                        @foreach ([1, 2] as $loopPass)
                            @foreach ($articles as $article)
                                <a href="{{ route('magazine.index') }}"
                                   class="group flex items-center gap-3 shrink-0 px-6 sm:px-8 border-r border-gray-100 last:border-r-0 hover:bg-indigo-50/40 transition-colors duration-300">
                                    <div class="flex flex-col justify-center min-w-[12rem] sm:min-w-[16rem] max-w-xs sm:max-w-sm">
                                        <span class="text-[10px] sm:text-[11px] text-gray-400 tracking-wide">{{ $article['date'] ?? '' }}</span>
                                        <span class="mt-0.5 text-sm sm:text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300 line-clamp-1">
                                            {{ $article['title'] }}
                                        </span>
                                        <span class="mt-0.5 text-xs sm:text-sm text-gray-500 line-clamp-1">
                                            {{ $article['subtitle'] }}
                                        </span>
                                    </div>
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg shrink-0 overflow-hidden ring-1 ring-gray-200/80 shadow-sm bg-gradient-to-br {{ $article['thumb'] ?? 'from-indigo-400 to-indigo-600' }}"
                                         aria-hidden="true"></div>
                                </a>
                            @endforeach
                        @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
