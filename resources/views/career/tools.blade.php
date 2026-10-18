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
    $panels = [
        'indigo' => 'from-indigo-100/80 via-white to-violet-50',
        'emerald' => 'from-emerald-100/80 via-white to-teal-50',
        'amber' => 'from-amber-100/80 via-white to-orange-50',
    ];
    $badges = [
        'indigo' => 'bg-white text-indigo-700 ring-indigo-100',
        'emerald' => 'bg-white text-emerald-700 ring-emerald-100',
        'amber' => 'bg-white text-amber-700 ring-amber-100',
    ];
@endphp

@section('content')
<section class="relative overflow-hidden bg-slate-50 pb-16 pt-12 sm:pb-20 sm:pt-14" data-career-tools>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-indigo-50 to-transparent" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[1.75rem] bg-white px-6 py-7 shadow-[0_10px_30px_rgba(15,23,42,0.04)] ring-1 ring-slate-200/80 sm:px-8 sm:py-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-600">
                {{ __('talenma.nav.career_tools') }}
            </p>
            <h1 class="mt-2 max-w-2xl text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                {{ __('talenma.career_pages.tools_title') }}
            </h1>
            <p class="mt-3 max-w-xl text-base leading-relaxed text-slate-600">
                {{ __('talenma.career_pages.tools_intro') }}
            </p>
        </div>

        <ul class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach ($tools as $tool)
                <li>
                    <a href="{{ $tool['href'] }}" class="group flex h-full flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-[0_16px_40px_rgba(15,23,42,0.06)] ring-1 ring-slate-200/80 transition duration-200 hover:-translate-y-1 hover:shadow-[0_22px_48px_rgba(79,70,229,0.14)] hover:ring-indigo-200">
                        <div class="relative h-40 overflow-hidden bg-gradient-to-br {{ $panels[$tool['tone']] }}" aria-hidden="true">
                            @if ($tool['icon'] === 'cv')
                                <span class="absolute left-1/2 top-8 h-24 w-[4.5rem] -translate-x-[130%] -rotate-12 rounded-lg bg-white p-2 shadow-md ring-1 ring-slate-200/80">
                                    <span class="block h-1.5 w-8 rounded-full bg-indigo-400"></span>
                                    <span class="mt-2 block h-1 w-full rounded-full bg-slate-200"></span>
                                    <span class="mt-1 block h-1 w-4/5 rounded-full bg-slate-200"></span>
                                    <span class="mt-2 block h-1 w-full rounded-full bg-slate-200"></span>
                                </span>
                                <span class="absolute left-1/2 top-6 h-28 w-[4.75rem] -translate-x-1/2 rounded-lg bg-white p-2.5 shadow-lg ring-1 ring-slate-200/80">
                                    <span class="block h-1.5 w-10 rounded-full bg-indigo-500"></span>
                                    <span class="mt-2.5 block h-1 w-full rounded-full bg-slate-200"></span>
                                    <span class="mt-1 block h-1 w-11/12 rounded-full bg-slate-200"></span>
                                    <span class="mt-2.5 block h-1 w-7 rounded-full bg-violet-400"></span>
                                    <span class="mt-1 block h-1 w-full rounded-full bg-slate-200"></span>
                                    <span class="mt-1 block h-1 w-2/3 rounded-full bg-slate-200"></span>
                                </span>
                                <span class="absolute left-1/2 top-8 h-24 w-[4.5rem] translate-x-[30%] rotate-12 rounded-lg bg-white p-2 shadow-md ring-1 ring-slate-200/80">
                                    <span class="block h-5 rounded-md bg-violet-400"></span>
                                    <span class="mt-2 block h-1 w-full rounded-full bg-slate-200"></span>
                                    <span class="mt-1 block h-1 w-3/4 rounded-full bg-slate-200"></span>
                                </span>
                            @elseif ($tool['icon'] === 'ats')
                                <span class="absolute left-1/2 top-6 h-[6.5rem] w-[6.5rem] -translate-x-1/2 rounded-full bg-[conic-gradient(#34d399_0_72%,#d1fae5_72%_100%)] shadow-lg">
                                    <span class="absolute inset-[10px] rounded-full bg-white"></span>
                                    <span class="absolute left-1/2 top-1/2 h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-500"></span>
                                </span>
                            @else
                                <span class="absolute bottom-0 left-1/2 flex -translate-x-1/2 items-end gap-1.5">
                                    <span class="h-24 w-7 rounded-t-md bg-amber-300"></span>
                                    <span class="h-32 w-8 rounded-t-md bg-amber-500"></span>
                                    <span class="h-28 w-7 rounded-t-md bg-orange-300"></span>
                                    <span class="mb-4 h-16 w-14 rounded-lg bg-white p-2 shadow-md ring-1 ring-amber-100">
                                        <span class="block h-1 w-8 rounded-full bg-amber-300"></span>
                                        <span class="mt-1.5 block h-1 w-full rounded-full bg-slate-200"></span>
                                        <span class="mt-1 block h-1 w-4/5 rounded-full bg-slate-200"></span>
                                    </span>
                                </span>
                            @endif
                            <span class="absolute left-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-xl shadow-sm ring-1 {{ $badges[$tool['tone']] }}">
                                @if ($tool['icon'] === 'cv')
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M4 3.5A1.5 1.5 0 0 1 5.5 2h6.1a1.5 1.5 0 0 1 1.06.44l2.9 2.9A1.5 1.5 0 0 1 16 6.4V16.5A1.5 1.5 0 0 1 14.5 18h-9A1.5 1.5 0 0 1 4 16.5v-13Z"/></svg>
                                @elseif ($tool['icon'] === 'ats')
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                                @endif
                            </span>
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $tool['title'] }}</h2>
                            <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $tool['text'] }}</p>
                            <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-700">
                                {{ __('talenma.career_pages.tools_discover') }}
                                <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                            </span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-8 rounded-[1.75rem] bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.04)] ring-1 ring-slate-200/80 sm:p-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                {{ __('talenma.career_pages.tools_soon') }}
            </p>
            <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ($soon as $tool)
                    <li>
                        <div class="flex h-full gap-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-4" aria-disabled="true">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-400 ring-1 ring-slate-200" aria-hidden="true">
                                @if ($loop->first)
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9A2.25 2.25 0 0 0 13.5 5.25h-9A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z"/></svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 class="text-base font-semibold text-slate-600">{{ $tool['title'] }}</h2>
                                    <span class="shrink-0 rounded-full bg-white px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-400 ring-1 ring-slate-200">
                                        {{ __('talenma.career_pages.tools_soon') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $tool['text'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
@endsection
