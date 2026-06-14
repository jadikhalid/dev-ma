@extends('layouts.public')

@section('content')
<section class="bg-gradient-to-br from-indigo-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold">{{ __('talenma.magazine.title') }}</h1>
        <p class="mt-4 text-gray-600 max-w-2xl mx-auto">{{ __('talenma.magazine.subtitle') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($articles as $article)
                <article class="bg-white rounded-2xl border shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-6">
                        <span class="text-4xl">{{ $article->cover_emoji ?? '📰' }}</span>
                        <div class="mt-4 flex items-center gap-2 text-xs">
                            <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">{{ __('talenma.magazine.categories.'.$article->category) }}</span>
                            <span class="text-gray-400">{{ $article->published_at?->format('d M Y') }}</span>
                        </div>
                        <h2 class="mt-3 text-lg font-bold">
                            <a href="{{ route('magazine.show', $article->slug) }}" class="hover:text-indigo-600">{{ $article->localized_title }}</a>
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ $article->localized_excerpt }}</p>
                        <a href="{{ route('magazine.show', $article->slug) }}" class="mt-4 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">{{ __('talenma.magazine.read_more') }}</a>
                    </div>
                </article>
            @empty
                <p class="col-span-3 text-center text-gray-500 py-12">{{ __('talenma.magazine.empty') }}</p>
            @endforelse
        </div>
        <div class="mt-10">{{ $articles->links() }}</div>
    </div>
</section>
@endsection
