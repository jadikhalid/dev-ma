<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.dashboard.talent.title') }}</h2>
            <p class="text-sm text-gray-500">{{ __('talenma.dashboard.talent.subtitle') }}</p>
        </div>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('payment_success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">{{ __('talenma.dashboard.talent.payment_success') }}</div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl border p-6 sm:p-8">
                <h3 class="text-xl font-bold">{{ __('talenma.dashboard.talent.subscription_title') }}</h3>
                <p class="mt-2 text-gray-600 text-sm">{!! __('talenma.dashboard.talent.subscription_desc', ['price' => '<strong>'.__('talenma.common.price').'</strong>']) !!}</p>

                <div class="mt-6 p-4 rounded-xl {{ Auth::user()->hasActiveSubscription() ? 'bg-green-50 border border-green-200' : 'bg-amber-50 border border-amber-200' }}">
                    @if (Auth::user()->hasActiveSubscription())
                        <p class="font-semibold text-green-800">{{ __('talenma.dashboard.talent.active') }}</p>
                        <p class="text-sm text-green-700 mt-1">{{ __('talenma.dashboard.talent.expires', ['date' => Auth::user()->subscription_expires_at->format('d/m/Y')]) }}</p>
                    @else
                        <p class="font-semibold text-amber-800">{{ __('talenma.dashboard.talent.inactive') }}</p>
                        <p class="text-sm text-amber-700 mt-1">{{ __('talenma.dashboard.talent.inactive_desc') }}</p>
                        @if (app()->environment('local', 'testing'))
                            <form action="{{ route('payment.simulate') }}" method="POST" class="mt-4">@csrf
                                <button class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">{{ __('talenma.dashboard.talent.activate') }}</button>
                            </form>
                        @endif
                    @endif
                </div>

                <a href="{{ route('profile.details.edit') }}" class="mt-6 inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-800">{{ __('talenma.dashboard.talent.edit_profile') }}</a>
            </div>

            <div class="bg-white rounded-2xl border p-6">
                <h4 class="font-semibold">{{ __('talenma.dashboard.talent.checklist') }}</h4>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="{{ Auth::user()->profile?->title ? 'text-green-700' : 'text-gray-500' }}">{{ Auth::user()->profile?->title ? '✓' : '○' }} {{ __('talenma.dashboard.talent.check_title') }}</li>
                    <li class="{{ Auth::user()->profile?->bio ? 'text-green-700' : 'text-gray-500' }}">{{ Auth::user()->profile?->bio ? '✓' : '○' }} {{ __('talenma.dashboard.talent.check_bio') }}</li>
                    <li class="{{ Auth::user()->profile?->daily_rate_eur ? 'text-green-700' : 'text-gray-500' }}">{{ Auth::user()->profile?->daily_rate_eur ? '✓' : '○' }} {{ __('talenma.dashboard.talent.check_rate') }}</li>
                    <li class="{{ Auth::user()->hasActiveSubscription() ? 'text-green-700' : 'text-gray-500' }}">{{ Auth::user()->hasActiveSubscription() ? '✓' : '○' }} {{ __('talenma.dashboard.talent.check_sub') }}</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
