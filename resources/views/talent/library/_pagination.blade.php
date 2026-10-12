@if ($paginator->hasPages())
    @php
        $buttonClass = 'inline-flex items-center rounded-lg px-3 sm:px-4 py-2 text-sm font-medium ring-1 ring-gray-200 transition';
        $pageClass = 'inline-flex min-w-[2.25rem] items-center justify-center rounded-lg px-2.5 py-2 text-sm font-semibold ring-1 ring-gray-200 transition';
    @endphp
    <nav role="navigation" aria-label="{{ __('talenma.library.pagination_label') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-100 pt-4">
        <p class="text-sm text-gray-600" data-library-pagination-summary>
            {{ __('talenma.library.pagination_summary', [
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </p>

        <div class="flex flex-wrap justify-center gap-1.5 items-center">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="{{ $buttonClass }} bg-white text-gray-400 cursor-not-allowed opacity-60">{{ __('talenma.library.pagination_previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $buttonClass }} bg-white text-gray-800 hover:bg-amber-50 hover:text-amber-800 hover:ring-amber-200" data-library-page-link>{{ __('talenma.library.pagination_previous') }}</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="px-1 text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="{{ $pageClass }} bg-amber-600 text-white ring-amber-600 shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="{{ $pageClass }} bg-white text-gray-700 hover:bg-amber-50 hover:text-amber-800 hover:ring-amber-200" aria-label="{{ __('talenma.library.pagination_go_to', ['page' => $page]) }}" data-library-page-link>{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $buttonClass }} bg-white text-gray-800 hover:bg-amber-50 hover:text-amber-800 hover:ring-amber-200" data-library-page-link>{{ __('talenma.library.pagination_next') }}</a>
            @else
                <span aria-disabled="true" class="{{ $buttonClass }} bg-white text-gray-400 cursor-not-allowed opacity-60">{{ __('talenma.library.pagination_next') }}</span>
            @endif
        </div>
    </nav>
@endif
