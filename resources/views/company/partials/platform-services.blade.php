@php
    $services = [
        ['key' => 'catalogue', 'anchor' => 'platform-catalogue', 'tag' => 'bg-amber-300', 'panel' => 'bg-amber-100', 'section' => 'bg-white'],
        ['key' => 'applications', 'anchor' => 'platform-applications', 'tag' => 'bg-emerald-300', 'panel' => 'bg-emerald-100', 'section' => 'bg-sky-50/70'],
        ['key' => 'sourcing', 'anchor' => 'platform-sourcing', 'tag' => 'bg-rose-300', 'panel' => 'bg-rose-100', 'section' => 'bg-white'],
        ['key' => 'jobs', 'anchor' => 'platform-jobs', 'tag' => 'bg-sky-300', 'panel' => 'bg-sky-100', 'section' => 'bg-sky-50/70'],
    ];

    $sep = app()->getLocale() === 'fr' ? ' ' : ',';
    $img = fn (string $name) => asset('images/hero/'.$name.'.jpg');
    $avatar = 'shrink-0 rounded-full object-cover object-top ring-2 ring-white';
    $windowBar = '<div class="flex items-center gap-1.5 border-b border-gray-100 px-4 py-2.5" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-rose-300"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span><span class="h-2.5 w-2.5 rounded-full bg-emerald-300"></span></div>';
@endphp

<section class="bg-white pt-16 sm:pt-24" data-company-platform-intro>
    <div class="home-shell">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">{{ __('talenma.company_offer.platform_eyebrow') }}</p>
            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-950 sm:text-4xl">{{ __('talenma.company_offer.platform_title') }}</h2>
            <p class="mt-4 text-base leading-relaxed text-gray-600 sm:text-lg">{{ __('talenma.company_offer.platform_text') }}</p>
        </div>
    </div>
</section>

