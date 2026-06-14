@extends('layouts.public')

@section('content')
<section class="bg-gradient-to-br from-emerald-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold">{{ __('talenma.services.title') }}</h1>
        <p class="mt-4 text-gray-600 max-w-2xl">{{ __('talenma.services.subtitle') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($services as $service)
            <a href="{{ route('services.show', $service->slug) }}" class="group p-8 bg-white rounded-2xl border shadow-sm hover:shadow-md hover:border-indigo-200 transition">
                <span class="text-4xl">{{ $service->icon }}</span>
                <h2 class="mt-4 text-xl font-bold group-hover:text-indigo-600">{{ $service->localized_title }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $service->localized_summary }}</p>
                <span class="mt-4 inline-block text-sm font-semibold text-indigo-600">{{ __('talenma.services.learn_more') }}</span>
            </a>
        @endforeach
    </div>

    <div class="max-w-3xl mx-auto mt-16 p-8 bg-indigo-600 text-white rounded-2xl text-center">
        <h2 class="text-2xl font-bold">{{ __('talenma.services.cta_title') }}</h2>
        <p class="mt-3 text-indigo-100">{{ __('talenma.services.cta_desc') }}</p>
        @auth
            @if (auth()->user()->isCompany())
                <a href="{{ route('recruitment.create') }}?mode=intermediary" class="mt-6 inline-block px-6 py-3 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50">{{ __('talenma.services.cta_request') }}</a>
            @endif
        @else
            <a href="{{ route('register') }}" class="mt-6 inline-block px-6 py-3 bg-white text-indigo-700 font-semibold rounded-xl hover:bg-indigo-50">{{ __('talenma.services.cta_register') }}</a>
        @endauth
    </div>
</section>
@endsection
