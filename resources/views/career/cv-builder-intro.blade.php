@extends('layouts.public')

@section('title', __('talenma.cv_builder_intro.meta_title'))
@section('meta_description', __('talenma.cv_builder_intro.meta_description'))

@section('content')
<section class="bg-white py-12 sm:py-16" data-cv-builder-intro>
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-indigo-500">
            {{ __('talenma.cv_builder_intro.eyebrow') }}
        </p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            {{ __('talenma.cv_builder_intro.title') }}
        </h1>
        <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            {{ __('talenma.cv_builder_intro.lead') }}
        </p>

        <div
            class="mt-8"
            x-data="cvBuilderIntro()"
            @keydown.left.prevent="go(-1)"
            @keydown.right.prevent="go(1)"
        >
            <div class="overflow-hidden rounded-[1.6rem] border border-indigo-100 bg-white shadow-[0_16px_40px_rgba(79,70,229,0.10)]">
                <div
                    x-ref="track"
                    class="flex items-start snap-x snap-mandatory overflow-x-auto overflow-y-hidden scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    tabindex="0"
                    @scroll.debounce.50ms="sync()"
                >
                    @php
                        $cvIntroShots = [
                            'images/cv-builder/intro/step-template.jpg',
                            'images/cv-builder/intro/step-edit.jpg',
                            'images/cv-builder/intro/step-preview.jpg',
                            'images/cv-builder/intro/step-export.jpg',
                        ];
                    @endphp
                    @foreach (__('talenma.cv_builder_intro.steps') as $step)
                        <article class="box-border flex w-full shrink-0 snap-center flex-col overflow-hidden px-5 py-6 sm:px-8 sm:py-8" id="cv-builder-step-{{ $loop->iteration }}">
                            <img
                                src="{{ asset($cvIntroShots[$loop->index]) }}"
                                alt="{{ $step['title'] }}"
                                class="w-full rounded-xl border border-slate-100 bg-slate-50"
                            >
                            <div class="mt-auto pt-5">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-500">
                                    {{ $loop->iteration }} / {{ $loop->count }}
                                </p>
                                <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ $step['title'] }}</h2>
                                <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">{{ $step['text'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-slate-100 px-4 py-3 sm:px-6">
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                        @click="go(-1)"
                        :disabled="index === 0"
                        aria-label="{{ __('talenma.cv_builder_intro.prev') }}"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="flex items-center gap-1.5" role="tablist" aria-label="{{ __('talenma.cv_builder_intro.eyebrow') }}">
                        @foreach (__('talenma.cv_builder_intro.steps') as $step)
                            <button
                                type="button"
                                class="h-2 rounded-full transition"
                                :class="index === {{ $loop->index }} ? 'w-5 bg-indigo-600' : 'w-2 bg-slate-200'"
                                @click="goTo({{ $loop->index }})"
                                role="tab"
                                :aria-selected="index === {{ $loop->index }}"
                                aria-label="{{ __('talenma.cv_builder_intro.dot', ['n' => $loop->iteration]) }}"
                            ></button>
                        @endforeach
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                        @click="go(1)"
                        :disabled="index >= count - 1"
                        aria-label="{{ __('talenma.cv_builder_intro.next') }}"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
            <a href="{{ route('cv-builder.gate') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                {{ __('talenma.cv_builder_intro.login') }}
            </a>
            <a href="{{ route('register', ['role' => 'dev', 'from_tool' => 'cv-builder']) }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500">
                {{ __('talenma.cv_builder_intro.register') }}
            </a>
        </div>
        <p class="mt-3 text-sm text-slate-500">{{ __('talenma.cv_builder_intro.note') }}</p>
    </div>
</section>
@endsection