@foreach ($services as $index => $service)
    @php($reverse = $index % 2 === 1)
    <section
        id="{{ $service['anchor'] }}"
        class="{{ $service['section'] }} scroll-mt-20 py-14 sm:scroll-mt-28 sm:py-20"
        data-company-platform-service="{{ $service['key'] }}"
    >
        <div class="home-shell">
            <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16 lg:px-10 xl:px-16 2xl:px-24">
                {{-- Texte --}}
                <div @class(['lg:order-2' => $reverse])>
                    <p class="inline-flex items-center gap-2 rounded-full {{ $service['tag'] }} px-3.5 py-1.5 text-xs font-bold text-gray-950">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-950 text-[10px] text-white">{{ $index + 1 }}</span>
                        {{ __('talenma.nav.platform_menu_short_'.($index + 1)) }}
                    </p>
                    <h3 class="mt-4 text-2xl font-extrabold leading-tight tracking-tight text-gray-950 sm:text-[2rem]">{{ __('talenma.company_offer.services.'.$service['key'].'.title') }}</h3>
                    <p class="mt-4 text-[0.9375rem] leading-relaxed text-gray-600 sm:text-base">{{ __('talenma.company_offer.services.'.$service['key'].'.text') }}</p>
                    <ul class="mt-6 space-y-3" role="list">
                        @foreach (__('talenma.company_offer.services.'.$service['key'].'.points') as $point)
                            <li class="flex items-start gap-3 text-[0.9375rem] font-medium text-gray-800">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($showOfferForms)
                        <button
                            type="button"
                            x-data
                            @click="$dispatch('company-offer-drawer', 'demo')"
                            class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-md border-2 border-gray-950 px-6 py-2.5 text-sm font-semibold text-gray-950 transition hover:bg-gray-950 hover:text-white sm:w-auto"
                        >
                            {{ __('talenma.company_offer.hero_cta_demo') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </button>
                    @endif
                </div>

                {{-- Visuel --}}
                <div @class(['relative rounded-3xl p-5 sm:p-8 lg:p-10', $service['panel'], 'lg:order-1' => $reverse]) aria-hidden="true">
                    @switch($service['key'])
                        @case('catalogue')
                            <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
                                {!! $windowBar !!}
                                <div class="p-4 sm:p-5">
                                    <div class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-400 sm:text-sm">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                        {{ __('talenma.company_offer.mock.search') }}
                                    </div>
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach (['filter_sector', 'filter_experience', 'filter_city'] as $filter)
                                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">{{ __('talenma.company_offer.mock.'.$filter) }}</span>
                                        @endforeach
                                    </div>
                                    <p class="mt-4 text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.company_offer.mock.results') }}</p>
                                    <ul class="mt-2 space-y-2">
                                        @foreach ([['salma', 'Salma B.', 'role_1'], ['infirmiere', 'Nadia E.', 'role_2'], ['omar', 'Omar K.', 'role_3']] as [$photo, $name, $role])
                                            <li class="flex items-center gap-3 rounded-xl border border-gray-100 px-3 py-2.5">
                                                <img src="{{ $img($photo) }}" alt="" class="{{ $avatar }} h-10 w-10" loading="lazy">
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-bold text-gray-950">{{ $name }}</p>
                                                    <p class="truncate text-xs text-gray-500">{{ __('talenma.company_offer.mock.'.$role) }}</p>
                                                </div>
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                                                    {{ __('talenma.company_offer.mock.verified') }}
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @break

                        @case('applications')
                            <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
                                {!! $windowBar !!}
                                <div class="p-4 sm:p-5">
                                    <p class="text-sm font-bold text-gray-950">{{ __('talenma.company_offer.mock.pipeline_title') }}</p>
                                    <div class="mt-3 grid grid-cols-3 gap-2 sm:gap-3">
                                        @foreach ([
                                            ['pipeline_received', 'bg-gray-100', 12, [['yasmine', 'role_4'], ['tarik', 'role_5']]],
                                            ['pipeline_interview', 'bg-amber-100', 4, [['omar', 'role_3'], ['karim', 'role_5']]],
                                            ['pipeline_offer', 'bg-emerald-100', 1, [['infirmiere', 'role_2']]],
                                        ] as [$column, $tone, $count, $cards])
                                            <div class="rounded-xl {{ $tone }} p-2">
                                                <p class="flex items-center justify-between px-1 text-[10px] font-bold uppercase tracking-wide text-gray-700 sm:text-[11px]">
                                                    <span class="truncate">{{ __('talenma.company_offer.mock.'.$column) }}</span>
                                                    <span class="ml-1 rounded-full bg-white px-1.5 text-gray-900">{{ $count }}</span>
                                                </p>
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($cards as [$photo, $role])
                                                        <div class="rounded-lg bg-white p-2 shadow-sm">
                                                            <img src="{{ $img($photo) }}" alt="" class="{{ $avatar }} h-7 w-7" loading="lazy">
                                                            <p class="mt-1.5 truncate text-[10px] font-semibold text-gray-700 sm:text-[11px]">{{ __('talenma.company_offer.mock.'.$role) }}</p>
                                                            <div class="mt-1 h-1 w-2/3 rounded bg-gray-200"></div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="absolute -bottom-4 right-3 flex items-center gap-2.5 rounded-xl bg-white px-3 py-2 shadow-xl ring-1 ring-black/5 sm:-bottom-5 sm:right-6">
                                <img src="{{ $img('infirmiere') }}" alt="" class="{{ $avatar }} h-8 w-8">
                                <span class="text-xs font-bold text-gray-950">{{ __('talenma.company_offer.mock.pipeline_hired') }}</span>
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                            @break

                        @case('sourcing')
                            <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
                                {!! $windowBar !!}
                                <div class="p-4 sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.company_offer.mock.sourcing_request') }}</p>
                                            <p class="mt-1 text-sm font-bold text-gray-950 sm:text-base">{{ __('talenma.company_offer.mock.sourcing_role') }}</p>
                                        </div>
                                        <span class="rounded-full bg-rose-100 px-2.5 py-1 text-[11px] font-bold text-rose-700">{{ __('talenma.company_offer.mock.sourcing_status') }}</span>
                                    </div>
                                    <p class="mt-4 text-xs font-semibold text-gray-500">{{ __('talenma.company_offer.mock.sourcing_shortlist') }}</p>
                                    <ul class="mt-2 space-y-2">
                                        @foreach ([['tarik', 'Tarik M.', 96], ['karim', 'Karim A.', 91], ['yasmine', 'Yasmine R.', 88]] as [$photo, $name, $match])
                                            <li class="flex items-center gap-3 rounded-xl border border-gray-100 px-3 py-2.5">
                                                <img src="{{ $img($photo) }}" alt="" class="{{ $avatar }} h-9 w-9" loading="lazy">
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate text-sm font-bold text-gray-950">{{ $name }}</p>
                                                    <div class="mt-1.5 h-1.5 w-full rounded-full bg-gray-100">
                                                        <div class="h-1.5 rounded-full bg-rose-400" style="width: {{ $match }}%"></div>
                                                    </div>
                                                </div>
                                                <span class="text-xs font-extrabold text-gray-950">{{ $match }}%</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @break

                        @case('jobs')
                            <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
                                {!! $windowBar !!}
                                <div class="p-4 sm:p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-gray-950 sm:text-base">{{ __('talenma.company_offer.mock.job_title') }}</p>
                                            <p class="mt-0.5 text-xs text-gray-500">{{ __('talenma.company_offer.mock.job_meta') }}</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('talenma.company_offer.mock.job_status') }}
                                        </span>
                                    </div>
                                    <div class="mt-4 grid grid-cols-2 gap-3">
                                        <div class="rounded-xl bg-sky-50 p-3">
                                            <p class="text-[11px] font-semibold text-sky-800">{{ __('talenma.company_offer.mock.job_views') }}</p>
                                            <p class="mt-1 text-xl font-extrabold text-gray-950 sm:text-2xl">{{ number_format(1248, 0, ',', $sep) }}</p>
                                        </div>
                                        <div class="rounded-xl bg-indigo-50 p-3">
                                            <p class="text-[11px] font-semibold text-indigo-800">{{ __('talenma.company_offer.mock.job_applications') }}</p>
                                            <p class="mt-1 text-xl font-extrabold text-gray-950 sm:text-2xl">64</p>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ __('talenma.company_offer.mock.job_week') }}</p>
                                    <div class="mt-2 flex h-24 items-end gap-2">
                                        @foreach ([35, 55, 40, 70, 60, 90, 75] as $height)
                                            <div class="flex-1 rounded-t-md {{ $loop->index === 5 ? 'bg-indigo-600' : 'bg-sky-200' }}" style="height: {{ $height }}%"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </section>
