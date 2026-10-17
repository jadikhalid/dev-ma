@if ($paginator->total() > 0)
    @php
        $buttonClass = 'inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium ring-1 ring-slate-200 transition';
        $pageClass = 'inline-flex min-w-[2.25rem] items-center justify-center rounded-lg px-2.5 py-1.5 text-sm font-semibold ring-1 ring-slate-200 transition';
    @endphp
    <nav role="navigation" aria-label="{{ __('talenma.jobs.pagination_label') }}" class="flex flex-col items-center gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-between">
        <p class="text-sm text-slate-600">
            {{ __('talenma.jobs.pagination_summary', [
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </p>

        @if ($paginator->hasPages())
            <div class="flex flex-wrap items-center justify-center gap-1">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="{{ $buttonClass }} cursor-not-allowed bg-white text-slate-400 opacity-60">{{ __('talenma.jobs.pagination_previous') }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" data-offers-page="{{ $paginator->currentPage() - 1 }}" class="{{ $buttonClass }} bg-white text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">{{ __('talenma.jobs.pagination_previous') }}</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-1.5 text-sm text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $pageClass }} bg-indigo-600 text-white ring-indigo-600 shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" data-offers-page="{{ $page }}" class="{{ $pageClass }} bg-white text-slate-700 hover:bg-indigo-50 hover:text-indigo-700" aria-label="{{ __('talenma.library.pagination_go_to', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" data-offers-page="{{ $paginator->currentPage() + 1 }}" class="{{ $buttonClass }} bg-white text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">{{ __('talenma.jobs.pagination_next') }}</a>
                @else
                    <span aria-disabled="true" class="{{ $buttonClass }} cursor-not-allowed bg-white text-slate-400 opacity-60">{{ __('talenma.jobs.pagination_next') }}</span>
                @endif
            </div>
        @endif
    </nav>
@endif
