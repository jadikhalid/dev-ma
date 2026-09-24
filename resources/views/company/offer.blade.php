@extends('layouts.public')

@section('title', __('talenma.company_offer.meta_title'))
@section('meta_description', __('talenma.company_offer.meta_description'))

@section('content')
@php
    $initialTab = request('tab') === 'trial'
        || filled(old('sector'))
        || filled(old('company_description'))
        || filled(old('company_country'))
            ? 'trial'
            : 'demo';
@endphp

<section
    class="relative overflow-hidden border-b border-indigo-100/80 bg-gradient-to-br from-indigo-50 via-white to-teal-50"
    x-data="{
        tab: @js($initialTab),
        init() {
            if (window.location.hash === '#trial') this.tab = 'trial';
            if (window.location.hash === '#demo') this.tab = 'demo';
            this.$watch('tab', (value) => {
                const hash = value === 'trial' ? '#trial' : '#demo';
                if (window.location.hash !== hash) {
                    history.replaceState(null, '', hash);
                }
            });
        },
        setTab(value) {
            this.tab = value;
        }
    }"
>
    <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-amber-200/30 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-28 -left-16 h-80 w-80 rounded-full bg-indigo-200/40 blur-3xl" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        <p class="inline-flex items-center rounded-full border border-indigo-200/80 bg-white/80 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-indigo-800">
            {{ __('talenma.company_offer.badge') }}
        </p>
        <h1 class="mt-4 max-w-3xl text-3xl font-extrabold tracking-tight text-gray-950 sm:text-4xl lg:text-[2.6rem] lg:leading-[1.12]">
            {{ __('talenma.company_offer.title') }}
        </h1>

        @php
            $offerIncludes = [
                __('talenma.company_offer.includes_1'),
                __('talenma.company_offer.includes_2'),
                __('talenma.company_offer.includes_3'),
                __('talenma.company_offer.includes_4'),
            ];
        @endphp
        <ul class="mt-6 grid max-w-3xl gap-3 sm:grid-cols-2" role="list">
            @foreach ($offerIncludes as $index => $include)
                <li class="group flex items-start gap-3 rounded-2xl border border-indigo-100/80 bg-white/70 px-4 py-3.5 shadow-[0_1px_0_rgba(99,102,241,0.06)] backdrop-blur-sm transition duration-300 hover:border-indigo-200 hover:bg-white">
                    <span
                        class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-teal-500 text-[11px] font-bold text-white shadow-sm shadow-indigo-500/20"
                        aria-hidden="true"
                    >{{ $index + 1 }}</span>
                    <span class="text-sm font-medium leading-snug text-gray-800 sm:text-[0.9375rem]">{{ $include }}</span>
                </li>
            @endforeach
        </ul>

        {{-- Onglets épurés --}}
        <div class="mt-10 max-w-3xl">
            <div
                class="flex gap-8 border-b border-gray-200"
                role="tablist"
                aria-label="{{ __('talenma.company_offer.badge') }}"
            >
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'demo'"
                    @click="setTab('demo')"
                    class="-mb-px border-b-2 pb-3 text-sm font-medium transition"
                    :class="tab === 'demo'
                        ? 'border-indigo-600 text-indigo-700'
                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-800'"
                >
                    {{ __('talenma.company_offer.cta_demo') }}
                </button>
                <button
                    type="button"
                    role="tab"
                    :aria-selected="tab === 'trial'"
                    @click="setTab('trial')"
                    class="-mb-px border-b-2 pb-3 text-sm font-medium transition"
                    :class="tab === 'trial'
                        ? 'border-indigo-600 text-indigo-700'
                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-800'"
                >
                    {{ __('talenma.company_offer.cta_trial') }}
                </button>
            </div>

            <div class="pt-8">
            <div x-show="tab === 'demo'" x-cloak id="demo" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">{{ __('talenma.company_offer.demo_title') }}</h2>
                <p class="mt-2 text-sm text-gray-600 sm:text-base">{{ __('talenma.company_offer.demo_subtitle') }}</p>

                <form
                    id="company-demo-request-form"
                    method="POST"
                    action="{{ route('company.demo.store') }}"
                    class="relative mt-8 space-y-4 rounded-2xl border border-gray-200 bg-white p-5 sm:p-8"
                    novalidate
                    @submit="onSubmit($event)"
                    :aria-busy="submitting"
                    x-data="companyOfferAjaxForm({
                        mode: 'demo',
                        loadingTargetId: 'company-demo-request-form',
                        messages: {
                            company_required: @js(__('talenma.company_offer.demo_company_required')),
                            contact_required: @js(__('talenma.company_offer.demo_contact_required')),
                            email_required: @js(__('talenma.company_offer.demo_email_required')),
                            email_invalid: @js(__('talenma.company_offer.demo_email_invalid')),
                            message_required: @js(__('talenma.company_offer.demo_message_required')),
                            message_min: @js(__('talenma.company_offer.demo_message_min')),
                            incomplete: @js(__('talenma.auth.register_incomplete_toast')),
                            network_error: @js(__('talenma.common.network_error')),
                            sent: @js(__('talenma.company_offer.demo_sent')),
                        },
                    })"
                >
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="company_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_company') }}</label>
                            <input
                                id="company_name"
                                name="company_name"
                                type="text"
                                required
                                value="{{ old('company_name') }}"
                                @input="clearFieldError('company_name')"
                                :class="fieldInvalidClass('company_name')"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p x-show="fieldMessage('company_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('company_name')"></p>
                        </div>
                        <div>
                            <label for="contact_name" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_contact') }}</label>
                            <input
                                id="contact_name"
                                name="contact_name"
                                type="text"
                                required
                                value="{{ old('contact_name') }}"
                                @input="clearFieldError('contact_name')"
                                :class="fieldInvalidClass('contact_name')"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p x-show="fieldMessage('contact_name')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('contact_name')"></p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="demo_email" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_email') }}</label>
                            <input
                                id="demo_email"
                                name="email"
                                type="email"
                                required
                                value="{{ old('email') }}"
                                @input="clearFieldError('email')"
                                :class="fieldInvalidClass('email')"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p x-show="fieldMessage('email')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('email')"></p>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_phone') }}</label>
                            <input
                                id="phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone') }}"
                                class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700">{{ __('talenma.company_offer.demo_message') }}</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="8"
                            required
                            @input="clearFieldError('message')"
                            :class="fieldInvalidClass('message')"
                            class="mt-1.5 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('message') }}</textarea>
                        <p x-show="fieldMessage('message')" x-cloak class="mt-1 text-xs text-rose-600" x-text="fieldMessage('message')"></p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-gray-500">{{ __('talenma.company_offer.demo_privacy') }}</p>
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="inline-flex justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-70"
                        >
                            <span x-show="!submitting">{{ __('talenma.company_offer.demo_submit') }}</span>
                            <span x-show="submitting" x-cloak>{{ __('talenma.auth.register_submitting') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'trial'" x-cloak id="trial" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">{{ __('talenma.company_offer.trial_title') }}</h2>
                <p class="mt-2 text-sm text-gray-600 sm:text-base">{{ __('talenma.company_offer.trial_subtitle') }}</p>

                <div class="mt-8 rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm sm:p-6">
                    @include('company.partials.trial-request-form', [
                        'professionSectors' => $professionSectors,
                        'companyCountryOptions' => $companyCountryOptions,
                    ])
                </div>
            </div>
            </div>
        </div>
    </div>
</section>
@endsection