@endforeach

@if ($showOfferForms)
<section class="bg-white py-14 sm:py-20" data-company-platform-final>
    <div class="home-shell">
        <div class="relative overflow-hidden rounded-3xl bg-indigo-600 px-6 py-12 text-center text-white sm:px-12 sm:py-16 lg:mx-10 xl:mx-16 2xl:mx-24">
            <div class="pointer-events-none absolute -left-16 -top-16 h-56 w-56 rounded-full bg-indigo-500/60" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-10 h-64 w-64 rounded-full bg-indigo-700/60" aria-hidden="true"></div>
            <div class="relative mx-auto max-w-2xl">
                <h2 class="text-2xl font-extrabold tracking-tight sm:text-4xl">{{ __('talenma.company_offer.final_title') }}</h2>
                <p class="mt-4 text-base leading-relaxed text-indigo-100">{{ __('talenma.company_offer.final_text') }}</p>
                <div class="mt-8 grid grid-cols-1 gap-2.5 sm:inline-grid sm:grid-cols-2 sm:gap-3" x-data>
                    <button
                        type="button"
                        @click="$dispatch('company-offer-drawer', 'demo')"
                        class="inline-flex w-full items-center justify-center rounded-md border-2 border-gray-950 bg-gray-950 px-6 py-2.5 text-sm font-semibold text-white transition hover:border-gray-800 hover:bg-gray-800"
                    >{{ __('talenma.company_offer.hero_cta_demo') }}</button>
                    <button
                        type="button"
                        @click="$dispatch('company-offer-drawer', 'trial')"
                        class="inline-flex w-full items-center justify-center rounded-md border-2 border-white px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-white hover:text-indigo-700"
                    >{{ __('talenma.company_offer.hero_cta_trial') }}</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
