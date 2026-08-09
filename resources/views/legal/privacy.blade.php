@extends('layouts.public')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
            {{ __('talenma.privacy.title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('talenma.privacy.updated_at', ['date' => __('talenma.privacy.updated_date')]) }}
        </p>

        <div class="mt-8 space-y-8 text-sm sm:text-base text-gray-700 leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.who.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.who.body') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.what.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.what.body') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.why.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.why.body') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.share.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.share.body') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.rights.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.rights.body') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.contact.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.contact.body') }}</p>
            </section>
        </div>
    </div>
@endsection
