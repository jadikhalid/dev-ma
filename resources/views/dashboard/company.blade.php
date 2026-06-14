<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.dashboard.company.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.dashboard.company.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('recruitment_sent'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.dashboard.company.request_sent') }}</div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border p-6 sm:p-8">
                    <h3 class="text-xl font-bold">{{ __('talenma.dashboard.company.recruit_title') }}</h3>
                    <p class="mt-2 text-gray-600 text-sm">{{ __('talenma.dashboard.company.recruit_desc') }}</p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('company.search') }}" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 text-center">{{ __('talenma.dashboard.company.browse') }}</a>
                        <a href="{{ route('recruitment.create') }}" class="px-5 py-2.5 border border-indigo-200 text-indigo-700 text-sm font-semibold rounded-lg hover:bg-indigo-50 text-center">{{ __('talenma.dashboard.company.intermediary') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border p-6 sm:p-8">
                    <h3 class="text-xl font-bold">{{ __('talenma.dashboard.company.morocco_title') }}</h3>
                    <p class="mt-2 text-gray-600 text-sm">{{ __('talenma.dashboard.company.morocco_desc') }}</p>
                    <a href="{{ route('services.index') }}" class="mt-4 inline-block text-indigo-600 font-semibold text-sm hover:text-indigo-800">{{ __('talenma.dashboard.company.morocco_link') }}</a>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-2xl border p-6">
                    <h4 class="font-semibold">{{ __('talenma.dashboard.company.my_company') }}</h4>
                    <p class="mt-2 text-sm text-gray-600">{{ Auth::user()->companyProfile?->company_name ?? __('talenma.dashboard.company.complete_profile') }}</p>
                    <a href="{{ route('company.profile.edit') }}" class="mt-3 inline-block text-sm text-indigo-600 font-medium">{{ __('talenma.dashboard.company.edit_company') }}</a>
                </div>

                @if ($recentRequests->isNotEmpty())
                <div class="bg-white rounded-2xl border p-6">
                    <h4 class="font-semibold">{{ __('talenma.dashboard.company.recent_requests') }}</h4>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($recentRequests as $req)
                            <li class="text-gray-600">
                                <span class="font-medium text-gray-900">{{ $req->subject }}</span>
                                <span class="text-xs text-gray-400"> — {{ $req->mode === 'intermediary' ? __('talenma.talents.intermediary') : __('talenma.recruitment.mode_direct') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
