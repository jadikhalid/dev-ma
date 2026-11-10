@extends('layouts.public')

@php
    $isCompanyContact = $isCompanyContact ?? \App\Support\PortalHost::isCompanyHost();

    $copy = $isCompanyContact
        ? [
            'meta_title' => __('talenma.company_contact.meta_title'),
            'meta_description' => __('talenma.company_contact.meta_description'),
            'home_label' => __('talenma.company_contact.home'),
            'home_url' => route('company.offer'),
            'title_before' => __('talenma.company_contact.title_before'),
            'title_accent' => __('talenma.company_contact.title_accent'),
            'subtitle' => __('talenma.company_contact.subtitle'),
            'form_title' => __('talenma.company_contact.form_title'),
            'default_role' => 'company',
            'show_role_select' => false,
            'faq_title' => __('talenma.company_contact.faq_title'),
            'faq_subtitle' => __('talenma.company_contact.faq_subtitle'),
            'faq_url' => route('faq'),
            'secondary_label' => __('talenma.company_contact.secondary_label'),
            'secondary_subtitle' => __('talenma.company_contact.secondary_subtitle'),
            'secondary_url' => route('company.demo'),
        ]
        : [
            'meta_title' => __('talenma.contact_page.meta_title'),
            'meta_description' => __('talenma.contact_page.meta_description'),
            'home_label' => __('talenma.contact_page.home'),
            'home_url' => route('home'),
            'title_before' => __('talenma.contact_page.title_before'),
            'title_accent' => __('talenma.contact_page.title_accent'),
            'subtitle' => __('talenma.contact_page.subtitle'),
            'form_title' => __('talenma.contact_page.form_title'),
            'default_role' => 'talent',
            'show_role_select' => true,
            'faq_title' => __('talenma.contact_page.faq_title'),
            'faq_subtitle' => __('talenma.contact_page.faq_subtitle'),
            'faq_url' => route('faq'),
            'secondary_label' => null,
            'secondary_subtitle' => null,
            'secondary_url' => null,
        ];

    $contactEmail = (string) config('talenma.contact.email');
    $contactPhone = trim((string) config('talenma.contact.phone'));
    $contactAddress = trim((string) config('talenma.contact.address'));
@endphp

@section('title', $copy['meta_title'])
@section('meta_description', $copy['meta_description'])

