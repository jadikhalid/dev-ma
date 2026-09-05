@extends('layouts.public')

@section('content')
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-gradient-to-b from-indigo-50 via-white to-transparent"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">{{ __('talenma.blog.badge') }}</p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                    {{ __('talenma.blog.index_title') }}
                </h1>
                <p class="mt-3 text-base text-gray-600 leading-relaxed">
                    {{ __('talenma.blog.index_subtitle') }}
                </p>
            </div>

            @if ($posts->isEmpty())
                <div class="mt-12 rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-14 text-center">
                    <p class="text-sm text-gray-600">{{ __('talenma.blog.empty') }}</p>
                </div>
            @else
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($posts as $post)
                        <a
                            href="{{ route('blog.show', $post->slug) }}"
                            class="group flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md"
                        >
                            @if ($post->coverUrl())
                                <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                                    <img
                                        src="{{ $post->coverUrl() }}"
                                        alt=""
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                    >
                                </div>
                            @else
                                <div class="aspect-[16/10] bg-gradient-to-br from-indigo-100 via-white to-teal-50"></div>
                            @endif
                            <div class="flex flex-1 flex-col p-5">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-indigo-600">
                                    {{ optional($post->published_at)->translatedFormat('d M Y') }}
                                </p>
                                <h2 class="mt-2 text-lg font-bold text-gray-900 leading-snug group-hover:text-indigo-800">
                                    {{ $post->title }}
                                </h2>
                                <p class="mt-2 text-sm text-gray-600 leading-relaxed line-clamp-3">
                                    {{ $post->excerpt }}
                                </p>
                                <span class="mt-4 text-sm font-semibold text-indigo-600">
                                    {{ __('talenma.blog.read_more') }} →
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
