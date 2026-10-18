@extends('layouts.public')

@section('title', __('talenma.career_pages.tools_meta_title'))
@section('meta_description', __('talenma.career_pages.tools_meta_description'))

@php
    $tools = [
        [
            'href' => route('cv-builder.intro'),
            'title' => __('talenma.career_pages.tools_cv_title'),
            'text' => __('talenma.career_pages.tools_cv_text'),
            'tone' => 'indigo',
            'icon' => 'cv',
        ],
        [
            'href' => route('ats-score.intro'),
            'title' => __('talenma.nav.apps_launcher_ats_score'),
            'text' => __('talenma.career_pages.tools_ats_text'),
            'tone' => 'emerald',
            'icon' => 'ats',
        ],
        [
            'href' => route('library.intro'),
            'title' => __('talenma.nav.apps_launcher_library'),
            'text' => __('talenma.career_pages.tools_library_text'),
            'tone' => 'amber',
            'icon' => 'library',
        ],
    ];
    $soon = [
        [
            'title' => __('talenma.nav.apps_launcher_videotheque'),
            'text' => __('talenma.career_pages.tools_video_text'),
        ],
        [
            'title' => __('talenma.nav.apps_launcher_e_academie'),
            'text' => __('talenma.career_pages.tools_academy_text'),
        ],
    ];
    $tones = [
        'indigo' => 'bg-indigo-50 text-indigo-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-700',
    ];
@endphp

@section('content')
<section class="bg-white py-14 sm:py-20" data-career-tools>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
            {{ __('talenma.nav.career_tools') }}
        </p>
        <h1 class="mt-3 max-w-2xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            {{ __('talenma.career_pages.tools_title') }}
        </h1>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            {{ __('talenma.career_pages.tools_intro') }}
        </p>

        <ul class="mt-10 grid gap-4 md:grid-cols-3">
            @foreach ($tools as $tool)
                <li>
                    <a href="{{ $tool['href'] }}" class="group flex h-full flex-col rounded-[1.6rem] border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.04)] transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-[0_16px_40px_rgba(79,70,229,0.10)]">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl {{ $tones[$tool['tone']] }}">
                            @if ($tool['icon'] === 'cv')
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3.5A1.5 1.5 0 0 1 5.5 2h6.1a1.5 1.5 0 0 1 1.06.44l2.9 2.9A1.5 1.5 0 0 1 16 6.4V16.5A1.5 1.5 0 0 1 14.5 18h-9A1.5 1.5 0 0 1 4 16.5v-13Z"/></svg>
                            @elseif ($tool['icon'] === 'ats')
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                            @endif
                        </span>
                        <h2 class="mt-5 text-xl font-bold tracking-tight text-slate-900">{{ $tool['title'] }}</h2>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $tool['text'] }}</p>
                        <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-700">
                            {{ __('talenma.career_pages.tools_discover') }}
                            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                {{ __('talenma.career_pages.tools_soon') }}
            </p>
            <ul class="mt-3 grid gap-4 sm:grid-cols-2">
                @foreach ($soon as $tool)
                    <li>
                        <div class="h-full rounded-[1.4rem] border border-dashed border-slate-200 bg-slate-50/80 p-5" aria-disabled="true">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="text-base font-semibold text-slate-500">{{ $tool['title'] }}</h2>
                                <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-400 ring-1 ring-slate-200">
                                    {{ __('talenma.career_pages.tools_soon') }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $tool['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
@endsection