@section('content')
<div @class(['relative' => ! $isCompanyContact])>
    <section
        @class([
            'relative overflow-hidden text-center',
            '-mt-20 pt-20 sm:-mt-[4.25rem] sm:pt-[4.25rem]' => ! $isCompanyContact,
        ])
        @if ($isCompanyContact) data-contact-hero @endif
    >
        @unless ($isCompanyContact)
            <div class="how-hero-frame" aria-hidden="true">
                <div class="how-wash how-wash-lilac -left-24 -top-40 h-[28rem] w-[28rem] opacity-50 sm:-left-32 sm:-top-72 sm:h-[760px] sm:w-[760px]"></div>
                <div class="how-wash how-wash-sky -right-24 -top-24 h-[22rem] w-[22rem] opacity-30 sm:-top-40 sm:right-0 sm:h-[700px] sm:w-[700px]"></div>
            </div>
        @endunless

        <div class="relative px-4 pb-8 pt-12 sm:px-6 sm:pt-16 lg:px-8">
            <h1 class="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl lg:leading-[1.04]">
                {{ $copy['title_before'] }}
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 bg-clip-text text-transparent">{{ $copy['title_accent'] }}</span>
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg font-light text-gray-600 sm:text-xl">
                {{ $copy['subtitle'] }}
            </p>
        </div>
    </section>

    <section class="relative mx-auto grid max-w-7xl gap-8 px-4 pb-20 pt-4 sm:px-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)] lg:gap-10 lg:px-8 lg:pb-28">
        <div
            id="contact-form-card"
            class="rounded-[32px] border border-[#EEF0F4] bg-[#FAFAFE] p-6 shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)] sm:p-10"
        >
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-[28px]">{{ $copy['form_title'] }}</h2>

            <form
                method="POST"
                action="{{ route('contact.store') }}"
                class="mt-6 flex flex-col gap-4"
                data-ajax
                data-ajax-reset
                data-loading-target="contact-form-card"
                data-error-message="{{ __('talenma.contact_page.form.error') }}"
                data-network-error-message="{{ __('talenma.contact_page.form.error') }}"
                novalidate
            >
                @csrf

                <div class="absolute -left-[9999px] top-0 h-0 w-0 overflow-hidden" aria-hidden="true">
                    <label for="contact_website">Website</label>
                    <input id="contact_website" type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                        <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.name') }}</span>
                        <input
                            type="text"
                            name="name"
                            required
                            maxlength="120"
                            autocomplete="name"
                            placeholder="{{ __('talenma.contact_page.form.name_placeholder') }}"
                            data-required
                            data-required-message="{{ __('talenma.contact_page.form.name_required') }}"
                            class="border-0 bg-transparent p-0 text-base text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0"
                        >
                    </label>
                    <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                        <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.email') }}</span>
                        <input
                            type="email"
                            name="email"
                            required
                            maxlength="255"
                            autocomplete="email"
                            placeholder="{{ __('talenma.contact_page.form.email_placeholder') }}"
                            data-required
                            data-required-message="{{ __('talenma.contact_page.form.email_required') }}"
                            data-email
                            data-email-message="{{ __('talenma.contact_page.form.email_invalid') }}"
                            class="border-0 bg-transparent p-0 text-base text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0"
                        >
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @if ($copy['show_role_select'])
                        <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                            <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.role') }}</span>
                            <select
                                name="role"
                                required
                                data-required
                                data-required-message="{{ __('talenma.contact_page.form.role_required') }}"
                                class="border-0 bg-transparent p-0 text-base text-gray-900 outline-none focus:ring-0"
                            >
                                <option value="talent" @selected($copy['default_role'] === 'talent')>{{ __('talenma.contact_page.form.role_talent') }}</option>
                                <option value="company" @selected($copy['default_role'] === 'company')>{{ __('talenma.contact_page.form.role_company') }}</option>
                                <option value="other" @selected($copy['default_role'] === 'other')>{{ __('talenma.contact_page.form.role_other') }}</option>
                            </select>
                        </label>
                    @else
                        <input type="hidden" name="role" value="company">
                        <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                            <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.role') }}</span>
                            <span class="text-base text-gray-900">{{ __('talenma.contact_page.form.role_company') }}</span>
                        </label>
                    @endif

                    <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                        <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.subject') }}</span>
                        <input
                            type="text"
                            name="subject"
                            required
                            maxlength="180"
                            placeholder="{{ __('talenma.contact_page.form.subject_placeholder') }}"
                            data-required
                            data-required-message="{{ __('talenma.contact_page.form.subject_required') }}"
                            class="border-0 bg-transparent p-0 text-base text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0"
                        >
                    </label>
                </div>

                <label class="flex flex-col gap-1.5 rounded-2xl border border-gray-200 bg-white px-4 py-3.5">
                    <span class="text-[13px] font-bold text-gray-900">{{ __('talenma.contact_page.form.message') }}</span>
                    <textarea
                        name="message"
                        required
                        maxlength="5000"
                        rows="6"
                        placeholder="{{ __('talenma.contact_page.form.message_placeholder') }}"
                        data-required
                        data-required-message="{{ __('talenma.contact_page.form.message_required') }}"
                        data-minlength="20"
                        data-minlength-message="{{ __('talenma.contact_page.form.message_min') }}"
                        class="h-36 resize-none border-0 bg-transparent p-0 text-base text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0"
                    ></textarea>
                </label>

                <label class="flex items-start gap-3 text-sm text-gray-600 sm:items-center">
                    <input
                        type="checkbox"
                        name="consent"
                        value="1"
                        required
                        data-required
                        data-required-message="{{ __('talenma.contact_page.form.consent_required') }}"
                        class="mt-0.5 h-5 w-5 shrink-0 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 sm:mt-0"
                        style="accent-color: #5546F0;"
                    >
                    <span>{{ __('talenma.contact_page.form.consent') }}</span>
                </label>

                <button
                    type="submit"
                    class="mt-1 inline-flex h-14 w-fit items-center gap-2.5 rounded-[14px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 px-7 text-base font-semibold text-white shadow-[0_14px_28px_-14px_rgba(79,70,229,0.8)] transition hover:brightness-105"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"></path></svg>
                    {{ __('talenma.contact_page.form.submit') }}
                </button>
            </form>
        </div>

        <aside class="flex flex-col gap-3.5">
            <div class="flex items-center gap-4 rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-[18px] shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 text-white">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
                </span>
                <span class="flex min-w-0 flex-col gap-0.5">
                    <span class="text-[13px] text-gray-500">{{ __('talenma.contact_page.info.email') }}</span>
                    <a href="mailto:{{ $contactEmail }}" class="truncate text-base font-semibold text-gray-900 hover:text-indigo-600">{{ $contactEmail }}</a>
                </span>
            </div>

            @if ($contactPhone !== '')
                <div class="flex items-center gap-4 rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-[18px] shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 text-white">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"></path></svg>
                    </span>
                    <span class="flex min-w-0 flex-col gap-0.5">
                        <span class="text-[13px] text-gray-500">{{ __('talenma.contact_page.info.phone') }}</span>
                        <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="text-base font-semibold text-gray-900 hover:text-indigo-600">{{ $contactPhone }}</a>
                    </span>
                </div>
            @endif

            @if ($contactAddress !== '')
                <div class="flex items-center gap-4 rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-[18px] shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-500 text-white">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><path d="M12 21s-7-6.5-7-12a7 7 0 0 1 14 0c0 5.5-7 12-7 12z"></path><circle cx="12" cy="9" r="2.5"></circle></svg>
                    </span>
                    <span class="flex min-w-0 flex-col gap-0.5">
                        <span class="text-[13px] text-gray-500">{{ __('talenma.contact_page.info.address') }}</span>
                        <span class="text-base font-semibold text-gray-900">{{ $contactAddress }}</span>
                    </span>
                </div>
            @endif

            <div class="relative h-[210px] overflow-hidden rounded-3xl shadow-[0_1px_2px_rgba(17,24,39,0.06),0_12px_32px_-12px_rgba(49,46,129,0.22)]" aria-hidden="true">
                <svg class="h-full w-full" viewBox="0 0 400 260" preserveAspectRatio="xMidYMid slice">
                    <defs>
                        <linearGradient id="lbct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#C7D2FE"/><stop offset="0.6" stop-color="#E0E7FF"/><stop offset="1" stop-color="#F5F3FF"/></linearGradient>
                        <linearGradient id="l1ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5546F0"/><stop offset="1" stop-color="#1E1B6B"/></linearGradient>
                        <linearGradient id="l2ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#8B5CF6"/><stop offset="1" stop-color="#3730A3"/></linearGradient>
                        <linearGradient id="l3ct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3B82F6"/><stop offset="1" stop-color="#1E3A8A"/></linearGradient>
                        <linearGradient id="lwct" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5546F0"/><stop offset="1" stop-color="#2563EB"/></linearGradient>
                        <filter id="lgct" x="-100%" y="-100%" width="300%" height="300%"><feGaussianBlur stdDeviation="18"/></filter>
                    </defs>
                    <rect width="400" height="260" fill="url(#lbct)"/>
                    <circle cx="86" cy="70" r="40" fill="#EBBA6E" opacity="0.5" filter="url(#lgct)"/>
                    <circle cx="86" cy="70" r="20" fill="#EBBA6E"/>
                    <g fill="#A5B4FC" opacity="0.7"><rect x="0" y="160" width="40" height="60"/><rect x="44" y="146" width="30" height="74"/><rect x="350" y="150" width="50" height="70"/></g>
                    <path d="M96 220 V110 L132 92 V220z" fill="url(#l3ct)"/>
                    <rect x="146" y="40" width="46" height="180" rx="4" fill="url(#l1ct)"/>
                    <path d="M206 220 V104 Q206 66 240 58 Q274 66 274 104 V220z" fill="url(#l2ct)"/>
                    <path d="M286 220 V128 L326 112 V220z" fill="url(#l3ct)"/>
                    <rect x="0" y="218" width="400" height="42" fill="url(#lwct)"/>
                    <g stroke="#FFFFFF" stroke-opacity="0.45" stroke-width="2" stroke-linecap="round"><path d="M30 232h40M110 240h60M220 230h50M300 244h70M60 252h30"/></g>
                </svg>
            </div>

            <div class="flex items-center justify-between gap-4 rounded-[18px] border border-[#E8E9F0] bg-white px-5 py-[18px]">
                <span class="text-[15px] font-semibold text-gray-900">{{ __('talenma.contact_page.follow') }}</span>
                <x-social-links size="md" class="!gap-2" />
            </div>

            <a href="{{ $copy['faq_url'] }}" class="flex items-center justify-between gap-4 rounded-[20px] bg-[#0D0B2B] px-6 py-[22px] text-white transition hover:bg-[#16133a]">
                <span class="flex flex-col gap-1">
                    <span class="text-[17px] font-bold">{{ $copy['faq_title'] }}</span>
                    <span class="text-sm text-indigo-200">{{ $copy['faq_subtitle'] }}</span>
                </span>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white text-[#0D0B2B]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </span>
            </a>

            @if ($copy['secondary_label'])
                <a href="{{ $copy['secondary_url'] }}" class="flex items-center justify-between gap-4 rounded-[20px] border border-indigo-100 bg-indigo-50/70 px-6 py-5 text-indigo-950 transition hover:bg-indigo-50">
                    <span class="flex flex-col gap-1">
                        <span class="text-[17px] font-bold">{{ $copy['secondary_label'] }}</span>
                        <span class="text-sm text-indigo-700/80">{{ $copy['secondary_subtitle'] }}</span>
                    </span>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>
            @endif
        </aside>
    </section>
</div>
@endsection
