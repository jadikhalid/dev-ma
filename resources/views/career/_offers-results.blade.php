@if ($jobs->isEmpty())
    <p class="rounded-2xl bg-gray-50 px-6 py-10 text-center text-gray-600">
        {{ $search !== '' ? __('talenma.career_pages.offers_search_empty') : __('talenma.career_pages.offers_empty') }}
    </p>
@else
    <ul class="grid gap-4 md:grid-cols-2">
        @foreach ($jobs as $job)
            <li class="min-w-0">
                <a href="{{ $job['url'] }}" class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                    <div class="flex items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white ring-1 ring-slate-200">
                            @if ($job['logo_url'])
                                <img src="{{ $job['logo_url'] }}" alt="" class="h-full w-full object-contain p-1" loading="lazy" decoding="async">
                            @else
                                <span class="flex h-full w-full items-center justify-center bg-indigo-50 text-sm font-bold text-indigo-700" aria-hidden="true">{{ $job['company_initials'] }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 pt-0.5">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $job['company'] }}</p>
                            @if ($job['sector'])
                                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $job['sector'] }}</p>
                            @endif
                        </div>
                    </div>

                    <h2 class="mt-4 line-clamp-2 text-lg font-bold leading-snug text-slate-900 group-hover:text-indigo-700">{{ $job['title'] }}</h2>

                    @if ($job['excerpt'] !== '')
                        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500">{{ $job['excerpt'] }}</p>
                    @endif

                    @if ($job['location'] || $job['work_modes'] !== [] || $job['experience'])
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @if ($job['location'])
                                <span class="inline-flex max-w-full items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                    </svg>
                                    <span class="truncate">{{ $job['location'] }}</span>
                                </span>
                            @endif
                            @foreach ($job['work_modes'] as $mode)
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">{{ $mode }}</span>
                            @endforeach
                            @if ($job['experience'])
                                <span class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-800">{{ $job['experience'] }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-auto pt-4">
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            @if ($job['date'] !== '')
                                <time class="inline-flex min-w-0 items-center gap-1.5 text-xs text-slate-500">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                                    </svg>
                                    <span class="truncate">{{ __('talenma.jobs.card_published_on', ['date' => $job['date']]) }}</span>
                                </time>
                            @else
                                <span></span>
                            @endif
                            <span class="shrink-0 text-xs font-semibold text-indigo-700">{{ __('talenma.career_pages.offers_view') }}</span>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="mt-8">{{ $jobs->links('career._offers-pagination') }}</div>
@endif
