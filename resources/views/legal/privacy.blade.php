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
                <h2 class="text-lg font-semibold text-gray-900">{{ __('talenma.privacy.sections.cookies.title') }}</h2>
                <p class="mt-2">{{ __('talenma.privacy.sections.cookies.intro') }}</p>
                <div class="mt-4 overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-900">
                            <tr>
                                <th scope="col" class="px-3 py-2 font-semibold">{{ __('talenma.privacy.sections.cookies.table.name') }}</th>
                                <th scope="col" class="px-3 py-2 font-semibold">{{ __('talenma.privacy.sections.cookies.table.purpose') }}</th>
                                <th scope="col" class="px-3 py-2 font-semibold">{{ __('talenma.privacy.sections.cookies.table.duration') }}</th>
                                <th scope="col" class="px-3 py-2 font-semibold">{{ __('talenma.privacy.sections.cookies.table.type') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach (__('talenma.privacy.sections.cookies.rows') as $row)
                                <tr class="align-top">
                                    <td class="px-3 py-2 font-medium text-gray-900">{{ $row['name'] }}</td>
                                    <td class="px-3 py-2">{{ $row['purpose'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $row['duration'] }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $row['type'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-4">{{ __('talenma.privacy.sections.cookies.note_ip') }}</p>
                <p class="mt-2">{{ __('talenma.privacy.sections.cookies.note_manage') }}</p>
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
