@extends('layouts.public')

@section('content')
    <article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            {{ __('talenma.blog.back_to_list') }}
        </a>

        <header class="mt-6">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">
                {{ optional($post->published_at)->translatedFormat('d M Y') }}
            </p>
            <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 leading-tight">
                {{ $post->title }}
            </h1>
            <p class="mt-4 text-base sm:text-lg text-gray-600 leading-relaxed">
                {{ $post->excerpt }}
            </p>
        </header>

        @if ($post->coverUrl())
            <div class="mt-8 overflow-hidden rounded-2xl border border-gray-100 bg-gray-50">
                <img src="{{ $post->coverUrl() }}" alt="" class="w-full max-h-[28rem] object-cover">
            </div>
        @endif

        <div class="mt-10 space-y-4 text-base text-gray-700 leading-relaxed [&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:text-gray-900 [&_h3]:mt-6 [&_h3]:text-lg [&_h3]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-indigo-600 [&_a]:underline [&_img]:rounded-xl [&_img]:max-w-full">
            {!! $post->body !!}
        </div>
    </article>
@endsection
