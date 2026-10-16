@extends('layouts.public')

@section('title', __('talenma.career_pages.offers_meta_title'))
@section('meta_description', __('talenma.career_pages.offers_meta_description'))

@section('content')
<section class="bg-white py-16 sm:py-20" data-career-offers>
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.career_pages.offers_title') }}</h1>
        <p class="mt-3 max-w-2xl text-gray-600">{{ __('talenma.career_pages.offers_intro') }}</p>

        @if ($jobs->isEmpty())
            <p class="mt-12 rounded-2xl bg-gray-50 px-6 py-10 text-center text-gray-600">{{ __('talenma.career_pages.offers_empty') }}</p>
        @else
            <ul class="mt-10 divide-y divide-gray-100 rounded-2xl border border-gray-100">
                @foreach ($jobs as $job)
                    <li>
                        <a href="{{ $job['url'] }}" class="block px-5 py-5 transition hover:bg-indigo-50/40 sm:px-6">
                            <span class="text-lg font-semibold text-gray-900">{{ $job['title'] }}</span>
                            <span class="mt-1 block text-sm text-gray-600">
                                {{ $job['company'] }}
                                @if ($job['sector'])
                                    <span class="text-gray-300"> · </span>{{ $job['sector'] }}
                                @endif
                                @if ($job['date'])
                                    <span class="text-gray-300"> · </span>{{ $job['date'] }}
                                @endif
                            </span>
                            @if ($job['excerpt'] !== '')
                                <span class="mt-2 block text-sm leading-relaxed text-gray-500">{{ $job['excerpt'] }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-8">{{ $jobs->links() }}</div>
        @endif
    </div>
</section>
@endsection
