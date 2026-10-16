@extends('layouts.public')

@section('title', __('talenma.career_pages.tools_meta_title'))
@section('meta_description', __('talenma.career_pages.tools_meta_description'))

@php
    $guest = auth()->guest();
    $tools = [
        [
            'href' => $guest ? route('cv-builder.gate') : route('talent.cv-builder.index'),
            'title' => __('talenma.nav.apps_launcher_cv_builder'),
            'text' => __('talenma.career_pages.tools_cv_text'),
            'soon' => false,
        ],
        [
            'href' => $guest ? route('ats-score.gate') : route('talent.ats-score.index'),
            'title' => __('talenma.nav.apps_launcher_ats_score'),
            'text' => __('talenma.career_pages.tools_ats_text'),
            'soon' => false,
        ],
        [
            'href' => $guest ? route('library.gate') : route('talent.library.index'),
            'title' => __('talenma.nav.apps_launcher_library'),
            'text' => __('talenma.career_pages.tools_library_text'),
            'soon' => false,
        ],
        [
            'href' => null,
            'title' => __('talenma.nav.apps_launcher_videotheque'),
            'text' => __('talenma.career_pages.tools_video_text'),
            'soon' => true,
        ],
        [
            'href' => null,
            'title' => __('talenma.nav.apps_launcher_e_academie'),
            'text' => __('talenma.career_pages.tools_academy_text'),
            'soon' => true,
        ],
    ];
@endphp

@section('content')
<section class="bg-white py-16 sm:py-20" data-career-tools>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.career_pages.tools_title') }}</h1>
        <p class="mt-3 max-w-2xl text-gray-600">{{ __('talenma.career_pages.tools_intro') }}</p>

        <ul class="mt-10 grid gap-4 sm:grid-cols-2">
            @foreach ($tools as $tool)
                <li>
                    @if ($tool['href'])
                        <a href="{{ $tool['href'] }}" class="block h-full rounded-2xl border border-gray-100 p-6 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                            <span class="text-lg font-semibold text-gray-900">{{ $tool['title'] }}</span>
                            <span class="mt-2 block text-sm leading-relaxed text-gray-600">{{ $tool['text'] }}</span>
                        </a>
                    @else
                        <div class="h-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6" aria-disabled="true">
                            <span class="text-lg font-semibold text-gray-500">{{ $tool['title'] }}</span>
                            <span class="ml-2 align-middle text-xs font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.career_pages.tools_soon') }}</span>
                            <span class="mt-2 block text-sm leading-relaxed text-gray-500">{{ $tool['text'] }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endsection
