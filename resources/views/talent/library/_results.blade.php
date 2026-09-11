<section class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.library.catalog_title') }}</h2>
        <p class="text-sm text-gray-500 shrink-0">
            {{ trans_choice('talenma.library.results_count', $books->total(), ['count' => $books->total()]) }}
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($books as $book)
            @include('talent.library._book-card', ['book' => $book])
        @empty
            <div class="sm:col-span-2 lg:col-span-3 rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-16 text-center">
                <p class="text-sm font-medium text-gray-700">{{ __('talenma.library.empty_title') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('talenma.library.empty_hint') }}</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $books->links() }}
    </div>
</section>
