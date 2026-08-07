@props([
    'topic',
])

@php
    $user = Auth::user();
    $audience = null;

    if ($user?->isAdmin() || $user?->isActingAsModerator()) {
        $audience = 'admin';
    } elseif ($user?->isCompany()) {
        $audience = 'company';
    } elseif ($user?->isTalent()) {
        $audience = 'talent';
    }

    $help = $audience
        ? __('talenma.help.'.$audience.'.'.$topic)
        : null;

    $isValidHelp = is_array($help) && filled($help['title'] ?? null);
    $steps = $isValidHelp && is_array($help['steps'] ?? null) ? $help['steps'] : [];
    $tips = $isValidHelp && is_array($help['tips'] ?? null) ? $help['tips'] : [];
    $drawerId = 'process-help-'.($audience ?? 'guest').'-'.$topic;
@endphp

@if ($isValidHelp)
<div
    {{ $attributes->class('fixed bottom-5 right-4 z-40 sm:bottom-6 sm:right-6') }}
    x-data="{
        open: false,
        show() {
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        hide() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        },
    }"
    @keydown.escape.window="open && hide()"
>
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/25 transition hover:bg-indigo-700 hover:shadow-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
        @click="show()"
        :aria-expanded="open.toString()"
        aria-controls="{{ $drawerId }}"
    >
        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15 text-white" aria-hidden="true">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" d="M12 16v-4" />
                <circle cx="12" cy="8" r="0.75" fill="currentColor" stroke="none" />
            </svg>
        </span>
        <span>{{ __('talenma.help.label') }}</span>
    </button>

    <div
        id="{{ $drawerId }}"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[60]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $drawerId }}-title"
    >
        <div
            x-show="open"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-gray-900/40"
            @click="hide()"
        ></div>

        <div
            x-show="open"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col bg-white shadow-2xl"
            @click.stop
        >
            <div class="relative shrink-0 border-b border-gray-100 px-5 py-5 sm:px-6">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-indigo-400 to-sky-400"></div>
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-sm shadow-indigo-200">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path stroke-linecap="round" d="M12 16v-4" />
                                <circle cx="12" cy="8" r="0.75" fill="currentColor" stroke="none" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ __('talenma.help.label') }}</p>
                            <h2 id="{{ $drawerId }}-title" class="mt-1 text-lg font-bold text-gray-900">{{ $help['title'] }}</h2>
                            @if (filled($help['summary'] ?? null))
                                <p class="mt-2 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $help['summary'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="hide()"
                        class="shrink-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        aria-label="{{ __('talenma.help.close') }}"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-5 sm:px-6">
                @if (filled($help['intro'] ?? null))
                    <p class="text-sm leading-relaxed text-gray-600">{{ $help['intro'] }}</p>
                @endif

                @foreach ($steps as $index => $step)
                    @continue(! is_array($step) || blank($step['title'] ?? null))
                    <div class="rounded-2xl border border-gray-100 bg-slate-50/80 p-4 sm:p-5">
                        <div class="flex gap-3">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-xs font-bold text-indigo-700 shadow-sm ring-1 ring-indigo-100">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $step['title'] }}</h3>
                                @if (filled($step['body'] ?? null))
                                    <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $step['body'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($tips !== [])
                    <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-4 sm:p-5">
                        <h3 class="text-sm font-semibold text-amber-950">{{ $help['tips_title'] ?? __('talenma.help.tips_title') }}</h3>
                        <ul class="mt-2 space-y-1.5">
                            @foreach ($tips as $tip)
                                @continue(blank($tip))
                                <li class="flex gap-2 text-sm leading-relaxed text-amber-900/90">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-amber-500" aria-hidden="true"></span>
                                    <span>{{ $tip }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="shrink-0 border-t border-gray-100 px-5 py-4 sm:px-6">
                <x-primary-button type="button" class="w-full justify-center" @click="hide()">
                    {{ __('talenma.help.close') }}
                </x-primary-button>
            </div>
        </div>
    </div>
</div>
@endif
