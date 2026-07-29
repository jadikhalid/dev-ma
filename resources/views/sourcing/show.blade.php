@php
    $statusTone = match ($recruitment->status) {
        'pending' => 'bg-sky-50 text-sky-800 border-sky-200',
        'in_progress' => 'bg-amber-50 text-amber-800 border-amber-200',
        'completed_successful', 'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'completed_unsuccessful', 'cancelled' => 'bg-rose-50 text-rose-800 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
    $isNamed = $recruitment->isNamed();
    $backRoute = $isStaff
        ? route('admin.recruitment.index')
        : route('sourcing.index');
    $backLabel = $isStaff
        ? __('talenma.recruitment.show_back_admin')
        : __('talenma.recruitment.show_back');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900">{{ __('talenma.recruitment.show_title') }}</h2>
                <p class="text-sm text-gray-500 truncate">{{ $recruitment->displayTitle() }}</p>
                @if ($isStaff)
                    <p class="text-sm text-gray-500">{{ $recruitment->companyDisplayName() }}</p>
                @endif
            </div>
            <span class="inline-flex items-center rounded-md border px-2 py-1 text-xs font-bold uppercase tracking-wider {{ $statusTone }}">
                {{ $recruitment->statusLabel() }}
            </span>
        </div>
        <div class="mt-2">
            <a href="{{ $backRoute }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">
                {{ $backLabel }}
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] gap-5 lg:gap-6 items-start">
            <div class="space-y-5 min-w-0">
                {{-- Contexte --}}
                <section @class([
                    'overflow-hidden rounded-2xl shadow-sm',
                    'border border-violet-100 bg-gradient-to-br from-violet-50/80 via-white to-slate-50 shadow-violet-600/5' => $isNamed,
                    'border border-indigo-100 bg-gradient-to-br from-indigo-50/80 via-white to-slate-50 shadow-indigo-600/5' => ! $isNamed,
                ])>
                    <div @class([
                        'border-b bg-white/60 px-5 py-4 sm:px-6',
                        'border-violet-100/80' => $isNamed,
                        'border-indigo-100/80' => ! $isNamed,
                    ])>
                        <div class="flex items-start gap-3 min-w-0">
                            <span @class([
                                'inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white shadow-sm',
                                'bg-violet-600 shadow-violet-600/20' => $isNamed,
                                'bg-indigo-600 shadow-indigo-600/20' => ! $isNamed,
                            ]) aria-hidden="true">
                                @if ($isNamed)
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p @class([
                                    'text-xs font-semibold uppercase tracking-wide',
                                    'text-violet-700/80' => $isNamed,
                                    'text-indigo-700/80' => ! $isNamed,
                                ])>{{ __('talenma.recruitment.context_label') }}</p>
                                <h3 class="mt-0.5 text-base sm:text-lg font-bold tracking-tight text-slate-900">{{ $recruitment->subject }}</h3>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span @class([
                                        'inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider',
                                        'bg-violet-50 text-violet-800 border-violet-200' => $isNamed,
                                        'bg-indigo-50 text-indigo-800 border-indigo-200' => ! $isNamed,
                                    ])>
                                        {{ $recruitment->modeLabel() }}
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        {{ $recruitment->created_at?->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @if ($isNamed && $recruitment->talent)
                                        <span class="text-xs text-slate-500">
                                            · {{ __('talenma.recruitment.inbox_talent', ['name' => $recruitment->talent->name]) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6">
                        <blockquote @class([
                            'relative overflow-hidden rounded-xl border border-slate-200 px-5 py-5 sm:px-6 sm:py-6 shadow-sm ring-1 ring-slate-900/5',
                            'bg-gradient-to-br from-slate-50 via-white to-violet-50/40' => $isNamed,
                            'bg-gradient-to-br from-slate-50 via-white to-indigo-50/40' => ! $isNamed,
                        ])>
                            <span @class([
                                'absolute left-0 top-0 bottom-0 w-1',
                                'bg-violet-500' => $isNamed,
                                'bg-indigo-500' => ! $isNamed,
                            ]) aria-hidden="true"></span>
                            <span @class([
                                'select-none pointer-events-none absolute top-3 right-4 text-6xl leading-none font-serif',
                                'text-violet-200/80' => $isNamed,
                                'text-indigo-200/80' => ! $isNamed,
                            ]) aria-hidden="true">„</span>
                            <p class="relative text-[0.95rem] sm:text-base text-slate-800 whitespace-pre-line leading-relaxed font-medium">{{ $recruitment->message }}</p>
                        </blockquote>
                    </div>
                </section>

                {{-- Historique --}}
                <section class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm shadow-slate-900/5 space-y-5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold tracking-tight text-slate-900">{{ __('talenma.recruitment.status_panel_title') }}</h3>
                            <p class="text-xs text-slate-500">{{ __('talenma.recruitment.history_subtitle') }}</p>
                        </div>
                    </div>

                    <ol class="relative space-y-0 before:absolute before:left-[1.15rem] before:top-3 before:bottom-3 before:w-px before:bg-slate-200">
                        @forelse ($recruitment->statusEvents as $event)
                            @php
                                $dotClass = match (true) {
                                    $event->event === 'submitted' => 'bg-sky-500',
                                    $event->event === 'comment_updated' => 'bg-indigo-500',
                                    $event->status === 'in_progress' => 'bg-amber-500',
                                    in_array($event->status, ['completed_successful', 'completed'], true) => 'bg-emerald-500',
                                    in_array($event->status, ['completed_unsuccessful', 'cancelled'], true) => 'bg-rose-500',
                                    $event->status === 'pending' => 'bg-indigo-500',
                                    default => 'bg-slate-400',
                                };
                                $rowClass = match (true) {
                                    $event->event === 'submitted' => 'hover:bg-sky-50/80',
                                    $event->event === 'comment_updated' => 'hover:bg-indigo-50/80',
                                    $event->status === 'in_progress' => 'hover:bg-amber-50/80',
                                    in_array($event->status, ['completed_successful', 'completed'], true) => 'hover:bg-emerald-50/80',
                                    in_array($event->status, ['completed_unsuccessful', 'cancelled'], true) => 'hover:bg-rose-50/80',
                                    default => 'hover:bg-slate-50',
                                };
                            @endphp
                            <li class="relative flex gap-3 rounded-xl px-2 py-2.5 transition {{ $rowClass }}">
                                <span class="relative z-[1] mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white ring-4 ring-white">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></span>
                                </span>
                                <div class="min-w-0 pt-0.5">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        {{ $event->created_at?->translatedFormat('d M Y, H:i') }}
                                    </p>
                                    <p class="mt-0.5 text-sm leading-snug text-slate-800">{{ $event->label($isStaff, $recruitment->mode) }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-6 text-center text-sm text-slate-500">
                                {{ __('talenma.recruitment.history_empty') }}
                            </li>
                        @endforelse
                    </ol>

                    @if (filled($recruitment->admin_comment))
                        <div class="rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50 via-white to-slate-50 px-4 py-3.5 text-sm text-indigo-950 shadow-sm shadow-indigo-600/5">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-white" aria-hidden="true">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m4.5 6.75V6.75A2.25 2.25 0 0015.75 4.5H8.25A2.25 2.25 0 006 6.75v12.75l3.375-2.25H15.75a2.25 2.25 0 002.25-2.25z" />
                                    </svg>
                                </span>
                                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">{{ __('talenma.recruitment.company_comment_label') }}</p>
                            </div>
                            <p class="mt-2 whitespace-pre-line leading-relaxed">{{ $recruitment->admin_comment }}</p>
                            @if ($recruitment->statusUpdatedBy || $recruitment->status_updated_at)
                                <p class="mt-2 text-xs text-indigo-700/80">
                                    {{ $recruitment->statusUpdatedBy?->name }}
                                    @if ($recruitment->status_updated_at)
                                        — {{ $recruitment->status_updated_at->translatedFormat('d M Y, H:i') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    @elseif (! $isStaff)
                        <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-500">
                            {{ __('talenma.recruitment.status_no_comment') }}
                        </p>
                    @endif

                    @if ($isStaff)
                        <form
                            method="POST"
                            action="{{ route('admin.recruitment.status', $recruitment) }}"
                            class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/50 p-4"
                        >
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="redirect_to" value="show">

                            <div>
                                <x-input-label for="status-{{ $recruitment->id }}" :value="__('talenma.recruitment.admin_status')" />
                                <select
                                    id="status-{{ $recruitment->id }}"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    required
                                >
                                    @foreach ($statuses as $status)
                                        <option
                                            value="{{ $status }}"
                                            @selected($recruitment->normalizeStatus() === $status)
                                        >
                                            {{ __('talenma.recruitment.status_'.$status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="comment-{{ $recruitment->id }}" :value="__('talenma.recruitment.admin_comment')" />
                                <textarea
                                    id="comment-{{ $recruitment->id }}"
                                    name="admin_comment"
                                    rows="3"
                                    maxlength="2000"
                                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                    placeholder="{{ __('talenma.recruitment.admin_comment_placeholder') }}"
                                >{{ old('admin_comment', $recruitment->admin_comment) }}</textarea>
                            </div>

                            <x-primary-button>
                                {{ __('talenma.recruitment.admin_save') }}
                            </x-primary-button>
                        </form>
                    @endif
                </section>
            </div>

            <div class="min-w-0">
                @include('sourcing._chat', ['recruitment' => $recruitment, 'sidebar' => true])
            </div>
        </div>
    </div>
</x-app-layout>
