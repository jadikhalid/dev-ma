<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-600/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/>
                </svg>
            </span>
            <div>
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('talenma.ats_score.page_title') }}
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ __('talenma.ats_score.page_subtitle') }}</p>
            </div>
        </div>
    </x-slot>

    @php
        $score = (int) ($result['score'] ?? 0);
        $hasResult = is_array($result);
        $hasOptimized = is_array($optimizedResult);
        $initialTab = $errors->has('cv')
            ? 'upload'
            : ($hasOptimized ? 'optimize' : ($hasResult ? 'diagnosis' : 'upload'));
        $scoreTone = $score >= 80 ? 'good' : ($score >= 55 ? 'mid' : 'low');
        $scoreColor = match ($scoreTone) {
            'good' => 'text-emerald-600',
            'mid' => 'text-amber-600',
            default => 'text-rose-600',
        };
        $ringColor = match ($scoreTone) {
            'good' => 'stroke-emerald-500',
            'mid' => 'stroke-amber-500',
            default => 'stroke-rose-500',
        };
        $scoreGlow = match ($scoreTone) {
            'good' => 'from-emerald-400/30 via-teal-300/20 to-transparent',
            'mid' => 'from-amber-400/30 via-orange-300/15 to-transparent',
            default => 'from-rose-400/30 via-pink-300/15 to-transparent',
        };
        $circumference = 2 * M_PI * 54;
        $dash = $circumference * ($score / 100);
        $optScore = (int) ($optimizedResult['score'] ?? 0);
    @endphp

    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-100/70 via-slate-50 to-slate-100"></div>
        <div class="pointer-events-none absolute -top-24 right-0 h-72 w-72 rounded-full bg-teal-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute top-40 -left-16 h-64 w-64 rounded-full bg-emerald-400/15 blur-3xl"></div>

        <div
            class="relative py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8"
            x-data="{ tab: @js($initialTab) }"
        >
            <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 shadow-xl shadow-emerald-900/5 ring-1 ring-emerald-900/5 backdrop-blur-sm">
                {{-- Step tabs --}}
                <div
                    class="grid grid-cols-3 gap-1 border-b border-emerald-100/80 bg-gradient-to-r from-emerald-50/90 via-white to-teal-50/70 p-1.5 sm:p-2"
                    role="tablist"
                    aria-label="{{ __('talenma.ats_score.tabs_label') }}"
                >
                    @foreach ([
                        'upload' => ['n' => '1', 'label' => __('talenma.ats_score.tab_upload')],
                        'diagnosis' => ['n' => '2', 'label' => __('talenma.ats_score.tab_diagnosis')],
                        'optimize' => ['n' => '3', 'label' => __('talenma.ats_score.tab_optimize')],
                    ] as $key => $meta)
                        <button
                            type="button"
                            role="tab"
                            id="ats-tab-{{ $key }}"
                            :aria-selected="tab === '{{ $key }}'"
                            @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}'
                                ? 'bg-white text-emerald-800 shadow-md shadow-emerald-900/10 ring-1 ring-emerald-200/80'
                                : 'text-slate-500 hover:bg-white/70 hover:text-slate-700'"
                            class="relative rounded-2xl px-2 py-3 sm:px-3 sm:py-3.5 text-sm font-semibold transition duration-200"
                        >
                            <span class="flex flex-col sm:flex-row items-center justify-center gap-1.5 sm:gap-2">
                                <span
                                    :class="tab === '{{ $key }}' ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-sm' : 'bg-slate-200/80 text-slate-600'"
                                    class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-bold transition"
                                >{{ $meta['n'] }}</span>
                                <span class="leading-tight">{{ $meta['label'] }}</span>
                                @if ($key === 'diagnosis' && $hasResult)
                                    <span class="hidden sm:inline text-xs font-bold tabular-nums text-emerald-700">{{ $score }}%</span>
                                @endif
                                @if ($key === 'optimize')
                                    <span class="hidden sm:inline-flex rounded-full bg-emerald-500/15 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700">
                                        {{ __('talenma.ats_score.optimize_free_badge') }}
                                    </span>
                                @endif
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Tab 1: Upload --}}
                <div
                    x-show="tab === 'upload'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="ats-tab-upload"
                    class="p-6 sm:p-8 lg:p-10"
                >
                    <div class="mx-auto max-w-2xl text-center">
                        <div class="mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-600/30">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                            </svg>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                            {{ __('talenma.ats_score.upload_title') }}
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                            {{ __('talenma.ats_score.upload_help') }}
                        </p>
                        <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                            @foreach (['PDF', 'DOCX', 'TXT'] as $fmt)
                                <span class="inline-flex rounded-full border border-emerald-200/80 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold tracking-wide text-emerald-800">{{ $fmt }}</span>
                            @endforeach
                            <span class="inline-flex rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-500">{{ __('talenma.ats_score.upload_size_chip') }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('talent.ats-score.analyze') }}" enctype="multipart/form-data" class="mx-auto mt-8 max-w-xl space-y-5">
                        @csrf
                        <label
                            for="ats-cv"
                            class="group relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-emerald-300/80 bg-gradient-to-b from-emerald-50/80 to-white px-6 py-10 transition hover:border-emerald-500 hover:shadow-md hover:shadow-emerald-900/5"
                        >
                            <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100 transition group-hover:scale-105">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                                </svg>
                            </span>
                            <span class="text-sm font-semibold text-slate-800">{{ __('talenma.ats_score.upload_drop_title') }}</span>
                            <span class="mt-1 text-xs text-slate-500">{{ __('talenma.ats_score.upload_drop_hint') }}</span>
                            <input
                                id="ats-cv"
                                name="cv"
                                type="file"
                                accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain"
                                required
                                class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                            />
                        </label>
                        @error('cv')
                            <p class="text-center text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-center">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-7 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500 hover:shadow-emerald-600/35"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.611L5 14.5"/>
                                </svg>
                                {{ __('talenma.ats_score.upload_cta') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tab 2: Diagnosis --}}
                <div
                    x-show="tab === 'diagnosis'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="ats-tab-diagnosis"
                    class="p-6 sm:p-8 lg:p-10 space-y-6"
                >
                    @if ($hasResult)
                        <div class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-white to-emerald-50/80 p-6 sm:p-8 shadow-sm">
                            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-gradient-to-br {{ $scoreGlow }} blur-2xl"></div>
                            <div class="relative flex flex-col md:flex-row md:items-center gap-8">
                                <div class="relative mx-auto md:mx-0 h-40 w-40 shrink-0">
                                    <div class="absolute inset-3 rounded-full bg-white shadow-inner ring-1 ring-slate-100"></div>
                                    <svg class="relative h-40 w-40 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                                        <circle cx="60" cy="60" r="54" fill="none" stroke-width="9" class="stroke-slate-100"/>
                                        <circle
                                            cx="60" cy="60" r="54" fill="none" stroke-width="9" stroke-linecap="round"
                                            class="{{ $ringColor }}"
                                            stroke-dasharray="{{ $dash }} {{ $circumference }}"
                                        />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-4xl font-black tabular-nums tracking-tight {{ $scoreColor }}">{{ $score }}%</span>
                                        <span class="mt-0.5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">ATS</span>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 text-center md:text-left">
                                    <p class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                                        {{ __('talenma.ats_score.score_label', ['score' => $score]) }}
                                    </p>
                                    @if ($filename)
                                        <p class="mt-2 inline-flex max-w-full items-center gap-2 rounded-full bg-slate-100/90 px-3 py-1 text-xs font-medium text-slate-600">
                                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 9h6.75M8.25 15h3.75"/>
                                            </svg>
                                            <span class="truncate" title="{{ $filename }}">{{ $filename }}</span>
                                        </p>
                                    @endif
                                    <p class="mt-3 text-sm text-slate-600 leading-relaxed max-w-xl">
                                        {{ __('talenma.ats_score.score_help') }}
                                    </p>
                                    <div class="mt-5 flex flex-wrap items-center justify-center md:justify-start gap-2.5">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('talenma.ats_score.passed_count', ['count' => $result['passed_count']]) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-900">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            {{ __('talenma.ats_score.issue_count', ['count' => $result['issue_count']]) }}
                                        </span>
                                    </div>
                                    <div class="mt-6">
                                        <button
                                            type="button"
                                            @click="tab = 'optimize'"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-500 hover:to-teal-500"
                                        >
                                            {{ __('talenma.ats_score.go_optimize_tab') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/90 px-5 py-3.5">
                                <h2 class="text-sm font-bold uppercase tracking-wide text-slate-800">
                                    {{ __('talenma.ats_score.checklist_title') }}
                                </h2>
                                <span class="text-xs font-medium text-slate-400">{{ __('talenma.ats_score.checklist_count', ['count' => count($result['findings'])]) }}</span>
                            </div>
                            <ul class="divide-y divide-slate-100 max-h-[28rem] overflow-y-auto">
                                @foreach ($result['findings'] as $finding)
                                    @php
                                        $status = $finding['status'];
                                        $rowAccent = match ($status) {
                                            'pass' => 'border-l-emerald-400',
                                            'partial' => 'border-l-amber-400',
                                            'warn' => 'border-l-sky-400',
                                            default => 'border-l-rose-400',
                                        };
                                        $badge = match ($status) {
                                            'pass' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/70',
                                            'partial' => 'bg-amber-50 text-amber-800 ring-amber-200/70',
                                            'warn' => 'bg-sky-50 text-sky-800 ring-sky-200/70',
                                            default => 'bg-rose-50 text-rose-700 ring-rose-200/70',
                                        };
                                        $dot = match ($status) {
                                            'pass' => 'bg-emerald-500',
                                            'partial' => 'bg-amber-500',
                                            'warn' => 'bg-sky-500',
                                            default => 'bg-rose-500',
                                        };
                                    @endphp
                                    <li class="border-l-4 {{ $rowAccent }} px-5 py-4 flex flex-col sm:flex-row sm:items-start gap-3 hover:bg-slate-50/70 transition">
                                        <span class="mt-1.5 hidden sm:inline-flex h-2 w-2 shrink-0 rounded-full {{ $dot }}"></span>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ __('talenma.ats_score.findings.'.$finding['id'].'.title') }}
                                                </p>
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 {{ $badge }}">
                                                    {{ __('talenma.ats_score.status.'.$status) }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-sm text-slate-600 leading-relaxed">
                                                {{ __('talenma.ats_score.findings.'.$finding['id'].'.'.$status) }}
                                            </p>
                                        </div>
                                        <div class="shrink-0 rounded-lg bg-slate-50 px-2 py-1 text-xs font-bold tabular-nums text-slate-500 ring-1 ring-slate-100">
                                            {{ $finding['earned'] }}/{{ $finding['max'] }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-emerald-200 bg-gradient-to-b from-emerald-50/50 to-white px-6 py-14 text-center">
                            <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z"/>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-slate-600">{{ __('talenma.ats_score.tab_diagnosis_empty') }}</p>
                            <button
                                type="button"
                                @click="tab = 'upload'"
                                class="mt-5 inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-500 hover:to-teal-500"
                            >
                                {{ __('talenma.ats_score.go_upload_tab') }}
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Tab 3: Optimize --}}
                <div
                    x-show="tab === 'optimize'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="ats-tab-optimize"
                    class="p-6 sm:p-8 lg:p-10 space-y-5"
                >
                    @if ($hasResult && $hasSourceText)
                        <div class="relative overflow-hidden rounded-3xl border border-teal-200/70 bg-gradient-to-br from-teal-50 via-white to-emerald-50 p-6 sm:p-7">
                            <div class="pointer-events-none absolute -left-8 bottom-0 h-32 w-32 rounded-full bg-teal-300/20 blur-2xl"></div>
                            <div class="relative flex flex-col sm:flex-row sm:items-center gap-5">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl font-bold tracking-tight text-slate-900">
                                            {{ __('talenma.ats_score.optimize_title') }}
                                        </h2>
                                        <span class="inline-flex rounded-full bg-emerald-500 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white shadow-sm shadow-emerald-600/30">
                                            {{ __('talenma.ats_score.optimize_free_badge') }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-2xl">
                                        {{ __('talenma.ats_score.optimize_help') }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('talent.ats-score.optimize') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-500 hover:to-teal-500"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5 10.5 20.25 20.25 3.75"/>
                                        </svg>
                                        {{ __('talenma.ats_score.optimize_cta') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($hasOptimized)
                            @php
                                $optColor = $optScore >= 90 ? 'text-emerald-700' : ($optScore >= 70 ? 'text-amber-700' : 'text-rose-700');
                            @endphp
                            <div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-sm">
                                <div class="flex flex-col gap-4 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-teal-50/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-base font-bold text-slate-900">
                                            {{ __('talenma.ats_score.optimized_score_label', ['score' => $optScore]) }}
                                        </p>
                                        <p class="mt-1 text-sm font-medium text-slate-500">
                                            <span class="tabular-nums">{{ $score }}%</span>
                                            <span class="mx-1.5 text-emerald-500">→</span>
                                            <span class="tabular-nums font-bold {{ $optColor }}">{{ $optScore }}%</span>
                                        </p>
                                    </div>
                                    <a
                                        href="{{ route('talent.ats-score.download') }}"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-800 shadow-sm transition hover:bg-emerald-50"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                        </svg>
                                        {{ __('talenma.ats_score.download_optimized') }}
                                    </a>
                                </div>
                                <div class="space-y-4 p-5">
                                    <p class="rounded-xl border border-amber-100 bg-amber-50/80 px-3.5 py-2.5 text-sm font-medium text-amber-900">
                                        {{ __('talenma.ats_score.optimize_placeholder_note') }}
                                    </p>
                                    <label for="ats-optimized-text" class="sr-only">{{ __('talenma.ats_score.optimized_preview') }}</label>
                                    <textarea
                                        id="ats-optimized-text"
                                        readonly
                                        rows="16"
                                        class="w-full rounded-2xl border-slate-200 bg-slate-50/80 text-sm text-slate-800 font-mono leading-relaxed shadow-inner focus:border-emerald-300 focus:ring-emerald-200"
                                    >{{ $optimizedText }}</textarea>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="rounded-3xl border border-dashed border-teal-200 bg-gradient-to-b from-teal-50/50 to-white px-6 py-14 text-center">
                            <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-100 text-teal-700">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-slate-600">{{ __('talenma.ats_score.tab_optimize_empty') }}</p>
                            <button
                                type="button"
                                @click="tab = 'upload'"
                                class="mt-5 inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/20 transition hover:from-emerald-500 hover:to-teal-500"
                            >
                                {{ __('talenma.ats_score.go_upload_tab') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
