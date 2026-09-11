@php
    $bookPayload = [
        'id' => $book->id,
        'title' => $book->title,
        'author' => $book->author,
        'description' => $book->description,
        'category_path' => $book->category_path,
        'cover_url' => $book->coverUrl(),
        'size' => $book->formattedSize(),
        'download_url' => route('talent.library.download', $book),
    ];
@endphp

<article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm flex gap-4">
    <div class="relative h-24 w-16 shrink-0 overflow-hidden rounded-lg bg-gradient-to-br from-amber-100 via-orange-50 to-amber-200">
        @if ($book->coverUrl())
            <img
                src="{{ $book->coverUrl() }}"
                alt="{{ $book->title }}"
                class="absolute inset-0 h-full w-full object-cover"
                loading="lazy"
            >
        @else
            <div class="absolute inset-0 flex items-center justify-center text-amber-800/60">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
            </div>
        @endif
    </div>

    <div class="min-w-0 flex-1 flex flex-col">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700/80 truncate">{{ $book->category_path }}</p>
        <h2 class="mt-1 text-base font-semibold text-gray-900 leading-snug line-clamp-2">{{ $book->title }}</h2>
        @if ($book->author)
            <p class="mt-0.5 text-sm text-gray-500 truncate">{{ $book->author }}</p>
        @endif
        @if ($book->description)
            <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $book->description }}</p>
        @endif
        <div class="mt-auto pt-3 flex items-center justify-between gap-3">
            <span class="text-xs text-gray-400">{{ $book->formattedSize() }}</span>
            <button
                type="button"
                data-library-book="{{ base64_encode(json_encode($bookPayload, JSON_UNESCAPED_UNICODE)) }}"
                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-700"
            >
                {{ __('talenma.library.discover') }}
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>
    </div>
</article>
