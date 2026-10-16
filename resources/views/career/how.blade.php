@extends('layouts.public')

@section('title', __('talenma.career_pages.how_meta_title'))
@section('meta_description', __('talenma.career_pages.how_meta_description'))

@section('content')
<section class="bg-white py-16 sm:py-20" data-career-how>
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.career_pages.how_title') }}</h1>
        <p class="mt-3 text-gray-600">{{ __('talenma.career_pages.how_intro') }}</p>

        <ol class="mt-10 space-y-6">
            @foreach (__('talenma.career_pages.how_steps') as $step)
                <li class="flex gap-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">{{ $loop->iteration }}</span>
                    <span>
                        <span class="block font-semibold text-gray-900">{{ $step['title'] }}</span>
                        <span class="mt-1 block text-sm leading-relaxed text-gray-600">{{ $step['text'] }}</span>
                    </span>
                </li>
            @endforeach
        </ol>

        <div class="mt-12 rounded-2xl bg-gray-50 p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.career_pages.how_company_title') }}</h2>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ __('talenma.career_pages.how_company_text') }}</p>
            <a href="{{ route('company.offer') }}" class="mt-4 inline-flex text-sm font-semibold text-indigo-700 hover:text-indigo-800">{{ __('talenma.career_pages.how_company_cta') }}</a>
        </div>
    </div>
</section>
@endsection
