@extends('layouts.public')

@section('content')
<article class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ route('magazine.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">{{ __('talenma.magazine.back') }}</a>
        <div class="mt-6 flex items-center gap-3 text-sm text-gray-500">
            <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-medium">{{ __('talenma.magazine.categories.'.$article->category) }}</span>
            <span>{{ $article->published_at?->format('d F Y') }}</span>
        </div>
        <h1 class="mt-4 text-3xl sm:text-4xl font-bold leading-tight">{{ $article->localized_title }}</h1>
        <p class="mt-4 text-lg text-gray-600">{{ $article->localized_excerpt }}</p>
        <div class="mt-10 prose prose-indigo max-w-none text-gray-700 leading-relaxed">
            {!! $article->localized_content !!}
        </div>
    </div>
</article>

@if ($related->isNotEmpty())
<section class="py-12 bg-gray-50 border-t">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <h2 class="text-xl font-bold mb-6">{{ __('talenma.magazine.similar') }}</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($related as $item)
                <a href="{{ route('magazine.show', $item->slug) }}" class="bg-white p-5 rounded-xl border hover:shadow-sm">
                    <h3 class="font-semibold hover:text-indigo-600">{{ $item->localized_title }}</h3>
                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $item->localized_excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
