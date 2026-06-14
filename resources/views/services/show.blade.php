@extends('layouts.public')

@section('content')
<section class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ route('services.index') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-800">{{ __('talenma.services.back') }}</a>
        <span class="block mt-6 text-5xl">{{ $service->icon }}</span>
        <h1 class="mt-4 text-3xl font-bold">{{ $service->localized_title }}</h1>
        <p class="mt-4 text-lg text-gray-600">{{ $service->localized_summary }}</p>
        <div class="mt-10 prose max-w-none text-gray-700 leading-relaxed">{!! $service->localized_content !!}</div>
        <div class="mt-10 p-6 bg-gray-50 rounded-xl border">
            <p class="font-semibold">{{ __('talenma.services.interested') }}</p>
            <p class="mt-1 text-sm text-gray-600">{{ __('talenma.services.interested_desc') }}</p>
            <a href="{{ auth()->check() && auth()->user()->isCompany() ? route('recruitment.create') : route('register') }}" class="mt-4 inline-block px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                {{ auth()->check() ? __('talenma.services.request_btn') : __('talenma.services.register_btn') }}
            </a>
        </div>
    </div>
</section>
@endsection
