@extends('layouts.public')

@section('title', __('talenma.footer.contact_meta_title'))

@section('content')
<section class="bg-white py-16 sm:py-20">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('talenma.footer.contact') }}</h1>
        <p class="mt-3 text-gray-600">{{ __('talenma.footer.contact_intro') }}</p>
    </div>
</section>
@endsection
