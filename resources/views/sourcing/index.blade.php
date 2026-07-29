<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.index_title') }}</h2>
                <p class="text-sm text-gray-500">{{ __('talenma.recruitment.index_subtitle') }}</p>
            </div>
            <a
                href="{{ route('recruitment.create') }}"
                class="inline-flex shrink-0 items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700"
            >
                {{ __('talenma.dashboard.company.intermediary') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 lg:gap-6 items-start">
            <section class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 p-4 sm:p-5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-600/20" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.column_open') }}</p>
                        <p class="text-xs text-slate-500">{{ __('talenma.recruitment.column_open_hint') }}</p>
                    </div>
                </div>

                @if ($openRequests->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                        <p class="text-sm text-slate-500">{{ __('talenma.recruitment.column_empty') }}</p>
                    </div>
                @else
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($openRequests as $req)
                            <li>@include('sourcing._request-card', ['recruitment' => $req])</li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="rounded-2xl border border-violet-100 bg-gradient-to-br from-violet-50/80 via-white to-slate-50 p-4 sm:p-5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-white shadow-sm shadow-violet-600/20" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.column_named') }}</p>
                        <p class="text-xs text-slate-500">{{ __('talenma.recruitment.column_named_hint') }}</p>
                    </div>
                </div>

                @if ($namedRequests->isEmpty())
                    <div class="mt-4 rounded-xl border border-dashed border-slate-200 bg-white/70 px-4 py-6 text-center">
                        <p class="text-sm text-slate-500">{{ __('talenma.recruitment.column_empty') }}</p>
                    </div>
                @else
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($namedRequests as $req)
                            <li>@include('sourcing._request-card', ['recruitment' => $req])</li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
